<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    
    public function roomTypes()
    {
        return $this->belongsToMany('App\RoomType','rooms_types', 'room_id', 'room_type_id')->withPivot('remark','status','created_at','updated_at');
    }

    public function tenants()
    {
        return $this->belongsToMany('App\User','tenants_rooms', 'room_id', 'user_id')->withPivot('remark','status','created_at','updated_at');
    }
}