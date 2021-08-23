<?php

namespace App\Traits;

use App\Payment;
use Carbon\Carbon;

trait PaymentServices
{


    private function getPayments($requester, $params = null)
    {

        $params = $this->checkUndefinedProperty($params, $this->paymentFilterCols());
        $data = collect();

        if (property_exists($params, 'status') && $params->status != null) {   
            error_log('==========');
            error_log($params->status);
            error_log($params->status ? 'Active' : 'Inactive');
            $data = Payment::where('status', $params->status)->with(['roomcontract' => function ($q) {
                // Query the name field in status table
                $q->with(['tenant' => function ($q1) {
                    // Query the name field in status table
                    $q1->where('status', true);
                }]);
                $q->with(['room' => function ($q1) {
                    // Query the name field in status table
                    $q1->where('status', true);
                }]);
                $q->where('status', true);
            }, 'services' => function ($q) {
            },'otherpayments' => function ($q) {
            }, 'issueby', 'deletedby'])->get();
        }else{
            $data = Payment::where('status', true)->with(['roomcontract' => function ($q) {
                // Query the name field in status table
                $q->with(['tenant' => function ($q1) {
                    // Query the name field in status table
                    $q1->where('status', true);
                }]);
                $q->with(['room' => function ($q1) {
                    // Query the name field in status table
                    $q1->where('status', true);
                }]);
                $q->where('status', true);
            }, 'services' => function ($q) {
            },'otherpayments' => function ($q) {
            }, 'issueby'])->get();
        }

     

        $data = $data->unique('id')->sortByDesc('id')->flatten(1);
        return $data;
    }


    private function filterPayments($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->paymentFilterCols());
     
        if($params->tenant_id){
            $tenant_id = $params->tenant_id;
            $data = $data->filter(function ($item) use($tenant_id) {
                if($item->roomcontract){
                    if($item->roomcontract->tenant)
                    return $item->roomcontract->tenant->id == $tenant_id;
                }
                return false;
            })->values();
        }

        if($params->room_id){
            $room_id = $params->room_id;
            $data = $data->filter(function ($item) use($room_id) {
                if($item->roomcontract){
                    if($item->roomcontract->room)
                    return $item->roomcontract->room->id == $room_id;
                }
                return false;
            })->values();
        }

