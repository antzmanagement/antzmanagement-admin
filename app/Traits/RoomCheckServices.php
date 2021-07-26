<?php

namespace App\Traits;

use App\RoomCheck;
use Carbon\Carbon;
use OpenApi\Annotations\Property;

trait RoomCheckServices
{



    private function getRoomChecks($requester)
    {

        $data = collect();

        $data = RoomCheck::where('status', true)->with(['room' => function ($q) {
            $q->where('status', true);
        }, 'maintenances' => function ($q) {
            // Query the name field in status table
            $q->with(['property' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['owner' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['tenant' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->where('status', true);
        }, 'cleanings' => function ($q) {
            $q->with(['owner' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['tenant' => function ($q1) {
                $q1->where('status', true);
            }]);
            // Query the name field in status table
            $q->where('status', true);
        }])->get();

        $data = $data->unique('id')->sortBy(function ($item, $key) {
            return $item->room->unit;
        })->flatten(1);

        return $data;
    }


    private function filterRoomChecks($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->roomCheckFilterCols());

        if($params->room_id){
            $room_id = $params->room_id;
            $data = $data->filter(function ($item) use($room_id) {
                if($item->room){
                    return $item->room->id == $room_id;
                }
                return false;
            })->values();
        }

        $data = $data->unique('id')->sortBy(function ($item, $key) {
            return $item->checked_date;
        })->flatten(1);

        return $data;
    }

    private function getRoomCheck($uid)
    {

        $data = RoomCheck::where('uid', $uid)->with(['room' => function ($q) {
            $q->where('status', true);
        }, 'maintenances' => function ($q) {
            // Query the name field in status table
            $q->with(['property' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['room' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['owner' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['tenant' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['issueby' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->where('status', true);
        }, 'cleanings' => function ($q) {
            $q->with(['owner' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['room' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['tenant' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['issueby' => function ($q1) {
                $q1->where('status', true);
            }]);
            // Query the name field in status table
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function getRoomCheckById($id)
    {
        $data = RoomCheck::where('id', $id)->with(['room' => function ($q) {
            $q->where('status', true);
        }, 'maintenances' => function ($q) {
            // Query the name field in status table
            $q->with(['property' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['owner' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['tenant' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['room' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['issueby' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->where('status', true);
        }, 'cleanings' => function ($q) {
            $q->with(['owner' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['tenant' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['room' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['issueby' => function ($q1) {
                $q1->where('status', true);
            }]);
            // Query the name field in status table
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function createRoomCheck($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->roomCheckAllCols());
        $data = new RoomCheck();
        $data->uid = Carbon::now()->timestamp . RoomCheck::count();
        $data->checked_date  = $this->toDate($params->checked_date);
        $data->category  = $params->category;
        $data->remark  = $params->remark;

        $room = $this->getRoomById($params->room_id);
        if ($this->isEmpty($room)) {
            return null;
        }
        $data->room()->associate($room);

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure RoomCheck is not empty when calling this function
    private function updateRoomCheck($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->roomCheckAllCols());
        $data->checked_date  = $this->toDate($params->checked_date);
        $data->category  = $params->category;
        $data->remark  = $params->remark;

        $room = $this->getRoomById($params->room_id);
        if ($this->isEmpty($room)) {
            return null;
        }
        $data->room()->associate($room);

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteRoomCheck($data)
    {

        $maintenances = $data->maintenances()->where('status', true)->get();
        foreach ($maintenances as $maintenance) {
            $this->deleteMaintenance($maintenance);
        }

        $cleanings = $data->cleanings()->where('status', true)->get();
        foreach ($cleanings as $cleaning) {
            $this->deleteCleaning($cleaning);
        }

        $data->status = false;
        if ($this->saveModel($data)) {
            $this->syncRoomStatus($data->room);
            return $data->refresh();
        } else {
            return null;
        }
        
    }
    


    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function roomCheckAllCols()
    {
        return ['id', 'uid', 'room_id','checked_date', 'category' , 'remark'];
    }

    public function roomCheckDefaultCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }


    public function roomCheckListingCols()
    {
        return ['id', 'uid', 'price', 'remark'];
    }
    public function roomCheckFilterCols()
    {
        return ['keyword', 'startDateFromDate', 'startDateToDate', 'endDateFromDate', 'endDateToDate', 'tenant_id', 'owner_id', 'service_ids', 'room_id', 'checkedout', 'outstanding_deposit', 'sequence'];
    }
}
