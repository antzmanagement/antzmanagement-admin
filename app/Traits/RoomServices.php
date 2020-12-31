<?php

namespace App\Traits;

use App\Room;
use App\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait RoomServices
{

    use AllServices;


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
        }])->where('status', true)->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterRooms($data, $params)
    {
        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params, $this->roomFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->unit, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            });
        }
        if ($params->roomTypes) {
            $roomTypes = collect($params->roomTypes);
            $data = $data->filter(function ($item) use ($roomTypes) {
                $item = $item->roomTypes()->wherePivot('status', true)->where('room_types.status', true)->get();
                $ids = $item->pluck('id');
                foreach ($roomTypes as $roomType) {
                    if ($ids->contains($roomType)) {
                        return true;
                    }
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
        }])->where('status', true)->first();
        return $data;
    }

    private function getRoomById($id)
    {
        $data = Room::where('id', $id)->with(['maintenances' => function ($q) {
            // Query the name field in status table
            $q->with('property');
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

        return ['keyword', 'roomTypes'];
    }
}