        if ($params->sequence) {
            $sequence = $params->sequence;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($sequence) {
                return $item->sequence == $sequence;
            })->values();
        }

        if ($params->fromdate) {
            $date = Carbon::parse($params->fromdate);
            $data = collect($data);
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'paymentdate'))->gte($date);
            })->values();
        }
        
        if ($params->todate) {
            $date = Carbon::parse($params->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'paymentdate'))->lte($date);
            })->values();
        }

        if($params->service_ids){
            $service_ids = collect($params->service_ids);
            error_log($service_ids);
            $data = $data->filter(function ($item) use($service_ids) {
                $existed = false;
                if($item->services){
                    error_log($item->services->pluck('id'));
                    foreach ($item->services as $service) {
                        if($service_ids->contains($service->id)){
                            $existed = true;
                            break;
                        }
                    }
                }
                error_log($existed. ' ');
                return $existed;
            })->values();
        }
        
        if (property_exists($params, 'paid') && $params->paid != null) {
            error_log('$check paid');
            $paid = $params->paid;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($paid) {
                return $item->paid == $paid;
            })->values();
        }

        if($params->otherPaymentTitle){
            $keyword = $params->otherPaymentTitle;
            $data = $data->filter(function ($item) use($keyword) {
                $otherpayments = collect($item->otherpayments);
                $existed = false;
                foreach($otherpayments as $otherpayment){
                    if ( stristr($otherpayment->name, $keyword) == TRUE) {
                        $existed = true;
                        break;
                    }
                }
                return $existed;
            })->values();
        }

        if ($params->paymentmethod) {
            $paymentmethod = $params->paymentmethod;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($paymentmethod) {
                return $item->paymentmethod == $paymentmethod;
            })->values();
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getPayment($uid)
    {

        $data = Payment::where('uid', $uid)->with(['roomcontract' => function ($q) {
            // Query the name field in status table
            $q->with(['tenant' => function ($q1) {
                // Query the name field in status table
                $q1->where('status', true);
            }]);
            $q->with(['room' => function ($q1) {
                // Query the name field in status table
                $q1->where('status', true);
            }]);
            $q->where('status', true);
        }, 'services' => function ($q) {
        },'otherpayments' => function ($q) {
        }, 'issueby'])->where('status', true)->first();
        return $data;
    }

    private function getPaymentById($id)
    {
        $data = Payment::where('id', $id)->with(['roomcontract' => function ($q) {
            // Query the name field in status table
            $q->with(['tenant' => function ($q1) {
                // Query the name field in status table
                $q1->where('status', true);
            }]);
            $q->with(['room' => function ($q1) {
                // Query the name field in status table
                $q1->where('status', true);
            }]);
            $q->where('status', true);
        }, 'services' => function ($q) {
        },'otherpayments' => function ($q) {
        }, 'issueby'])->where('status', true)->first();
        return $data;
    }

    private function createPayment($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->paymentAllCols());

        $data = new Payment();
        $data->uid = Carbon::now()->timestamp . Payment::count();
        $data->price = $this->toDouble($params->price);
        $data->other_charges = $this->toDouble($params->other_charges);
        $data->processing_fees = $this->toDouble($params->processing_fees);
        $data->totalpayment = $this->toDouble($params->totalpayment);
        $data->outstanding = $data->price + $data->other_charges + $data->processing_fees - $data->totalpayment;
        $data->paymentdate = $this->toDate($params->paymentdate);
        if($params->sequence){
            $data->sequence = $this->toInt($params->sequence);
            $data->receiptno = 'ap-'. $data->sequence;
        }
        $data->remark = $params->remark;
        $data->referenceno = $params->referenceno;
        
        $roomContract = $this->getRoomContractById($params->room_contract_id);
        if ($this->isEmpty($roomContract)) {
            return false;
        }
        $data->roomcontract()->associate($roomContract);

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Payment is not empty when calling this function
    private function updatePayment($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->paymentAllCols());
        $data->price = $this->toDouble($params->price);
        $data->other_charges = $this->toDouble($params->other_charges);
        $data->processing_fees = $this->toDouble($params->processing_fees);
        $data->totalpayment = $this->toDouble($params->totalpayment);
        $data->outstanding = $data->price + $data->other_charges + $data->processing_fees - $data->totalpayment;
        $data->paid = $params->paid;
        $data->remark = $params->remark;
        if($params->sequence){
            $data->sequence = $this->toInt($params->sequence);
            $data->receiptno = 'ap-'. $data->sequence;
        }
        
        $roomContract = $this->getRoomContractById($params->room_contract_id);
        if ($this->isEmpty($roomContract)) {
            return false;
        }
        $data->roomcontract()->associate($roomContract);

        if($params->paid){
            
            $data->receive_from = $params->receive_from;
            $data->paymentmethod = $params->paymentmethod;
            $data->referenceno = $params->referenceno;
            $data->paymentdate = $this->toDate($params->paymentdate);
            if($params->issueby){
                $issueBy = $this->getUserById($params->issueby);
                if ($this->isEmpty($issueBy)) {
                    return false;
                }
                $data->issueby()->associate($issueBy);
            }
        }

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deletePayment($data, $user_id = null)
    {
        $data->status = false;

        if($user_id){
            $data->deletedby = $user_id;
        }
        if ($this->saveModel($data)) {
            return $data->refresh();
        } else {
            return null;
        }

        return $data->refresh();

    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function paymentAllCols()
    {

        return ['id', 'uid', 'price', 'remark', 'referenceno' ,'receive_from', 'issueby', 'sequence', 'paymentdate', 'other_charges', 'room_contract_id', 'paymentmethod', 'processing_fees', 'totalpayment'];
    }

    public function paymentDefaultCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }


    public function paymentListingCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }
    public function paymentFilterCols()
    {
        return ['fromdate', 'todate', 'tenant_id', 'room_id', 'sequence', 'service_ids', 'otherPaymentTitle', 'paymentmethod', 'status'];
    }
}
