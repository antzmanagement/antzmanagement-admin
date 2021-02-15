<?php

namespace App\Traits;

use App\Payment;
use Carbon\Carbon;

trait PaymentServices
{


    private function getPayments($requester)
    {

        $data = collect();

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
        }])->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

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
            });
        }

        if($params->room_id){
            $room_id = $params->room_id;
            $data = $data->filter(function ($item) use($room_id) {
                if($item->roomcontract){
                    if($item->roomcontract->room)
                    return $item->roomcontract->room->id == $room_id;
                }
                return false;
            });
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
            });
        }
        
        if ($params->todate) {
            $date = Carbon::parse($params->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'paymentdate'))->lte($date);
            });
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
            });
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
        }])->where('status', true)->first();
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
        }])->where('status', true)->first();
        return $data;
    }

    private function createPayment($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->paymentAllCols());

        $data = new Payment();
        $data->uid = Carbon::now()->timestamp . Payment::count();
        $data->price = $this->toDouble($params->price);
        $data->other_charges = $this->toDouble($params->other_charges);
        $data->totalpayment = $data->price + $data->other_charges;
        $data->paymentdate = $this->toDate($params->paymentdate);
        $data->sequence = $this->toInt($params->sequence);
        $data->remark = $params->remark;
        $data->sequence = Payment::where('status', true)->count() + 1;
        
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
        $data->payment = $this->toDouble($params->payment);
        $data->penalty =  $this->toDouble($params->penalty);
        $data->processing_fees =  $this->toDouble($params->processing_fees);
        $data->service_fees =  $this->toDouble($params->service_fees);
        $data->outstanding = $this->toDouble($data->price + $data->penalty  - $data->payment);
        $data->paid = $params->paid;
        $data->rentaldate = $this->toDate($params->rentaldate);
        $data->paymentdate = $this->toDate($params->paymentdate);
        $data->remark = $params->remark;
        
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

    private function deletePayment($data)
    {
        try {
            $data->services()->sync([]);
            $data->delete();

        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }


        return $data;
    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function paymentAllCols()
    {

        return ['id', 'uid', 'price', 'remark'];
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
        return ['fromdate', 'todate', 'tenant_id', 'room_id', 'sequence', 'service_ids'];
    }
}
