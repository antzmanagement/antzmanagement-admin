<?php

namespace App\Traits;
use App\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait UserTypeServices {


    private function getUserTypes($requester) {

        $data = collect();
        //Role Based Retrieve Done in Store
        $channels = $this->getChannels($requester);
        foreach($channels as $channel){
            $data = $data->merge($channel->usertypes()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterUserTypes($data , $params) {
        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params , $this->userTypeFilterCols());

        if($params->keyword){
            $keyword = $params->keyword;
            $data = $data->filter(function($item)use($keyword){
                //check string exist inside or not
                if(stristr($item->title, $keyword) == TRUE) {
                    return true;
                }else{
                    return false;
                }

            });
        }
        
        if($params->scope){
            error_log('Filtering userTypes with scope....');
            $scope = $params->scope;
            if($scope == 'private'){
                $data = $data->filter(function ($item){
                    return $item->scope == 'private';
                });
            }else{
                $data = $data->filter(function ($item){
                    return $item->scope == 'public';
                });
            }
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getUserType($uid) {
        $data = UserType::where('uid', $uid)->with('channel', 'comments.secondcomments')->where('status', 1)->first();
        return $data;
    }

    private function getUserTypeById($id) {
        $data = UserType::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createUserType($params) {

        $params = $this->checkUndefinedProperty($params , $this->userTypeAllCols());

        $data = new UserType();
        $data->uid = Carbon::now()->timestamp . UserType::count();
        $data->name  = $params->name ;
        $data->icno = $params->icno;
        $data->email = $params->email;
        $data->tel1 = $params->tel1;
        $data->password = Hash::make($params->password);

        if(!$this->saveModel($data)){
            return null;
        }

        return $data->refresh();
    }

    //Make Sure UserType is not empty when calling this function
    private function updateUserType($data,  $params) {
        
        $params = $this->checkUndefinedProperty($params , $this->userTypeAllCols());

        $data->title  = $params->title ;
        $data->desc = $params->desc;
        $data->userTypepath = $params->userTypepath;
        $data->userTypepublicid = $params->userTypepublicid;
        $data->totallength = $params->totallength;
        $data->agerestrict = false;
        
        if($params->scope == 'private'){
            $data->scope = $params->scope;
        }else{
            $data->scope = 'public';
        }
        
        if($this->isEmpty($params->free)){
            return null;
        }else{
            $data->free = $params->free;
            if($data->free){
                $data->price = 0;
                $data->disc = 0;
                $data->discpctg = 0;
            }else{
                $data->price = $this->toDouble($params->price);
                if($this->isEmpty( $params->discbyprice)){
                    return null;
                }else{
                    if($data->discbyprice){
                        $data->disc = $this->toDouble($params->disc);
                        $data->discpctg = $this->toInt($this->toDouble($data->disc / $data->price) * 100 );
                    }else{
                        $data->discpctg = $this->toInt($params->discpctg);
                        $data->disc = $this->toDouble($data->price * ($data->discpctg / 100));
                    }
                }
            }
        }

        $channel = $this->getChannelById($params->channel_id);
        if($this->isEmpty($channel)){
            error_log('here');
            return null;
        }
        $data->channel()->associate($channel);

        if(!$this->saveModel($data)){
            return null;
        }

        return $data->refresh();
    }

    private function deleteUserType($data) {
        
        $comments = $data->comments;
        foreach($comments as $comment){
            if(!$this->deleteComment($comment)){
                return null;
            }
        }
        
        $data->status = false;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    public function calculateUserTypePromotionPrice($data) {
        if(!$data->free){
            if($data->discbyprice &&  $data->disc > 0){
                $data->promoprice =  $this->toDouble($data->price - $data->disc);
                $data->promopctg =  $this->toInt($this->toDouble($data->promoprice / $data->price ) * 100);
            }else if( $data->discpctg > 0){
                $data->promopctg =  $this->toInt($data->discpctg);
                $data->promoprice =  $this->toDouble($data->price - ($data->price * ($data->promopctg / 100)));
            }else{
                $data->promoprice = $data->price;
                $data->promopctg = 0;
            }
        }

        return $data;
    }
    private function getAllPublicUserTypes() {
        
        $data = UserType::where('status', true)->where('scope','public')->get();

        return $data;
    }

    
    private function userTypeChangeToTrailerSources($userType) {
        
        if($this->isEmpty($userType)){
            return null;
        }

        if($userType->free){
            return null;
        }
        
        $trailer = $userType->trailers()->where('status',true)->where('scope', 'public')->first();
        if($this->isEmpty($trailer)){
            return null;
        }

        $userType->userTypepath = $trailer->userTypepath;
        $userType->userTypepublicid = $trailer->userTypepublicid;
        $userType->imgpath = $trailer->imgpath;
        $userType->imgpublicid = $trailer->imgpublicid;
        $userType->totallength = $trailer->totallength;
        $userType->view = $trailer->view;

        return $userType;

        
    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function userTypeAllCols() {

        return ['id','channel_id', 'playlist_id', 'uid', 
        'title' , 'desc' , 'userTypepath', 'userTypepublicid'  , 'imgpublicid', 'imgpath' , 'totallength' , 'view' , 
        'like' , 'dislike','price','discpctg','disc','discbyprice','free','salesqty','scope',
        'agerestrict','status'];

    }

    public function userTypeDefaultCols() {

        return ['id','channel_id', 'playlist_id', 'uid', 
        'title' , 'desc' , 'userTypepath', 'userTypepublicid'  , 'imgpublicid', 'imgpath' , 'totallength' , 'view' , 
        'like' , 'dislike','price','discpctg','disc','discbyprice','free','salesqty','scope',
        'agerestrict','status'];

    }
    public function userTypeFilterCols() {

        return ['keyword', 'scope'];

    }
    
}
