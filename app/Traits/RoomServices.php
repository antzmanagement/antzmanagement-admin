<?php

namespace App\Traits;

use App\Room;
use App\RoomType;
use Carbon\Carbon;
use DB;
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


    private function filterRooms( $params, $take, $skip)
    {
        $params = $this->checkUndefinedProperty($params, $this->roomFilterCols());

        $query = Room::query();
        $query->orderBy('unit');
        if ($params->id) {
            $query->where('rooms.id', $params->id);
        }

        if ($params->unit) {
            $unit = $params->unit;
            $query->where('unit', 'like', '%' . $unit . '%');
        }

        if ($params->lot) {
            $lot = $params->lot;
            $query->where('lot', 'like', '%' . $lot . '%');
        }

        if ($params->floor) {
            $floor = $params->floor;
            $query->where('floor', 'like', '%' . $floor . '%');
        }

        if ($params->jalan) {
            $jalan = $params->jalan;
            $query->where('jalan', 'like', '%' . $jalan . '%');
        }

        if ($params->room_status) {
            $room_status = $params->room_status;
            $query->where('room_status', $room_status);
        }

        if($params->room_type_id){
            $room_type_id = $params->room_type_id;
            $query->whereHas('roomTypes', function($q) use($room_type_id) {
                $q->where('room_type_id', $room_type_id);
            });
        }

        if($params->owner_id){
            $owner_id = $params->owner_id;   
            $query->whereHas('owners', function($q) use($owner_id) {
                $q->where('owner_id', $owner_id);
            });
        }

        if($params->minPrice){
            $minPrice = $this->toDouble($params->minPrice);
            $query->where('price', '>=', $minPrice);
        }

        if($params->maxPrice){
            $maxPrice = $this->toDouble($params->maxPrice);
            $query->where('price', '<=', $maxPrice);
        }

        $total = $query->count();
        error_log($total);
        if($skip){
            $query->skip($skip);
        }
        if($take){
            $query->take($take);
        }else{
            $query->take(10);
        }
        $data = $query->with(['maintenances' => function ($q) {
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
        }])->where('rooms.status',true)->get();
        $result['data'] = $data;
        $result['total'] = $total;

        return  $result;
    }

    private function getRoom($uid)
    {

        $data = Room::where('uid', $uid)->with(['maintenances' => function ($q) {
            // Query the name field in status table
            $q->with('property');
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
        },'roomchecks' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
        },'cleanings' => function ($q) {
            // Query the name field in status table  
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

            $roomchecks = $data->roomchecks()->where('status', true)->get();
            foreach ($roomchecks as $roomcheck) {
                $this->deleteRoomCheck($roomcheck);
            }

            $maintenances = $data->maintenances()->where('status', true)->get();
            foreach ($maintenances as $maintenance) {
                $this->deleteMaintenance($maintenance);
            }

            $cleanings = $data->cleanings()->where('status', true)->get();
            foreach ($cleanings as $cleaning) {
                $this->deleteCleaning($cleaning);
            }

            $roomcontracts = $data->roomcontracts()->where('status', true)->get();
            foreach ($roomcontracts as $roomcontract) {
                $this->deleteMaintenance($roomcontract);
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

        return ['unit', 'jalan','lot', 'floor','room_type_id', 'owner_id','room_status', 'id', 'minPrice', 'maxPrice'];
    }
}
