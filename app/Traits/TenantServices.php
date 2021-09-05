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
        $data = $data->merge($userType->users()->with('creator')->with(['roomcontracts' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
            $q->with(['room' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
            }, 'contract' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
            }]);
        }, 'creator'])->wherePivot('status', true)->where('users.status', true)->get());

        $data = $data->unique('id')->sortBy(function ($item) {
            $unit = '';
            try {
                $unit = $item->roomcontracts[0]->room->unit;
            } catch (\Throwable $th) {
            }
            return $unit;
        })->flatten(1);

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
                if ( stristr($item->name, $keyword) == TRUE || stristr($item->icno, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        if ($params->occupation) {
            $keyword = $params->occupation;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if ( stristr($item->occupation, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        if ($params->state) {
            $keyword = $params->state;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if ( stristr($item->state, $keyword) == TRUE) {
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

        if ($params->tel) {
            $tel = $params->tel;
            $data = collect($data);
            $data = $data->filter(function ($item) use ($tel) {
                if ( stristr($item->tel1, $tel) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }


        if ($params->birthdayfromdate) {
            $date = Carbon::parse($params->birthdayfromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                if(data_get($item, 'birthday')){
                    return Carbon::parse(data_get($item, 'birthday'))->gte($date);
                }else{
                    return false;
                }
            })->values();
        }

        if ($params->birthdaytodate) {
            $date = Carbon::parse($params->birthdaytodate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                if(data_get($item, 'birthday')){
                    return Carbon::parse(data_get($item, 'birthday'))->lte($date);
                }else{
                    return false;
                }
            })->values();
        }


        if ($params->birthdayFromMonth) {
            if($params->birthdayFromDay){
                $date = Carbon::createFromDate(2012, $params->birthdayFromMonth, $params->birthdayFromDay)->startOfDay();
            }else{
                $date = Carbon::createFromDate(2012, $params->birthdayFromMonth, 1)->startOfDay();
            }
            
            $data = $data->filter(function ($item) use ($date) {
                if(data_get($item, 'birthday')){
                    $birthday = Carbon::parse(data_get($item, 'birthday'));
                    $birthday->setYear(2012);
                    return $birthday->gte($date);
                }else{
                    return false;
                }
            })->values();
        }

        if ($params->birthdayToMonth) {
            if($params->birthdayToDay){
                
                $date = Carbon::createFromDate(2012, $params->birthdayToMonth, $params->birthdayToDay)->endOfDay();
            }else{
                $date = Carbon::createFromDate(2012, $params->birthdayToMonth, 1)->endOfMonth();
                error_log($date);
            }
            
            $data = $data->filter(function ($item) use ($date) {
                if(data_get($item, 'birthday')){
                    $birthday = Carbon::parse(data_get($item, 'birthday'));
                    $birthday->setYear(2012);
                    return $birthday->lte($date);
                }else{
                    return false;
                }
            })->values();
        }


        if($params->room_id){
            $room_id = $params->room_id;
            $data = $data->filter(function ($item) use($room_id) {
                if($item->roomcontracts){
                    return $item->roomcontracts->contains(function($roomcontract)use($room_id) {
                        $room = collect();
                        try {
                            $room = $roomcontract->room;
                        } catch (\Throwable $th) {
                        }
                        return $room->id == $room_id;
                    });
                }
                return false;
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
            $q->with(['payments' => function ($q1) {
                // Query the name field in status table
                $q1->with('services');
                $q1->with('otherpayments');
                $q1->with('issueby');
                $q1->where('status', true);
            }, 'addonservices' => function ($q1) {
                // Query the name field in status table
                $q1->wherePivot('status', true);
            }, 'origservices' => function ($q1) {
                // Query the name field in status table
                $q1->wherePivot('status', true);
            },'room' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
                $q->with(['roomTypes' => function ($q1) {
                    // Query the name field in status table
                    $q1->wherePivot('status', true);
                }]);
            }, 'contract' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
            }, 'rentalpayments' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
            }
          ]);
        },'maintenances' => function ($q) {
            // Query the name field in status table
            $q->with(['property' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['room' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['issueby' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->where('status', true);
        }, 'cleanings' => function ($q) {
            $q->with(['room' => function ($q1) {
                $q1->where('status', true);
            }]);
            $q->with(['issueby' => function ($q1) {
                $q1->where('status', true);
            }]);
            // Query the name field in status table
            $q->where('status', true);
        }, 'creator'])->where('users.status', true)->first();

        return $data;
    }

    private function getTenantById($id)
    {
        $userType = $this->getUserTypeById($this->tenantType);
        $data = $userType->users()->where('users.id', $id)->wherePivot('status', 1)->with(['roomcontracts' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
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

        return ['keyword', 'religion', 'approach_method', 'gender', 'birthdayfromdate', 'birthdaytodate', 'tel', 'pic', 'room_id','birthdayFromMonth','birthdayFromDay', 'birthdayToMonth', 'birthdayToDay', 'occupation', 'state'];
    }

}
