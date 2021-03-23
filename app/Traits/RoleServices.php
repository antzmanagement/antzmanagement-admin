<?php

namespace App\Traits;

use App\Role;
use App\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait RoleServices
{


    private function getRoles($requester)
    {

        $data = Role::all();
        
        return $data;
    }


    private function filterRoles($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->roleFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->uid, $keyword) == TRUE || stristr($item->name, $keyword) == TRUE || stristr($item->icno, $keyword) == TRUE || stristr($item->email, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

      
        $data = $data->unique('id');

        return $data;
    }

    private function getRole($uid)
    {

        
        $data = Role::where('uid', $uid)->where('status', true)->first();
        return $data;
    }

    private function getRoleById($id)
    {
        
        $data = Role::where('id', $id)->where('status', true)->first();
        return $data;
    }

    private function createRole($params)
    {
        return $this->createUser($params);
    }

    //Make Sure Role is not empty when calling this function
    private function updateRole($data,  $params)
    {

        return $this->updateUser($data, $params);
    }

    private function deleteRole($data)
    {

       return $this->deleteUser($data);
    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function roleAllCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'rolepath', 'rolepublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }

    public function roleDefaultCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'rolepath', 'rolepublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }


    public function roleListingCols()
    {

        return [
            'id', 'uid', 'name', 'email',
            'icno', 'tel1', 'password', 'rolepublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }
    public function roleFilterCols()
    {

        return ['keyword', 'roomTypes'];
    }
}
