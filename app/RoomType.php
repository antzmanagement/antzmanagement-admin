<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    
    public function rooms()
    {
        return $this->belongsToMany('App\Room','rooms_types', 'room_type_id', 'room_id')->withPivot('remark','status','created_at','updated_at');
    }
    
    public function images()
    {
        return $this->hasMany('App\RoomTypeImage', 'room_type_id');
    }
    
    public function properties()
    {
        return $this->belongsToMany('App\Property','room_types_properties','room_type_id', 'property_id')->withPivot('remark','status','qty','created_at','updated_at');
    }
    
    public function services()
    {
        return $this->belongsToMany('App\Service','room_types_services','room_type_id', 'service_id')->withPivot('remark','status','created_at','updated_at');
    }
}
