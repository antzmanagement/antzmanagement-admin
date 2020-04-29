<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomTypeService extends Model
{
    public function room_types()
    {
        return $this->belongsToMany('App\RoomType','room_types_services','room_type_service_id', 'room_type_id')->withPivot('remark','status','created_at','updated_at');
    }
}
