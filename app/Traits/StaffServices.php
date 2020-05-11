<?php

namespace App\Traits;

use App\User;
use App\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait StaffServices
{

    use AllServices;

    private function getStaffs($requester)
    {

        $data = collect();
        //Role Based Retrieve Done in Store
        $userType = $this->getUserTypeById($this->staffType);
        $data = $data->merge($userType->users()->wherePivot('status', true)->where('users.status', true)->get());

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterStaffs($data, $params)
    {
        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params, $this->staffFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->uid, $keyword) == TRUE || stristr($item->name, $keyword) == TRUE || stristr($item->icno, $keyword) == TRUE || stristr($item->email, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            });
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getStaff($uid)
    {

        $userType = $this->getUserTypeById($this->staffType);
        $data = $userType->users()->where('uid', $uid)->wherePivot('status', 1)->where('users.status', true)->first();
        return $data;
    }

    private function getStaffById($id)
    {
        $userType = $this->getUserTypeById($this->staffType);
        $data = $userType->users()->where('id', $id)->wherePivot('status', 1)->first();
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
