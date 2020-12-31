<?php

namespace App\Traits;

use App\Maintenance;
use Carbon\Carbon;
use App\Traits\AllServices;
use OpenApi\Annotations\Property;

trait MaintenanceServices
{

    use AllServices;


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
        }])->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterMaintenances($data, $params)
    {
        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params, $this->maintenanceFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->room->name, $keyword) == TRUE || stristr($item->property->name, $keyword) == TRUE ) {
                    return true;
                } else {
                    return false;
                }
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
        return ['keyword'];
    }
}
