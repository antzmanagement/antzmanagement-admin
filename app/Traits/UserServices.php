<?php

namespace App\Traits;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait UserServices
{

    use AllServices;

    private function getUsers($requester)
    { }


    private function filterUsers($data, $params)
    {
        $data = $this->globalFilter($data, $params);
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
            });
        }

        if ($params->scope) {
            error_log('Filtering users with scope....');
            $scope = $params->scope;
            if ($scope == 'private') {
                $data = $data->filter(function ($item) {
                    return $item->scope == 'private';
                });
            } else {
                $data = $data->filter(function ($item) {
                    return $item->scope == 'public';
                });
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
        }])->where('status', 1)->first();
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
        }])->where('status', 1)->first();
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
        $data->mother_name = $params->mother_name;
        $data->mother_tel = $params->mother_tel;
        $data->father_name = $params->father_name;
        $data->father_tel = $params->father_tel;
        $data->emergency_name = $params->emergency_name;
        $data->emergency_contact = $params->emergency_contact;
        $data->emergency_relationship = $params->emergency_relationship;

        $data->password = Hash::make($params->password);

        $role = $this->getRoleById($params->role_id);
        if ($this->isEmpty($role)) {
            return null;
        }
        $data->role()->associate($role);
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
        $data->mother_name = $params->mother_name;
        $data->mother_tel = $params->mother_tel;
        $data->father_name = $params->father_name;
        $data->father_tel = $params->father_tel;
        $data->emergency_name = $params->emergency_name;
        $data->emergency_contact = $params->emergency_contact;
        $data->emergency_relationship = $params->emergency_relationship;

        $role = $this->getRoleById($params->role_id);
        if ($this->isEmpty($role)) {
            return false;
        }
        $data->role()->associate($role);

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

            $ids = $data->rentrooms()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->rentrooms()->updateExistingPivot($ids, ['status' => false]);

            $ids = $data->ownrooms()->wherePivot('status', true)->get();
            $ids = $ids->pluck('id');
            $data->ownrooms()->updateExistingPivot($ids, ['status' => false]);
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
            'icno', 'tel1', 'mother_name', 'mother_tel', 'father_name',
            'father_tel', 'emergency_name', 'emergency_contact', 'emergency_relationship', 'password', 'status'
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
