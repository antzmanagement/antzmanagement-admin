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

            $q->with(['roomcontract' => function ($q1) {
            // Query the name field in status table
                $q1->with(['tenant' => function ($q2) {
                    // Query the name field in status table
                    $q2->where('status', true);
                }]);
                $q1->with(['room' => function ($q2) {
                    // Query the name field in status table
                    $q2->where('status', true);
                }]);
            }]);
        },'maintenances' => function ($q) {
            $q->where('status', true);
        },'owner' => function ($q) {
            $q->where('status', true);
        }])->get();

        $data = $data->unique('id')->sortByDesc('id')->flatten(1);

        return $data;
    }


    private function filterClaims($data, $params)
    {
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

            $q->with(['roomcontract' => function ($q1) {
            // Query the name field in status table
                $q1->with(['tenant' => function ($q2) {
                    // Query the name field in status table
                    $q2->where('status', true);
                }]);
                $q1->with(['room' => function ($q2) {
                    // Query the name field in status table
                    $q2->where('status', true);
                }]);
            }]);
        },'maintenances' => function ($q) {
            $q->where('status', true);  
            $q->with('property');
            $q->with('room');
        },'owner' => function ($q) {
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function getClaimById($id)
    {
        $data = Claim::where('id', $id)->with(['rentalpayments' => function ($q) {
            $q->where('status', true);

            $q->with(['roomcontract' => function ($q1) {
            // Query the name field in status table
                $q1->with(['tenant' => function ($q2) {
                    // Query the name field in status table
                    $q2->where('status', true);
                }]);
                $q1->with(['room' => function ($q2) {
                    // Query the name field in status table
                    $q2->where('status', true);
                }]);
            }]);
        },'maintenances' => function ($q) {
            $q->where('status', true);
            $q->with('property');
            $q->with('room');
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
        $data->admin_fees = $this->toDouble($params->admin_fees);
        $data->other_fees = $this->toDouble($params->other_fees);
        $data->remark = $params->remark;

        $owner = $this->getOwner($params->owner_id);
        if ($this->isEmpty($owner)) {
            return null;
        }
        $data->owner()->associate($owner);
        
        $total = 0;
        $total += $data->other_fees;
        $total += $data->admin_fees;

        if (!$this->saveModel($data)) {
            return null;
        }
        $data = $data->refresh();
        if($params->rentalpayments){
            foreach ($params->rentalpayments as $item) {
                $rentalpayment = $this->getRentalPaymentById($item->id);
                if ($this->isEmpty($rentalpayment)) {
                    return null;
                }

                $rentalpayment->isClaimed = true;
                $rentalpayment->claim_amount = $this->toDouble($item->claim_amount);
                $total += $rentalpayment->claim_amount;
                $rentalpayment->claim()->associate($data);
                if (!$this->saveModel($rentalpayment)) {
                    return null;
                }
            }
        }

        if($params->maintenances){
            foreach ($params->maintenances as $item) {
                $maintenance = $this->getMaintenanceById($item->id);
                if ($this->isEmpty($maintenance)) {
                    return null;
                }

                $maintenance->isClaimed = true;
                $maintenance->claim_amount = $this->toDouble($item->claim_amount);
                $total += $maintenance->claim_amount;
                $maintenance->claim()->associate($data);
                if (!$this->saveModel($maintenance)) {
                    return null;
                }
            }
        }

        $data->totalamount = $total;

        return $data->refresh();
    }

    //Make Sure Claim is not empty when calling this function
    private function updateClaim($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->claimAllCols());
        $data->admin_fees = $this->toDouble($params->admin_fees);
        $data->other_fees = $this->toDouble($params->other_fees);
        $data->remark = $params->remark;

        $owner = $this->getOwner($params->owner_id);
        if ($this->isEmpty($owner)) {
            return null;
        }
        $data->owner()->associate($owner);
        
        $total = 0;
        $total += $data->other_fees;
        $total += $data->admin_fees;

        if($params->rentalpayments){
            foreach ($params->rentalpayments as $item) {
                $rentalpayment = $this->getRentalPaymentById($item->id);
                if ($this->isEmpty($rentalpayment)) {
                    return null;
                }

                $rentalpayment->isClaimed = true;
                $rentalpayment->claim_amount = $this->toDouble($item->claim_amount);
                $total += $rentalpayment->claim_amount;
                $rentalpayment->claim()->associate($data);
                if (!$this->saveModel($rentalpayment)) {
                    return null;
                }
            }
        }

        if($params->maintenances){
            foreach ($params->maintenances as $item) {
                $maintenance = $this->getMaintenanceById($item->id);
                if ($this->isEmpty($maintenance)) {
                    return null;
                }

                $maintenance->isClaimed = true;
                $maintenance->claim_amount = $this->toDouble($item->claim_amount);
                $total += $maintenance->claim_amount;
                $maintenance->claim()->associate($data);
                if (!$this->saveModel($maintenance)) {
                    return null;
                }
            }
        }

        $data->totalamount = $total;
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
