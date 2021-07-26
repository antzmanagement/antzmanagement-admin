<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomCheck extends Model
{
    public function room()
    {
        return $this->belongsTo('App\Room', 'room_id');
    }

    public function maintenances()
    {
        return $this->hasMany('App\Maintenance');
    }

    public function cleanings()
    {
        return $this->hasMany('App\Cleaning');
    }
}
