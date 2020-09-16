<?php

namespace App\Traits;

use App\Role;
use App\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait RoleServices
{

    use AllServices;


    private function getRoles($requester)
    {

        $data = Role::all();
        
        return $data;
    }


    private function filterRoles($data, $params)
    {
        $data = $this->globalFilter($data, $params);
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
            });
        }

        if ($params->roomTypes) {
            error_log('Filtering roles with roomTypes....');
            $roomTypes = collect($params->roomTypes);
            $data = $data->filter(function ($item) use ($roomTypes) {
                $rooms = $item->ownrooms()->wherePivot('status', true)->where('rooms.status', true)->get();
                $roomTypeids = collect();

                //Merge All Room's Type ids;
                foreach($rooms as $room){
                    $temp = $room->roomTypes()->wherePivot('status', true)->where('room_types.status', true)->get();
                    $ids = $temp->pluck('id');
                    $roomTypeids = $roomTypeids->merge($ids);
                }
                
                //Check pass in ids exist or not
                foreach($roomTypes as $roomType){
                    if(!$roomTypeids->contains($roomType)){
                        return false;
                    }
                }

                return true;
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
