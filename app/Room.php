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
    
    public function owners()
    {
        return $this->belongsToMany('App\User','owners_rooms', 'room_id', 'user_id')->withPivot('remark','status','created_at','updated_at');
    }
    
    public function maintenances()
    {
        return $this->hasMany('App\Maintenance');
    }
    
    public function roomcontracts()
    {
        return $this->hasMany('App\RoomContract');
    }

    public function roomchecks()
    {
        return $this->hasMany('App\RoomCheck');
    }

    public function properties()
    {
        return $this->belongsToMany('App\Property','rooms_properties', 'room_id', 'property_id')->withPivot('remark','status','created_at','updated_at');
    }
}
