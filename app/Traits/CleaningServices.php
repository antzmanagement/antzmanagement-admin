<?php

namespace App\Traits;

use App\Cleaning;
use Carbon\Carbon;
use OpenApi\Annotations\Property;

trait CleaningServices
{


    private function getCleanings($requester)
    {

        $data = collect();

        $data = Cleaning::where('status', true)->with(['room' => function ($q) {
            // Query the name field in status table
            $q->with(['roomtypes' => function ($q1) {
                // Query the name field in status table
                $q1->wherePivot('status', true);
            }]);
            $q->where('status', true);
        }, 'owner' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'tenant' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'roomcheck' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }])->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterCleanings($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->cleaningFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->uid, $keyword) == TRUE || stristr($item->name, $keyword) == TRUE  ) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getCleaning($uid)
    {

        $data = Cleaning::where('uid', $uid)->with(['room' => function ($q) {
            // Query the name field in status table
            $q->with(['roomtypes' => function ($q1) {
                // Query the name field in status table
                $q1->wherePivot('status', true);
            }]);
            $q->where('status', true);
        }, 'owner' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'tenant' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'roomcheck' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function getCleaningById($id)
    {
        $data = Cleaning::where('id', $id)->with(['room' => function ($q) {
            // Query the name field in status table
            $q->with(['roomtypes' => function ($q1) {
                // Query the name field in status table
                $q1->wherePivot('status', true);
            }]);
            $q->where('status', true);
        }, 'owner' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'tenant' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'roomcheck' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function createCleaning($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->cleaningAllCols());

        $data = new Cleaning();
        $data->uid = Carbon::now()->timestamp . Cleaning::count();
        $data->price  = $this->toDouble($params->price);
        $data->remark = $params->remark;
        $data->cleaning_type = $params->cleaning_type;
        $data->cleaning_status = $params->cleaning_status;
        $data->cleaning_date = $this->toDate($params->cleaning_date);
        $data->claim_by_owner = $params->claim_by_owner;
        $data->claim_by_tenant = $params->claim_by_tenant;
        

        $room = $this->getRoomById($params->room_id);
        if ($this->isEmpty($room)) {
            return false;
        }
        $data->room()->associate($room);

        if ($params->claim_by_owner) {
            if($params->owner_id){
                $owner = $this->getOwnerById($params->owner_id);
                if ($this->isEmpty($owner)) {
                    return null;
                }
            }else{
                return null;
            }
            $data->owner()->associate($owner);
        }else{
            $data->owner_id = null;
        }

        if ($params->claim_by_tenant) {
            if($params->tenant_id){
                $tenant = $this->getTenantById($params->tenant_id);
                if ($this->isEmpty($tenant)) {
                    return null;
                }
            }else{
                return null;
            }
            $data->tenant()->associate($tenant);
        }else{
            $data->tenant_id = null;
        }

        if ($params->room_check_id) {
            $roomCheck = $this->getRoomCheckById($params->room_check_id);
            if ($this->isEmpty($roomCheck)) {
                return null;
            }
            $data->roomcheck()->associate($roomCheck);
        }

        
        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Cleaning is not empty when calling this function
    private function updateCleaning($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->cleaningAllCols());
        $data->price  = $this->toDouble($params->price);
        $data->remark = $params->remark;
        $data->cleaning_type = $params->cleaning_type;
        $data->cleaning_status = $params->cleaning_status;
        $data->cleaning_date = $this->toDate($params->cleaning_date);
        $data->claim_by_owner = $params->claim_by_owner;
        $data->claim_by_tenant = $params->claim_by_tenant;
        $data->paid = $params->paid == true;

        $room = $this->getRoomById($params->room_id);
        if ($this->isEmpty($room)) {
            return false;
        }
        $data->room()->associate($room);

        if ($params->claim_by_owner) {
            if($params->owner_id){
                $owner = $this->getOwnerById($params->owner_id);
                if ($this->isEmpty($owner)) {
                    return null;
                }
            }else{
                return null;
            }
            $data->owner()->associate($owner);
        }else{
            $data->owner_id = null;
        }

        if ($params->claim_by_tenant) {
            if($params->tenant_id){
                $tenant = $this->getTenantById($params->tenant_id);
                if ($this->isEmpty($tenant)) {
                    return null;
                }
            }else{
                return null;
            }
            $data->tenant()->associate($tenant);
        }else{
            $data->tenant_id = null;
        }

        if ($params->room_check_id) {
            $roomCheck = $this->getRoomCheckById($params->room_check_id);
            if ($this->isEmpty($roomCheck)) {
                return null;
            }
            $data->roomcheck()->associate($roomCheck);
        }else{
            $data->room_check_id = null;
        }

        if($data->paid){
            $data->payment = $this->toDouble($params->price);
            $data->processing_fees =  $this->toDouble($params->processing_fees);
            $data->paymentmethod = $params->paymentmethod;
            $data->receiptno = $params->receiptno;
            $data->sequence = $this->toInt($params->sequence);
            $data->referenceno = $params->referenceno;
            $data->receive_from = $params->receive_from;

            $issueBy = $this->getUserById($params->issue_by);
            if ($this->isEmpty($issueBy)) {
                $data->issue_by = null;
            }else{
                $data->issueby()->associate($issueBy);
            }
        }else{
            $data->payment = 0;
            $data->processing_fees =  0;
            $data->paymentmethod = null;
            $data->receiptno = null;
            $data->sequence = null;
            $data->referenceno = null;
            $data->receive_from = null;
            $data->issue_by = null;
        }

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteCleaning($data)
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
    public function cleaningAllCols()
    {

        return ['id', 'uid', 'price', 'remark', 'room_check_id', 'room_id', 'owner_id', 'tenant_id',
                'cleaning_type', 'cleaning_status', 'cleaning_date', 'claim_by_owner', 'claim_by_tenant',
                'paid', 'receive_from', 'issue_by', 'receiptno', 'sequence', 'referenceno', 'paymentmethod', 'processing_fees', 'paymentdate'];
    }

    public function cleaningDefaultCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }


    public function cleaningListingCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }
    public function cleaningFilterCols()
    {
        return ['keyword'];
    }
}
