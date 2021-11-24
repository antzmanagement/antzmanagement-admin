<?php

namespace App\Traits;

use App\User;
use App\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait StaffServices
{

    private function getStaffs($requester)
    {

        $data = collect();
        //Role Based Retrieve Done in Store
        $userType = $this->getUserTypeById($this->staffType);
        $data = $data->merge($userType->users()->wherePivot('status', true)->where('users.status', true)->with(['usertypes' => function ($q) {
            $q->wherePivot('status', true);
        }, 'role' => function ($q) {
            $q->where('status', true);
        }])->get());

        $data = $data->unique('id')->sortByDesc('id')->flatten(1);

        return $data;
    }


    private function filterStaffs( $params, $take, $skip)
    {
        $params = $this->checkUndefinedProperty($params, $this->staffFilterCols());

        $query = User::query();
        $userTypeId = $this->staffType;
        $query->whereHas('usertypes', function($q) use($userTypeId) {
            $q->where('user_type_id', $userTypeId);
        });
        $query->orderBy('id', 'DESC');
        if ($params->keyword) {
            $keyword = $params->keyword;
            $query->where('name', 'like', '%' . $keyword . '%');
            $query->orWhere('icno', 'like', '%' . $keyword . '%');
        }

        $total = $query->count();
        if($skip){
            $query->skip($skip);
        }
        if($take){
            $query->take($take);
        }
        $data = $query->where('users.status', true)->with(['usertypes' => function ($q) {
            $q->wherePivot('status', true);
        }, 'role' => function ($q) {
            $q->where('status', true);
        }])->get();
        $result['data'] = $data;
        $result['total'] = $total;

        return  $result;
    }

    private function getStaff($uid)
    {

        $userType = $this->getUserTypeById($this->staffType);
        $data = $userType->users()->with(['usertypes' => function ($q) {
            $q->wherePivot('status', true);
        }, 'role' => function ($q) {
            $q->where('status', true);
        }])->where('uid', $uid)->where('users.status', 1)->first();
        return $data;
    }

    private function getStaffById($id)
    {
        $userType = $this->getUserTypeById($this->staffType);
        $data = $userType->users()->with(['usertypes' => function ($q) {
            $q->wherePivot('status', true);
        }, 'role' => function ($q) {
            $q->where('status', true);
        }])->where('users.id', $id)->where('users.status', true)->first();
        return $data;
    }

    private function createStaff($params)
    {

      
        return $this->createUser($params);
    }

    //Make Sure Staff is not empty when calling this function
    private function updateStaff($data,  $params)
    {
        return $this->updateUser($data, $params);
    }

    private function deleteStaff($data)
    {

        return $this->deleteUser($data);
    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function staffAllCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'staffpath', 'staffpublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }

    public function staffDefaultCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'staffpath', 'staffpublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }


    public function staffListingCols()
    {

        return [
            'id', 'uid', 'name', 'email',
            'icno', 'tel1', 'password', 'staffpublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }
    public function staffFilterCols()
    {

        return ['keyword'];
    }

}
