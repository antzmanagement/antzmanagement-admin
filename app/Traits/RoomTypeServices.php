<?php

namespace App\Traits;

use App\RoomType;
use App\RoomTypeType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait RoomTypeServices
{

    use AllServices;


    private function getRoomTypes($requester)
    {

        $data = collect();

        $data = RoomType::where('status', true)->with(['images' => function ($q){
            $q->where('status', true);
        }, 'properties' => function ($q){
            $q->wherePivot('status', true);
            $q->where('properties.status', true);
        }, 'services' => function ($q){
            $q->wherePivot('status', true);
            $q->where('services.status', true);
        }])->get();

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterRoomTypes($data, $params)
    {
        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params, $this->roomTypeFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->title, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            });
        }

        if ($params->scope) {
            error_log('Filtering roomTypes with scope....');
            $scope = $params->scope;
            if ($scope == 'private') {
                $data = $data->filter(function ($item) {
                    return $item->scope == 'private';
                });
            } else {
                $data = $data->filter(function ($item) {
                    return $item->scope == 'public';
                });
            }
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getRoomType($uid)
    {

        $data = RoomType::where('uid', $uid)->with(['services' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function getRoomTypeById($id)
    {
        $data = RoomType::where('id', $id)->with(['services' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
        }])->where('status', true)->first();
        return $data;
    }

    private function createRoomType($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->roomTypeAllCols());

        $data = new RoomType();
        $data->uid = Carbon::now()->timestamp . RoomType::count();
        $data->name  = $params->name;
        $data->price = $this->toDouble($params->price);

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure RoomType is not empty when calling this function
    private function updateRoomType($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->roomTypeAllCols());

        $data->name  = $params->name;
        $data->price = $this->toDouble($params->price);

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }


    private function deleteRoomType($data)
    {

        $data->status = false;
        try {
            $ids = $data->rooms()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->rooms()->updateExistingPivot($ids, ['status' => false]);

            $ids = $data->properties()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->properties()->updateExistingPivot($ids, ['status' => false]);

            $ids = $data->services()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->services()->updateExistingPivot($ids, ['status' => false]);
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
    public function roomTypeAllCols()
    {

        return [
            'id', 'uid', 'name', 'price','status'
        ];
    }

    public function roomTypeDefaultCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'roomTypepath', 'roomTypepublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }


    public function roomTypeListingCols()
    {

        return [
            'id', 'uid', 'name', 'email',
            'icno', 'tel1', 'password', 'roomTypepublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }
    public function roomTypeFilterCols()
    {

        return ['keyword', 'scope'];
    }

    private function validateUserPurchasedRoomType($user, $roomType)
    {

        if ($this->isEmpty($user)) {
            return false;
        }

        if ($this->isEmpty($roomType)) {
            return false;
        }


        if ($roomType->free) {
            return true;
        }

        $purchasedroomTypes = $user->purchaseroomTypes()->wherePivot('status', true)->get();

        $ids = $purchasedroomTypes->pluck('id');
        $ids = $ids->filter(function ($id) use ($roomType) {
            return $id == $roomType->id;
        });
        if (!$this->isEmpty($ids)) {
            return true;
        }

        return false;
    }
}
