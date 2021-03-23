<?php

namespace App\Traits;

use App\User;
use App\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait OwnerServices
{


    private function getOwners($requester)
    {

        $data = collect();
        //Role Based Retrieve Done in Store
        $userType = $this->getUserTypeById($this->ownerType);
        $data = $data->merge($userType->users()->where('users.status', true)->get());

        $data = $data->unique('id')->sortByDesc('id')->flatten(1);

        return $data;
    }


    private function filterOwners($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->ownerFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->name, $keyword) == TRUE || stristr($item->icno, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getOwner($uid)
    {

        $userType = $this->getUserTypeById($this->ownerType);
        $data = $userType->users()->where('users.uid', $uid)->wherePivot('status', 1)->with(['ownrooms' => function ($q) {
            // Query the name field in status table
        }])->with(['claims' => function ($q) {
            // Query the name field in status table
        }])->where('users.status', true)->first();
        return $data;
    }

    private function getOwnerById($id)
    {
        $userType = $this->getUserTypeById($this->ownerType);
        $data = $userType->users()->where('users.id', $id)->wherePivot('status', 1)->with(['ownrooms' => function ($q) {
            // Query the name field in status table
        }])->where('users.status', true)->first();
        return $data;
    }

    private function createOwner($params)
    {
        return $this->createUser($params);
    }

    //Make Sure Owner is not empty when calling this function
    private function updateOwner($data,  $params)
    {

        return $this->updateUser($data, $params);
    }

    private function deleteOwner($data)
    {

       return $this->deleteUser($data);
    }

    private function getOwnerUnclaimRentalPayments($data)
    {
        $rentalPayments = collect();
        $rooms = $data->ownrooms()->where('rooms.status', true)->get();

        foreach ($rooms as $room) {
            $roomcontracts = $room->roomcontracts()->where('status', true)->get();
            foreach ($roomcontracts as $roomcontract) {
                $paidrentals = $roomcontract->rentalpayments()->where('status', true)->where('paid', true)->where('isClaimed', false)->with('roomcontract.room', 'roomcontract.tenant')->get();
                if($paidrentals->count() > 0){
                    $rentalPayments = $rentalPayments->concat($paidrentals);
                }
            }
        }

        return $rentalPayments;
    }

    private function getOwnerUnclaimMaintenances($data)
    {
        $maintenances = collect();
        $rooms = $data->ownrooms()->where('rooms.status', true)->get();

        foreach ($rooms as $room) {
            $unclaimmaintenances = $room->maintenances()->where('status', true)->where('claim_by_owner', true)->where('isClaimed',false)->with(['room' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
            }, 'property' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
            }])->get();
            if($unclaimmaintenances->count() > 0){
                $maintenances = $maintenances->concat($unclaimmaintenances);
            }
        }

        return $maintenances;
    }
    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function ownerAllCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'ownerpath', 'ownerpublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }

    public function ownerDefaultCols()
    {

        return [
            'id', 'channel_id', 'playlist_id', 'uid',
            'title', 'desc', 'ownerpath', 'ownerpublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }


    public function ownerListingCols()
    {

        return [
            'id', 'uid', 'name', 'email',
            'icno', 'tel1', 'password', 'ownerpublicid', 'imgpublicid', 'imgpath', 'totallength', 'view',
            'like', 'dislike', 'price', 'discpctg', 'disc', 'discbyprice', 'free', 'salesqty', 'scope',
            'agerestrict', 'status'
        ];
    }
    public function ownerFilterCols()
    {

        return ['keyword', 'roomTypes'];
    }
}
