<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    public function room_types()
    {
        return $this->belongsToMany('App\RoomType','room_types_properties','property_id', 'room_type_id')->withPivot('remark','status','qty','created_at','updated_at');
    }
    
    public function maintenances()
    {
        return $this->hasMany('App\Maintenance');
    }
    public function rooms()
    {
        return $this->belongsToMany('App\Room','rooms_properties','property_id', 'room_id')->withPivot('remark','status','qty','created_at','updated_at');
    }
}
