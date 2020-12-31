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
        }])->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterRentalPayments($data, $params)
    {
        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params, $this->rentalPaymentFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->roomcontract->tenant->name, $keyword) == TRUE || stristr($item->roomcontract->room->name, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        if (property_exists($params, 'paid') && $params->paid != 'all') {
            $paid = $params->paid;
            $data = $data->filter(function ($item) use ($paid) {
                return $item->paid == $paid;
            })->values();
        }


        $data = $data->unique('id');

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
        }])->where('status', true)->first();
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
        }])->where('status', true)->first();
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

        return ['id', 'uid', 'price', 'remark'];
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
        return ['keyword'];
    }
}
