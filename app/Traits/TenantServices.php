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
        $data = $userType->users()->where('users.uid', $uid)->wherePivot('status', 1)->with(['roomcontracts' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
            $q->with(['room' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
            }, 'contract' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
            }, 'rentalpayments' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
            }]);
        }])->where('users.status', true)->first();

        return $data;
    }

    private function getTenantById($id)
    {
        $userType = $this->getUserTypeById($this->tenantType);
        $data = $userType->users()->where('users.id', $id)->wherePivot('status', 1)->with(['roomcontracts' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
            $q->where('expired', false);
            $q->with(['room', 'contract', 'rentalpayments', 'tenant']);
        }])->where('users.status', true)->first();
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
