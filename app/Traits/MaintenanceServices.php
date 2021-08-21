<?php

namespace App\Traits;

use App\Maintenance;
use Carbon\Carbon;
use OpenApi\Annotations\Property;

trait MaintenanceServices
{



    private function getMaintenances($requester)
    {

        $data = collect();

        $data = Maintenance::where('status', true)->with(['room' => function ($q) {
            // Query the name field in status table
            $q->with(['roomtypes' => function ($q1) {
                // Query the name field in status table
                $q1->wherePivot('status', true);
            }]);
            $q->where('status', true);
        }, 'property' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'roomcheck' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        } , 'owner' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'tenant' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        },'issueby' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }])->get();

        $data = $data->unique('id')->sortByDesc('id')->flatten(1);

        return $data;
    }


    private function filterMaintenances($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->maintenanceFilterCols());

        if($params->room_id){
            $room_id = $params->room_id;
            $data = $data->filter(function ($item) use($room_id) {
                if($item->room){
                    return $item->room->id == $room_id;
                }
                return false;
            })->values();
        }

        if($params->property_id){
            $property_id = $params->property_id;
            $data = $data->filter(function ($item) use($property_id) {
                if($item->property){
                    return $item->property->id == $property_id;
                }
                return false;
            })->values();
        }
        if($params->owner_id){
            $owner_id = $params->owner_id;
            $data = $data->filter(function ($item) use($owner_id) {
                if($item->owner){
                    return $item->owner->id == $owner_id;
                }
                return false;
            })->values();
        }

        if($params->tenant_id){
            $tenant_id = $params->tenant_id;
            $data = $data->filter(function ($item) use($tenant_id) {
                if($item->tenant){
                    return $item->tenant->id == $tenant_id;
                }
                return false;
            })->values();
        }
        if ($params->maintenance_status) {
            $maintenance_status = $params->maintenance_status;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($maintenance_status) {
                return $item->maintenance_status == $maintenance_status;
            })->values();
        }
        if ($params->maintenance_type) {
            $maintenance_type = $params->maintenance_type;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($maintenance_type) {
                return $item->maintenance_type == $maintenance_type;
            })->values();
        }

        if ($params->fromdate) {
            $date = Carbon::parse($params->fromdate);
            $data = collect($data);
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'maintenance_date'))->gte($date);
            })->values();
        }
        
        if ($params->todate) {
            $date = Carbon::parse($params->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'maintenance_date'))->lte($date);
            })->values();
        }



        $data = $data->unique('id')->sortByDesc('id')->flatten(1);

        return $data;
    }

    private function getMaintenance($uid)
    {

        $data = Maintenance::where('uid', $uid)->with(['room' => function ($q) {
            // Query the name field in status table
            $q->with(['roomtypes' => function ($q1) {
                // Query the name field in status table
                $q1->wherePivot('status', true);
            }]);
            $q->where('status', true);
        }, 'property' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'roomcheck' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        } , 'owner' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'tenant' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        },'issueby' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function getMaintenanceById($id)
    {
        $data = Maintenance::where('id', $id)->with(['room' => function ($q) {
            // Query the name field in status table
            $q->with(['roomtypes' => function ($q1) {
                // Query the name field in status table
                $q1->wherePivot('status', true);
            }]);
            $q->where('status', true);
        }, 'property' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'roomcheck' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        } , 'owner' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'tenant' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        },'issueby' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function createMaintenance($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->maintenanceAllCols());

        $data = new Maintenance();
        $data->uid = Carbon::now()->timestamp . Maintenance::count();
        $data->price  = $this->toDouble($params->price);
        $data->maintenance_date  = Carbon::parse($params->maintenance_date)->timezone('Asia/Kuala_Lumpur');
        $data->remark = $params->remark;
        $data->maintenance_type = $params->maintenance_type;
        $data->maintenance_status = $params->maintenance_status;
        $data->other_property = $params->other_property;
        $data->claim_by_owner = $params->claim_by_owner;
        $data->claim_by_tenant = $params->claim_by_tenant;


        $room = $this->getRoomById($params->room_id);
        if ($this->isEmpty($room)) {
            return null;
        }
        $data->room()->associate($room);

        $property = $this->getPropertyById($params->property_id);
        if ($this->isEmpty($property)) {
            return null;
        }
        $data->property()->associate($property);

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

        if (!$this->saveModel($data)) {
            return null;
        }

        $this->syncRoomStatus($data->room);

        return $data->refresh();
    }

    //Make Sure Maintenance is not empty when calling this function
    private function updateMaintenance($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->maintenanceAllCols());
        $data->price  = $this->toDouble($params->price);
        $data->maintenance_date  = Carbon::parse($params->maintenance_date)->timezone('Asia/Kuala_Lumpur');
        $data->remark = $params->remark;
        $data->maintenance_type = $params->maintenance_type;
        $data->maintenance_status = $params->maintenance_status;
        $data->other_property = $params->other_property;
        $data->claim_by_owner = $params->claim_by_owner;
        $data->claim_by_tenant = $params->claim_by_tenant;

        $data->paid = $params->paid == true;
        $data->paymentdate = $this->toDate($params->paymentdate);


        $room = $this->getRoomById($params->room_id);
        if ($this->isEmpty($room)) {
            return null;
        }
        $data->room()->associate($room);

        $property = $this->getPropertyById($params->property_id);
        if ($this->isEmpty($property)) {
            return null;
        }
        $data->property()->associate($property);

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

        $this->syncRoomStatus($data->room);
        return $data->refresh();
    }

    private function deleteMaintenance($data)
    {

        $data->status = false;
        if ($this->saveModel($data)) {
            return $data->refresh();
        } else {
            return null;
        }

        $this->syncRoomStatus($data->room);
        return $data->refresh();
    }


    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function maintenanceAllCols()
    {

        return ['id', 'uid', 'price', 'remark', 'owner_id','room_check_id', 'property_id',
        'room_id', 'tenant_id', 'claim_by_owner', 'claim_by_tenant', 'claim_id', 'maintenance_type', 
        'maintenance_status', 'maintenance_date', 'price', 'paid', 'receive_from', 'issue_by', 'receiptno',
        'sequence', 'referenceno', 'paymentmethod', 'processing_fees', 'paymentdate', 'other_property'];
    }

    public function maintenanceDefaultCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }


    public function maintenanceListingCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }
    public function maintenanceFilterCols()
    {
        return ['fromdate', 'todate', 'owner_id', 'room_id'. 'tenant_id', 'property_id', 'maintenance_status', 'maintenance_type'];
    }
}
