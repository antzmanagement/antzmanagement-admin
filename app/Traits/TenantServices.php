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

    //Tenant
    private $type = "1";

    private function getTenants($requester)
    {

        $data = collect();
        //Role Based Retrieve Done in Store
        $userType = $this->getUserTypeById($this->type);
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

        $userType = $this->getUserTypeById($this->type);
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
        $userType = $this->getUserTypeById($this->type);
        $data = $userType->users()->where('id', $id)->wherePivot('status', 1)->first();
        return $data;
    }

    private function createTenant($params)
    {

        $params = $this->checkUndefinedProperty($params, $this->tenantAllCols());

        $data = new User();
        $data->uid = Carbon::now()->timestamp . User::count();
        $data->title  = $params->title;
        $data->desc = $params->desc;
        $data->tenantpath = $params->tenantpath;
        $data->tenantpublicid = $params->tenantpublicid;
        $data->totallength = $params->totallength;
        $data->agerestrict = false;
        $data->like = 0;
        $data->dislike = 0;
        $data->view = 0;

        if ($params->scope == 'private') {
            $data->scope = $params->scope;
        } else {
            $data->scope = 'public';
        }

        if ($this->isEmpty($params->free)) {
            return null;
        } else {
            $data->free = $params->free;
            if ($data->free) {
                $data->price = 0;
                $data->disc = 0;
                $data->discpctg = 0;
            } else {
                $data->price = $this->toDouble($params->price);
                if ($this->isEmpty($params->discbyprice)) {
                    return null;
                } else {
                    if ($data->discbyprice) {
                        $data->disc = $this->toDouble($params->disc);
                        $data->discpctg = $this->toInt($this->toDouble($data->disc / $data->price) * 100);
                    } else {
                        $data->discpctg = $this->toInt($params->discpctg);
                        $data->disc = $this->toDouble($data->price * ($data->discpctg / 100));
                    }
                }
            }
        }

        $channel = $this->getChannelById($params->channel_id);
        if ($this->isEmpty($channel)) {
            error_log('here');
            return null;
        }
        $data->channel()->associate($channel);

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Tenant is not empty when calling this function
    private function updateTenant($data,  $params)
    {

        $params = $this->checkUndefinedProperty($params, $this->tenantAllCols());

        $data->title  = $params->title;
        $data->desc = $params->desc;
        $data->tenantpath = $params->tenantpath;
        $data->tenantpublicid = $params->tenantpublicid;
        $data->totallength = $params->totallength;
        $data->agerestrict = false;

        if ($params->scope == 'private') {
            $data->scope = $params->scope;
        } else {
            $data->scope = 'public';
        }

        if ($this->isEmpty($params->free)) {
            return null;
        } else {
            $data->free = $params->free;
            if ($data->free) {
                $data->price = 0;
                $data->disc = 0;
                $data->discpctg = 0;
            } else {
                $data->price = $this->toDouble($params->price);
                if ($this->isEmpty($params->discbyprice)) {
                    return null;
                } else {
                    if ($data->discbyprice) {
                        $data->disc = $this->toDouble($params->disc);
                        $data->discpctg = $this->toInt($this->toDouble($data->disc / $data->price) * 100);
                    } else {
                        $data->discpctg = $this->toInt($params->discpctg);
                        $data->disc = $this->toDouble($data->price * ($data->discpctg / 100));
                    }
                }
            }
        }

        $channel = $this->getChannelById($params->channel_id);
        if ($this->isEmpty($channel)) {
            error_log('here');
            return null;
        }
        $data->channel()->associate($channel);

        if (!$this->saveModel($data)) {
            return null;
        }

        return $data->refresh();
    }

    private function deleteTenant($data)
    {

        $comments = $data->comments;
        foreach ($comments as $comment) {
            if (!$this->deleteComment($comment)) {
                return null;
            }
        }

        $data->status = false;
        if ($this->saveModel($data)) {
            return $data->refresh();
        } else {
            return null;
        }

        return $data->refresh();
    }

    public function calculateTenantPromotionPrice($data)
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
    private function getAllPublicTenants()
    {

        $data = User::where('status', true)->where('scope', 'public')->get();

        return $data;
    }

    private function likeTenant($data)
    {

        $data->like += 1;
        if ($this->saveModel($data)) {
            return $data->refresh();
        } else {
            return null;
        }


        return true;
    }

    private function tenantChangeToTrailerSources($tenant)
    {

        if ($this->isEmpty($tenant)) {
            return null;
        }

        if ($tenant->free) {
            return null;
        }

        $trailer = $tenant->trailers()->where('status', true)->where('scope', 'public')->first();
        if ($this->isEmpty($trailer)) {
            return null;
        }

        $tenant->tenantpath = $trailer->tenantpath;
        $tenant->tenantpublicid = $trailer->tenantpublicid;
        $tenant->imgpath = $trailer->imgpath;
        $tenant->imgpublicid = $trailer->imgpublicid;
        $tenant->totallength = $trailer->totallength;
        $tenant->view = $trailer->view;

        return $tenant;
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

    private function validateUserPurchasedTenant($user, $tenant)
    {

        if ($this->isEmpty($user)) {
            return false;
        }

        if ($this->isEmpty($tenant)) {
            return false;
        }


        if ($tenant->free) {
            return true;
        }

        $purchasedtenants = $user->purchasetenants()->wherePivot('status', true)->get();

        $ids = $purchasedtenants->pluck('id');
        $ids = $ids->filter(function ($id) use ($tenant) {
            return $id == $tenant->id;
        });
        if (!$this->isEmpty($ids)) {
            return true;
        }

        return false;
    }
}
