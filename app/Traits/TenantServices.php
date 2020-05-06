<?php

namespace App\Traits;

use App\User;
use App\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait TenantServices
{

    use AllServices;

    private function getTenants($requester)
    {

        $data = collect();
        //Role Based Retrieve Done in Store
        $userType = $this->getUserTypeById($this->tenantType);
        $data = $data->merge($userType->users()->wherePivot('status', true)->where('users.status', true)->get());

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterTenants($data, $params)
    {
        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params, $this->tenantFilterCols());

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
            error_log('Filtering tenants with roomTypes....');
            $roomTypes = collect($params->roomTypes);
            $data = $data->filter(function ($item) use ($roomTypes) {
                $rooms = $item->rentrooms()->wherePivot('status', true)->where('rooms.status', true)->get();
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

    private function getTenant($uid)
    {

        $userType = $this->getUserTypeById($this->tenantType);
        $data = $userType->users()->where('uid', $uid)->wherePivot('status', 1)->with(['rentrooms' => function ($q) {
            // Query the name field in status table
            $q->wherePivot('status', true);
            $q->where('rooms.status', true);
            $q->with(['roomTypes' => function($q1){
                $q1->wherePivot('status', true);
                $q1->where('room_types.status', true);
            }]);
        }])->where('users.status', true)->first();
        return $data;
    }

    private function getTenantById($id)
    {
        $userType = $this->getUserTypeById($this->tenantType);
        $data = $userType->users()->where('id', $id)->wherePivot('status', 1)->first();
        return $data;
    }

    private function createTenant($params)
    {

      
        return $this->createUser($params);
    }

    //Make Sure Tenant is not empty when calling this function
    private function updateTenant($data,  $params)
    {
        return $this->updateUser($data, $params);
    }

    private function deleteTenant($data)
    {

        return $this->deleteUser($data);
    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function tenantAllCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'tenantpath', 'tenantpublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }

    public function tenantDefaultCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'tenantpath', 'tenantpublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }


    public function tenantListingCols()
    {

        return [
            'id', 'uid', 'name', 'email',
            'icno', 'tel1', 'password', 'tenantpublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }
    public function tenantFilterCols()
    {

        return ['keyword', 'roomTypes'];
    }

}
