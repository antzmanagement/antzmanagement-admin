<?php

namespace App\Traits;

use App\Room;
use App\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait RoomServices
{


    private function getRooms($requester)
    {

        $data = collect();

        $data = Room::with(['maintenances' => function ($q) {
            // Query the name field in status table
            $q->with('property');
            $q->where('status', true);
        }, 'roomTypes' => function ($q) {
            // Query the name field in status table
            $q->with('services');
            $q->wherePivot('status', true);
        }, 'owners' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
        }])->where('status', true)->get();

        $data = $data->unique('id')->sortBy('unit')->flatten(1);

        return $data;
    }


    private function filterRooms($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->roomFilterCols());

    
        if ($params->unit) {
            $unit = $params->unit;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($unit) {
                if ( stristr($item->unit, $unit) == TRUE ) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        if ($params->lot) {
            $lot = $params->lot;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($lot) {
                if ( stristr($item->lot, $lot) == TRUE ) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        if ($params->floor) {
            $floor = $params->floor;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($floor) {
                if ( stristr($item->floor, $floor) == TRUE ) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        if ($params->jalan) {
            $jalan = $params->jalan;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($jalan) {
                if ( stristr($item->jalan, $jalan) == TRUE ) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        if ($params->room_status) {
            $room_status = $params->room_status;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($room_status) {
                return $item->room_status == $room_status;
            })->values();
        }

        if($params->room_type_id){
            $room_type_id = $params->room_type_id;
            $data = $data->filter(function ($item) use($room_type_id) {
                if($item->roomtypes){
                    return $item->roomtypes->contains('id' , $room_type_id);
                }
                return false;
            })->values();
        }

        if($params->owner_id){
            $owner_id = $params->owner_id;
            $data = $data->filter(function ($item) use($owner_id) {
                if($item->owners){
                    return $item->owners->contains('id' , $owner_id);
                }
                return false;
            })->values();
        }


        $data = $data->unique('id');

        return $data;
    }

    private function getRoom($uid)
    {

        $data = Room::where('uid', $uid)->with(['maintenances' => function ($q) {
            // Query the name field in status table
            $q->with('property');
            $q->where('status', true);
        },'roomchecks' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        },'cleanings' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'roomTypes' => function ($q) {
            // Query the name field in status table
            $q->with('services');
            $q->wherePivot('status', true);
        }, 'properties' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
        }, 'owners' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
        }, 'roomcontracts' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function getRoomById($id)
    {
        $data = Room::where('id', $id)->with(['maintenances' => function ($q) {
            // Query the name field in status table
            $q->with('property');
            $q->where('status', true);
        },'roomchecks' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        },'cleanings' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }, 'roomTypes' => function ($q) {
            // Query the name field in status table
            $q->with('services');
            $q->wherePivot('status', true);
        }, 'properties' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
        }, 'owners' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
        }, 'roomcontracts' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function createRoom($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->roomAllCols());
        $data = new Room();
        $data->uid = Carbon::now()->timestamp . Room::count();
        $data->name  = $params->name;
        $data->address = $params->address;
        $data->postcode = $params->postcode;
        $data->state = $params->state;
        $data->city = $params->city;
        $data->country =  $params->country;
        $data->price = $this->toDouble($params->price);
        $data->jalan =  $params->jalan;
        $data->block =  $params->block;
        $data->floor =  $params->floor;
        $data->unit =  $params->unit;
        $data->size =  $this->toDouble($params->size);
        $data->remark =  $params->remark;
        $data->sublet =  $params->sublet;
        $data->sublet_claim = $this->toDouble($params->sublet_claim);
        $data->owner_claim = $this->toDouble($params->owner_claim);
        $data->room_status =  $params->room_status;
        $data->lot =  $params->lot;
        $data->tnb_account_no =  $params->tnb_account_no;

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Room is not empty when calling this function
    private function updateRoom($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->roomAllCols());
        $data->name  = $params->name;
        $data->address = $params->address;
        $data->postcode = $params->postcode;
        $data->state = $params->state;
        $data->city = $params->city;
        $data->country =  $params->country;
        $data->price = $this->toDouble($params->price);
        $data->jalan =  $params->jalan;
        $data->block =  $params->block;
        $data->floor =  $params->floor;
        $data->unit =  $params->unit;
        $data->size =  $this->toDouble($params->size);
        $data->remark =  $params->remark;
        $data->sublet =  $params->sublet;
        $data->sublet_claim = $this->toDouble($params->sublet_claim);
        $data->owner_claim = $this->toDouble($params->owner_claim);
        $data->room_status =  $params->room_status;
        $data->lot =  $params->lot;
        $data->tnb_account_no =  $params->tnb_account_no;

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteRoom($data)
    {

        $data->status = false;
        try {
            $data->roomTypes()->sync([]);
            $data->tenants()->sync([]);
            $data->owners()->sync([]);
            $data->properties()->sync([]);

            $maintenances = $data->maintenances()->where('status', true)->get();
            foreach ($maintenances as $maintenance) {
                $this->deleteMaintenance($maintenance);
            }

            $roomcontracts = $data->roomcontracts()->where('status', true)->get();
            foreach ($roomcontracts as $roomcontract) {
                $this->deleteMaintenance($maintenance);
            }

        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }

        if (!$this->saveModel($data)) {
            return null;
        }


        return $data->refresh();
    }

    private function syncRoomStatus($room)
    {

        if($room){
            $status = $room->room_status;
            $contracts = $room->roomcontracts()->where('status', true)->where('checkedout', false)->count();
            if($contracts > 0){
                $status = 'occupied';
            }else{
                $status = 'vacant';
            }
    
            $maintenances = $room->maintenances()->where('status', true)->where('maintenance_status', 'inprogress')->count();
            if($maintenances > 0){
                $status = 'maintaining';
            }

            $room->room_status = $status;

            if (!$this->saveModel($room)) {
                return null;
            }
        }
    }


    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function roomAllCols()
    {

        return ['id', 'uid', 'name', 'address', 'postcode', 'state', 'city', 'country', 'price', 'status'];
    }

    public function roomDefaultCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'roompath', 'roompublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }


    public function roomListingCols()
    {

        return [
            'id', 'uid', 'name', 'email',
            'icno', 'tel1', 'password', 'roompublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }
    public function roomFilterCols()
    {

        return ['unit', 'jalan','lot', 'floor','room_type_id', 'owner_id','room_status'];
    }
}
