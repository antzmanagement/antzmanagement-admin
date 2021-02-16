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
        }, 'owner' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }])->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

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
            });
        }

        if($params->property_id){
            $property_id = $params->property_id;
            $data = $data->filter(function ($item) use($property_id) {
                if($item->property){
                    return $item->property->id == $property_id;
                }
                return false;
            });
        }
        if($params->owner_id){
            $owner_id = $params->owner_id;
            $data = $data->filter(function ($item) use($owner_id) {
                if($item->owner){
                    return $item->owner->id == $owner_id;
                }
                return false;
            });
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
                return Carbon::parse(data_get($item, 'created_at'))->gte($date);
            });
        }
        
        if ($params->todate) {
            $date = Carbon::parse($params->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return Carbon::parse(data_get($item, 'created_at'))->lte($date);
            });
        }



        $data = $data->unique('id');

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
        }, 'owner' => function ($q) {
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
        }, 'owner' => function ($q) {
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
        $data->remark = $params->remark;
        $data->maintenance_type = $params->maintenance_type;
        $data->maintenance_status = $params->maintenance_status;

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

        $data->name = $room->name. '-'. $property->name. '-' .Carbon::now()->format('Y-m-d');
        if ($params->owner_id) {
            $owner = $this->getOwnerById($params->owner_id);
            if ($this->isEmpty($owner)) {
                return null;
            }
            $data->owner()->associate($owner);
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
        $data->remark = $params->remark;
        $data->maintenance_type = $params->maintenance_type;
        $data->maintenance_status = $params->maintenance_status;

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


        if ($params->owner_id) {
            $owner = $this->getOwnerById($params->owner_id);
            if ($this->isEmpty($owner)) {
                return null;
            }
            $data->owner()->associate($owner);
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

        return ['id', 'uid', 'price', 'remark'];
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
        return ['fromdate', 'todate', 'owner_id', 'room_id', 'property_id', 'maintenance_status', 'maintenance_type'];
    }
}
