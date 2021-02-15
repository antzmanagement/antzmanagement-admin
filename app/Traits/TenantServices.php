<?php

namespace App\Traits;

use App\User;
use App\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait TenantServices
{

    private function getTenants($requester)
    {

        $data = collect([]);
        //Role Based Retrieve Done in Store
        $userType = $this->getUserTypeById($this->tenantType);
        $data = $data->merge($userType->users()->with('creator')->wherePivot('status', true)->where('users.status', true)->get());

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }


    private function filterTenants($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->tenantFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if ( stristr($item->name, $keyword) == TRUE || stristr($item->icno, $keyword) == TRUE || stristr($item->occupation, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        if ($params->religion) {
            $religion = $params->religion;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($religion) {
                return $item->religion == $religion;
            })->values();
        }

        if ($params->approach_method) {
            $approach_method = $params->approach_method;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($approach_method) {
                return $item->approach_method == $approach_method;
            })->values();
        }

        if ($params->pic) {
            $pic = $params->pic;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($pic) {
                if($item->creator){
                    return $item->creator->id == $pic;
                }else{
                    return false;
                }
            })->values();
        }

        if ($params->gender) {
            $gender = $params->gender;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($gender) {
                return $item->gender == $gender;
            })->values();
        }

        if ($params->birthdayfromdate) {
            $date = Carbon::parse($params->birthdayfromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'birthday')) >= $date);
            });
        }

        if ($params->birthdaytodate) {
            $date = Carbon::parse($params->birthdaytodate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'birthday')) <= $date);
            });
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
        }, 'creator'])->where('users.status', true)->first();

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
        }, 'creator'])->where('users.status', true)->first();
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

        return ['keyword', 'religion', 'approach_method', 'gender', 'birthdayfromdate', 'birthdaytodate', 'pic'];
    }

}
