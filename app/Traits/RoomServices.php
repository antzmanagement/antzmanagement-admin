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
                if (stristr($item->uid, $keyword) == TRUE) {
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

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteRoom($data)
    {

        $data->roomTypes()->detach();
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
