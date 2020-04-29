<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomTypeProperty extends Model
{
    public function room_types()
    {
        return $this->belongsToMany('App\RoomType','room_types_properties','room_type_property_id', 'room_type_id')->withPivot('remark','status','qty','created_at','updated_at');
    }
}
