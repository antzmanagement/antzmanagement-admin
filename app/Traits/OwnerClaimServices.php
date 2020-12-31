<?php

namespace App\Traits;

use App\Claim;
use Carbon\Carbon;

trait OwnerClaimServices
{



    private function getClaims($requester)
    {

        $data = collect();

        $data = Claim::where('status', true)->with(['rentalpayments' => function ($q) {
            $q->where('status', true);
        },'maintenances' => function ($q) {
            $q->where('status', true);
        },'owner' => function ($q) {
            $q->where('status', true);
        }])->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterClaims($data, $params)
    {
        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params, $this->claimFilterCols());

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

    private function getClaim($uid)
    {

        $data = Claim::where('uid', $uid)->with(['rentalpayments' => function ($q) {
            $q->where('status', true);
        },'maintenances' => function ($q) {
            $q->where('status', true);
        },'owner' => function ($q) {
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function getClaimById($id)
    {
        $data = Claim::where('id', $id)->with(['rentalpayments' => function ($q) {
            $q->where('status', true);
        },'maintenances' => function ($q) {
            $q->where('status', true);
        },'owner' => function ($q) {
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function createClaim($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->claimAllCols());

        $data = new Claim();
        $data->uid = Carbon::now()->timestamp . Claim::count();
        $data->maintenance_fees = $this->toDouble($params->maintenance_fees);
        $data->admin_fees = $this->toDouble($params->admin_fees);
        $data->other_fees = $this->toDouble($params->other_fees);
        $data->rental_fees = $this->toDouble($params->rental_fees);
        $data->remark = $params->remark;

        $owner = $this->getOwner($params->owner_id);
        if ($this->isEmpty($owner)) {
            return null;
        }
        $data->owner()->associate($owner);
        
        if (!$this->saveModel($data)) {
            return null;
        }

        if($params->rentalpayments){
            foreach ($params->rentalpayments as $id) {
                $rentalpayment = $this->getRentalPaymentById($id);
                if ($this->isEmpty($rentalpayment)) {
                    return null;
                }

                $rentalpayment->isClaimed = true;
                $rentalpayment->claim()->associate($data);
                if (!$this->saveModel($rentalpayment)) {
                    return null;
                }
            }
        }

        if($params->maintenances){
            foreach ($params->maintenances as $id) {
                $maintenance = $this->getMaintenanceById($id);
                if ($this->isEmpty($maintenance)) {
                    return null;
                }

                $maintenance->isClaimed = true;
                $maintenance->claim()->associate($data);
                if (!$this->saveModel($maintenance)) {
                    return null;
                }
            }
        }

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Claim is not empty when calling this function
    private function updateClaim($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->claimAllCols());
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

    private function deleteClaim($data)
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
    public function claimAllCols()
    {

        return ['id', 'uid', 'price', 'remark'];
    }

    public function claimDefaultCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }


    public function claimListingCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }
    public function claimFilterCols()
    {
        return ['keyword'];
    }
}
