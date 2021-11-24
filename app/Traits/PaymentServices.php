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


    private function filterPayments( $params, $take, $skip)
    {
        $params = $this->checkUndefinedProperty($params, $this->paymentFilterCols());
     
        $query = Payment::query();
        $query->orderBy('sequence', 'DESC');
        if($params->tenant_id){
            $tenant_id = $params->tenant_id;
            $query->whereHas('roomcontract', function($q) use($tenant_id) {
                    $q->whereHas('tenant', function($q1) use($tenant_id) {
                        $q1->where('id', $tenant_id);
                });
            });
        }

        if($params->room_id){
            $room_id = $params->room_id;
            $query->whereHas('roomcontract', function($q) use($room_id) {
                    $q->whereHas('room', function($q1) use($room_id) {
                        $q1->where('id', $room_id);
                });
            });
        }

        if ($params->sequence) {
            $sequence = $params->sequence;
            $query->where('sequence', $sequence);
        }

        if ($params->fromdate) {
            $date = Carbon::parse($params->fromdate);
            $query->whereDate('paymentdate', '>=', $date );
        }
        
        if ($params->todate) {
            $date = Carbon::parse($params->todate)->endOfDay();
            $query->whereDate('paymentdate', '>=', $date );
        }

        if($params->service_ids){
            $service_ids = collect($params->service_ids);
            error_log($service_ids);
            $query->whereHas('services', function($q) use($service_ids) {
                $q->whereIn('services.id', $service_ids);
            });
        }
        
        if (property_exists($params, 'paid') && $params->paid != null) {
            $paid = $params->paid;
            $query->where('paid' , $params->paid);
        }

        if($params->otherPaymentTitle){
            $keyword = $params->otherPaymentTitle;
            $query->whereHas('otherpayments', function($q) use($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }

        if ($params->paymentmethod) {
            $paymentmethod = $params->paymentmethod;
            $query->where('paid' , $params->paid);
        }
    
        $total = $query->count();
        if($skip){
            $query->skip($skip);
        }
        if($take){
            $query->take($take);
        }else{
            $query->take(10);
        }
        if (property_exists($params, 'status') && $params->status != null) {   
            $data = $query->where('status', $params->status)->with(['roomcontract' => function ($q) {
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
            $data = $query->where('status', true)->with(['roomcontract' => function ($q) {
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
            }, 'issueby'])->get()->unique('id')->flatten(1);;
        }

        $result['data'] = $data;
        $result['total'] = $total;

        return  $result;
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
