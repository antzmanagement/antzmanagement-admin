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
            if($item->room){
                return $item->room->unit;
            }else{
                return '';
            }
        })->flatten(1);

        return $data;
    }


    private function filterRoomChecks( $params, $take, $skip)
    {
        $params = $this->checkUndefinedProperty($params, $this->roomCheckFilterCols());

        $query = RoomCheck::query();   
        $query->join('rooms', function ($join) {
            $join->on('room_checks.room_id', '=', 'rooms.id');
        })->select('room_checks.*', 'rooms.unit');
        $query->orderBy('unit');

        if($params->room_id){
            $room_id = $params->room_id;
            $query->whereHas('room', function($q) use($room_id) {
                $q->where('rooms.id', $room_id);
            });
        }

        if ($params->category) {
            $category = $params->category;
            $query->where('category',$category);
        }

        if ($params->fromdate) {
            $date = Carbon::parse($params->fromdate);
            $query->whereDate('checked_date', '>=', $date );
        }
        
        if ($params->todate) {
            $date = Carbon::parse($params->todate)->endOfDay();
            $query->whereDate('checked_date', '<=', $date );
        }

        $total = $query->count();
        if($skip){
            $query->skip($skip);
        }
        if($take){
            $query->take($take);
        }else{
            $query->take(10);
        }
        $data = $query->where('room_checks.status', true)->with(['room' => function ($q) {
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
        }])->get()->unique('id')->flatten(1);

        $result['data'] = $data;
        $result['total'] = $total;

        return  $result;
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
        $data->checked_date  = Carbon::parse($params->checked_date)->timezone('Asia/Kuala_Lumpur');
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
        $data->checked_date  = Carbon::parse($params->checked_date)->timezone('Asia/Kuala_Lumpur');
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
        return ['fromdate', 'todate', 'category', 'room_id'];
    }
}
