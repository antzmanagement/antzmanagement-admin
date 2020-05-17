<?php

namespace App\Traits;

use App\RentalPayment;
use Carbon\Carbon;
use App\Traits\AllServices;

trait RentalPaymentServices
{

    use AllServices;


    private function getRentalPayments($requester)
    {

        $data = collect();

        $data = RentalPayment::where('status', true)->get();

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
                if (stristr($item->uid, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            });
        }
        if ($params->rooms) {
            error_log('Filtering rentalPayments with rooms....');
            $rooms = collect($params->rooms);
            $data = $data->filter(function ($item) use ($rooms) {

                if (!$rooms->contains($item->room->id)) {
                    return false;
                }
                return true;
            })->values();
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getRentalPayment($uid)
    {

        $data = RentalPayment::where('uid', $uid)->with(['room', 'tenant', 'contract'])->where('status', true)->first();
        return $data;
    }

    private function getRentalPaymentById($id)
    {
        $data = RentalPayment::where('id', $id)->with(['room', 'tenant', 'contract'])->where('status', true)->first();
        return $data;
    }

    private function createRentalPayment($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->rentalPaymentAllCols());

        $data = new RentalPayment();
        $data->uid = Carbon::now()->timestamp . RentalPayment::count();
        $data->price = $this->toDouble($params->price);
        $data->payment = 0;
        $data->outstanding = $this->toDouble($data->price - $data->payment);
        $data->paid = false;
        error_log($params->rentaldate);
        $data->rentaldate = $this->toDate($params->rentaldate);
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
        $data->uid = Carbon::now()->timestamp . RentalPayment::count();
        $data->price = $this->toDouble($params->price);
        $data->payment = 0;
        $data->outstanding = $this->toDouble($data->price - $data->payment);
        $data->paid = false;
        $data->rentaldate = $this->toDate($params->rentaldate);
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
