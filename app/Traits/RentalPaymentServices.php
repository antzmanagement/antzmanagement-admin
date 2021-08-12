<?php

namespace App\Traits;

use App\RentalPayment;
use Carbon\Carbon;

trait RentalPaymentServices
{


    private function getRentalPayments($requester)
    {

        $data = collect();

        $data = RentalPayment::where('status', true)->with(['roomcontract' => function ($q) {
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
        }, 'issueby'])->get();

        $data = $data->unique('id')->sortByDesc('sequence')->flatten(1);

        return $data;
    }


    private function filterRentalPayments($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->rentalPaymentFilterCols());
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


        if (property_exists($params, 'penalty') && $params->penalty != null) {
            $penalty = $params->penalty;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($penalty) {
                return $penalty ? $item->penalty > 0 : $item->penalty == 0;
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

        if ($params->fromdate) {
            $date = Carbon::parse($params->fromdate);
            $data = collect($data);
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'rentaldate'))->gte($date);
            })->values();
        }
        
        if ($params->todate) {
            $date = Carbon::parse($params->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'rentaldate'))->lte($date);
            })->values();
        }

        if ($params->paymentfromdate) {
            error_log('$check paymentfromdate');
            $date = Carbon::parse($params->paymentfromdate);
            $data = collect($data);
            $data = $data->filter(function ($item) use ($date) {
                if(data_get($item, 'paymentdate')){
                    return Carbon::parse(data_get($item, 'paymentdate'))->gte($date);
                }else{
                    return false;
                }
            })->values();
        }
        
        if ($params->paymenttodate) {
            $date = Carbon::parse($params->paymenttodate);
            $data = $data->filter(function ($item) use ($date) {
                error_log(data_get($item, 'paymentdate'));
                if(data_get($item, 'paymentdate')){
                    return Carbon::parse(data_get($item, 'paymentdate'))->lte($date);
                }else{
                    return false;
                }
            })->values();
        }
        
        if ($params->paymentmethod) {
            $paymentmethod = $params->paymentmethod;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($paymentmethod) {
                return $item->paymentmethod == $paymentmethod;
            })->values();
        }
        
        $data = $data->unique('id')->sortByDesc('sequence')->flatten(1);

        return $data;
    }

    private function getRentalPayment($uid)
    {

        $data = RentalPayment::where('uid', $uid)->with(['roomcontract' => function ($q) {
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
        }, 'issueby'])->where('status', true)->first();
        return $data;
    }

    private function getRentalPaymentById($id)
    {
        $data = RentalPayment::where('id', $id)->with(['roomcontract' => function ($q) {
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
        }, 'issueby'])->where('status', true)->first();
        return $data;
    }

    private function createRentalPayment($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->rentalPaymentAllCols());

        $data = new RentalPayment();
        $data->uid = Carbon::now()->timestamp . RentalPayment::count();
        $data->price = $this->toDouble($params->price);
        $data->payment = 0;
        $data->penalty = 0;
        $data->outstanding = $this->toDouble($data->price + $data->penalty - $data->payment);
        $data->paid = false;
        $data->rentaldate = $this->toDate($params->rentaldate);
        $data->paymentdate = null;
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

    //Make Sure RentalPayment is not empty when calling this function
    private function updateRentalPayment($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->rentalPaymentAllCols());
        $data->price = $this->toDouble($params->price);
        $data->payment = $this->toDouble($params->payment);
        $data->penalty =  $this->toDouble($params->penalty);
        $data->processing_fees =  $this->toDouble($params->processing_fees);
        $data->service_fees =  $this->toDouble($params->service_fees);
        $data->outstanding = $this->toDouble($data->price + $data->penalty  - $data->payment);
        $data->paid = $params->paid;
        $data->rentaldate = $this->toDate($params->rentaldate);
        $data->sequence = $this->toInt($params->sequence);
        $data->remark = $params->remark;
        
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
            $issueBy = $this->getUserById($params->issueby);
            if ($this->isEmpty($issueBy)) {
                return false;
            }
            $data->issueby()->associate($issueBy);
        }

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteRentalPayment($data)
    {

        $data->status = false;
        if ($this->saveModel($data)) {
            return $data->refresh();
        } else {
            return null;
        }

        return $data->refresh();
    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function rentalPaymentAllCols()
    {

        return ['id', 'uid', 'price', 'remark', 'referenceno'];
    }

    public function rentalPaymentDefaultCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }


    public function rentalPaymentListingCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }
    public function rentalPaymentFilterCols()
    {
        return ['fromdate', 'todate', 'tenant_id', 'room_id', 'penalty', 'paid', 'sequence', 'paymentfromdate', 'paymenttodate', 'paymentmethod', 'receive_from', 'paymentmethod'];
    }
}
