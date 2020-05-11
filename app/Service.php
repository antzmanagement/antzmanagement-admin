<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function room_types()
    {
        return $this->belongsToMany('App\RoomType','room_types_services','service_id', 'room_type_id')->withPivot('remark','status','created_at','updated_at');
    }
}
