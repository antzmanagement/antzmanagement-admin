<?php

namespace App\Traits;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait UserServices
{

    private function getUsers($requester)
    { }


    private function filterUsers($data, $params)
    {
        $params = $this->checkUndefinedProperty($params, $this->userFilterCols());

        if ($params->keyword) {
            $keyword = $params->keyword;
            $data = $data->filter(function ($item) use ($keyword) {
                //check string exist inside or not
                if (stristr($item->title, $keyword) == TRUE) {
                    return true;
                } else {
                    return false;
                }
            })->values();
        }

        if ($params->scope) {
            error_log('Filtering users with scope....');
            $scope = $params->scope;
            if ($scope == 'private') {
                $data = $data->filter(function ($item) {
                    return $item->scope == 'private';
                })->values();
            } else {
                $data = $data->filter(function ($item) {
                    return $item->scope == 'public';
                })->values();
            }
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getUser($uid)
    {
        $data = User::where('uid', $uid)->with(['usertypes' => function ($q) {
            $q->wherePivot('status', true);
        }, 'role' => function ($q) {
            $q->where('status', true);
            $q->with(['modules' => function($q1){
                $q1->wherePivot('status', true);
                $q1->with(['actions' => function($q2){
                    $q2->where('status', true);
                }]);

            }]);
        }, 'creator'])->where('status', 1)->first();
        return $data;
    }

    private function getUserById($id)
    {
        $data = User::where('id', $id)->with(['usertypes' => function ($q) {
            $q->wherePivot('status', true);
        }, 'role' => function ($q) {
            $q->where('status', true);
            $q->with(['modules' => function($q1){
                $q1->wherePivot('status', true);
                $q1->with(['actions' => function($q2){
                    $q2->where('status', true);
                }]);

            }]);
        }, 'creator'])->where('status', 1)->first();
        return $data;
    }

    private function createUser($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->userAllCols());

        $data = new User();
        $data->uid = Carbon::now()->timestamp . User::count();
        $data->name  = $params->name;
        $data->icno = $params->icno;
        $data->email = $params->email;
        $data->tel1 = $params->tel1;
        $data->tel2 = $params->tel2;
        $data->tel3 = $params->tel3;
        $data->mother_name = $params->mother_name;
        $data->mother_tel = $params->mother_tel;
        $data->father_name = $params->father_name;
        $data->father_tel = $params->father_tel;
        $data->emergency_name = $params->emergency_name;
        $data->emergency_contact = $params->emergency_contact;
        $data->emergency_relationship = $params->emergency_relationship;
        $data->age = $this->toInt($params->age);
        $data->birthday = $this->toDate($params->birthday);
        $data->gender = $params->gender;
        $data->religion = $params->religion;
        $data->approach_method = $params->approach_method;
        $data->occupation = $params->occupation;
        $data->address = $params->address;
        $data->postcode = $params->postcode;
        $data->address = $params->address;
        $data->state = $params->state;
        $data->city = $params->city;
        $data->referenceno = $params->referenceno;
        $data->banktype = $params->banktype;
        $data->otherbanktype = $params->otherbanktype;
        $data->bankaccount = $params->bankaccount;
        $data->bankaccountname = $params->bankaccountname;

        $data->password = Hash::make($params->password);

        $role = $this->getRoleById($params->role_id);
        if ($this->isEmpty($role)) {
            return null;
        }
        $data->role()->associate($role);

        if($params->pic){
            $user = $this->getUserById($params->pic);
            if ($this->isEmpty($role)) {
                return null;
            }
            $data->creator()->associate($user);
        }
        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure User is not empty when calling this function
    private function updateUser($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->userAllCols());

        $data->name  = $params->name;
        $data->icno = $params->icno;
        $data->email = $params->email;
        $data->tel1 = $params->tel1;
        $data->tel2 = $params->tel2;
        $data->tel3 = $params->tel3;
        $data->mother_name = $params->mother_name;
        $data->mother_tel = $params->mother_tel;
        $data->father_name = $params->father_name;
        $data->father_tel = $params->father_tel;
        $data->emergency_name = $params->emergency_name;
        $data->emergency_contact = $params->emergency_contact;
        $data->emergency_relationship = $params->emergency_relationship;
        $data->age = $this->toInt($params->age);
        $data->birthday = $this->toDate($params->birthday);
        $data->gender = $params->gender;
        $data->religion = $params->religion;
        $data->approach_method = $params->approach_method;
        $data->occupation = $params->occupation;
        $data->address = $params->address;
        $data->postcode = $params->postcode;
        $data->address = $params->address;
        $data->state = $params->state;
        $data->city = $params->city;
        $data->referenceno = $params->referenceno;
        $data->banktype = $params->banktype;
        $data->otherbanktype = $params->otherbanktype;
        $data->bankaccount = $params->bankaccount;
        $data->bankaccountname = $params->bankaccountname;

        $role = $this->getRoleById($params->role_id);
        if ($this->isEmpty($role)) {
            return false;
        }
        $data->role()->associate($role);

        if($params->pic){
            $user = $this->getUserById($params->pic);
            if ($this->isEmpty($role)) {
                return null;
            }
            $data->creator()->associate($user);
        }

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteUser($data)
    {


        try {
            $ids = $data->usertypes()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->usertypes()->updateExistingPivot($ids, ['status' => false]);

            $ids = $data->ownrooms()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->ownrooms()->sync([]);

            $roomcontracts = $data->roomcontracts()->where('status', true)->get();
            foreach ($roomcontracts as $roomcontract) {
                $this->deleteRoomContract($roomcontract);
            }

            $manageroomcontracts = $data->manageroomcontracts()->where('status', true)->get();
            foreach ($manageroomcontracts as $manageroomcontract) {
                $manageroomcontract->pic = null;
                $this->updateRoomContract($manageroomcontract, $manageroomcontract);
            }

            $maintenances = $data->maintenances()->where('status', true)->get();
            foreach ($maintenances as $maintenance) {
                $maintenance->tenant_id = null;
                $maintenance->claim_by_tenant = false;
                $this->updateMaintenance($maintenance, $maintenance);
            }

            $cleanings = $data->cleanings()->where('status', true)->get();
            foreach ($cleanings as $cleaning) {
                $cleaning->tenant_id = null;
                $cleaning->claim_by_tenant = false;
                $this->updateCleaning($cleaning, $cleaning);
            }

            $ownermaintenances = $data->ownermaintenances()->where('status', true)->get();
            foreach ($ownermaintenances as $ownermaintenance) {
                $ownermaintenance->owner_id = null;
                $ownermaintenance->claim_by_owner = false;
                $this->updateMaintenance($ownermaintenance, $ownermaintenance);
            }

            $ownercleanings = $data->ownercleanings()->where('status', true)->get();
            foreach ($ownercleanings as $ownercleaning) {
                $ownercleaning->owner_id = null;
                $ownercleaning->claim_by_owner = false;
                $this->updateCleaning($ownercleaning, $ownercleaning);
            }

            $createdUsers = $data->createdUsers()->where('status', true)->get();
            foreach ($createdUsers as $createdUser) {
                $createdUser->pic = null;
                $this->updateUser($createdUser, $createdUser);
            }

            $issuerentalpayments = $data->issuerentalpayments()->where('status', true)->get();
            foreach ($issuerentalpayments as $issuerentalpayment) {
                $issuerentalpayment->issueby = null;
                $this->updateRentalPayment($issuerentalpayment, $issuerentalpayment);
            }

            $issuepayments = $data->issuepayments()->where('status', true)->get();
            foreach ($issuepayments as $issuepayment) {
                $issuepayment->issueby = null;
                $this->updatePayment($issuepayment, $issuepayment);
            }
            
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }


        $data->status = false;
        if ($this->saveModel($data)) {
            return $data->refresh();
        } else {
            return null;
        }

        return $data->refresh();
    }

    public function calculateUserPromotionPrice($data)
    {
        if (!$data->free) {
            if ($data->discbyprice &&  $data->disc > 0) {
                $data->promoprice =  $this->toDouble($data->price - $data->disc);
                $data->promopctg =  $this->toInt($this->toDouble($data->promoprice / $data->price) * 100);
            } else if ($data->discpctg > 0) {
                $data->promopctg =  $this->toInt($data->discpctg);
                $data->promoprice =  $this->toDouble($data->price - ($data->price * ($data->promopctg / 100)));
            } else {
                $data->promoprice = $data->price;
                $data->promopctg = 0;
            }
        }

        return $data;
    }
    private function getAllPublicUsers()
    {

        $data = User::where('status', true)->where('scope', 'public')->get();

        return $data;
    }


    private function userChangeToTrailerSources($user)
    {

        if ($this->isEmpty($user)) {
            return null;
        }

        if ($user->free) {
            return null;
        }

        $trailer = $user->trailers()->where('status', true)->where('scope', 'public')->first();
        if ($this->isEmpty($trailer)) {
            return null;
        }

        $user->userpath = $trailer->userpath;
        $user->userpublicid = $trailer->userpublicid;
        $user->imgpath = $trailer->imgpath;
        $user->imgpublicid = $trailer->imgpublicid;
        $user->totallength = $trailer->totallength;
        $user->view = $trailer->view;

        return $user;
    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function userAllCols()
    {

        return [
            'id', 'uid', 'name', 'email',
            'icno', 'tel1', 'tel2', 'tel3', 'mother_name', 'mother_tel', 'father_name', 'age', 'birthday', 'gender', 'religion', 'approach_method', 'occupation', 'address', 'state', 'postcode', 'city', 'pic',
            'father_tel', 'emergency_name', 'emergency_contact', 'emergency_relationship', 'password', 'status', 'referenceno', 'bankaccount', 'banktype', 'otherbanktype', 'bankaccountname'
        ];
    }

    public function userDefaultCols()
    {

        return [
            'id', 'uid', 'name', 'email',
            'icno', 'tel1', 'password', 'status'
        ];
    }
    public function userFilterCols()
    {

        return ['keyword', 'scope'];
    }
}
