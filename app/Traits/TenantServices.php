<?php

namespace App\Traits;

use App\User;
use App\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use DB;

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


    private function filterTenants( $params, $take, $skip)
    {
        $params = $this->checkUndefinedProperty($params, $this->tenantFilterCols());

        $query = User::query();
        $userTypeId = $this->tenantType;
        $query->whereHas('usertypes', function($q) use($userTypeId) {
            $q->where('user_type_id', $userTypeId);
        });
        // $query->join('room_contracts', function ($join) {
        //     $join->on('users.id', '=', 'room_contracts.tenant_id');
        //     $join->join('rooms', function ($join1) {
        //         $join1->on('rooms.id', '=', 'room_contracts.room_id');
        //     })->select('rooms.unit as unit');;
        // })->select('users.*', 'unit');
        $query->orderBy('name');
        if ($params->keyword) {
            $keyword = $params->keyword;
            $query->where('users.name', 'like', '%' . $keyword . '%');
            $query->orWhere('icno', 'like', '%' . $keyword . '%');
        }

        if ($params->occupation) {
            $keyword = $params->occupation;
            $query->where('occupation', 'like', '%' . $keyword . '%');
        }

        if ($params->state) {
            $keyword = $params->state;
            $query->where('state', 'like', '%' . $keyword . '%');
        }

        if ($params->religion) {
            $religion = $params->religion;
            $query->where('religion', 'like', '%' . $religion . '%');
        }

        if ($params->approach_method) {
            $approach_method = $params->approach_method;
            $query->where('approach_method', 'like', '%' . $approach_method . '%');
        }

        if ($params->gender) {
            $gender = $params->gender;
            $query->where('gender', 'like', '%' . $gender . '%');
        }

        if ($params->tel) {
            $tel = $params->tel;
            $query->where('tel1', 'like', '%' . $tel . '%');
        }


        if ($params->birthdayfromdate) {
            $date = Carbon::parse($params->birthdayfromdate)->startOfDay();
            $query->whereDate('birthday', '>=', $date );
        }

        if ($params->birthdaytodate) {
            $date = Carbon::parse($params->birthdaytodate)->endOfDay();
            $query->whereDate('birthday', '<=', $date );
        }


        if ($params->birthdayFromMonth) {
            $query->whereMonth('birthday', '>=', $params->birthdayFromMonth );
            $query->whereDay('birthday', '>=', $params->birthdayFromDay );
        }

        if ($params->birthdayToMonth) {
            $query->whereMonth('birthday', '<=', $params->birthdayToMonth );
            $query->whereDay('birthday', '<=', $params->birthdayToMonth );
        }


        if ($params->pic) {
            $pic = $params->pic;
            $query->whereHas('creator', function($q) use($pic) {
                $q->where('id', $pic);
            });
        }

        if($params->room_id){
            $room_id = $params->room_id;
            $query->whereHas('roomcontracts', function($q) use($room_id) {
                $q->whereHas('room', function($q1) use($room_id) {
                    $q1->where('id', $room_id);
                });
            });
        }

        $total = $query->count();
        if($skip){
            $query->skip($skip);
        }
        if($take){
            $query->take($take);
        }else{
            $query->take(10);
        }
        $data = $query->where('users.status', true)->with(['roomcontracts' => function ($q) {
            // Query the name field in status table
            $q->where('status', true);
            $q->with(['room' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
            }, 'contract' => function ($q) {
                // Query the name field in status table
                $q->where('status', true);
            }]);
        }, 'creator'])->get()->unique('id')->flatten(1);

        $result['data'] = $data;
        $result['total'] = $total;

        return  $result;
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
