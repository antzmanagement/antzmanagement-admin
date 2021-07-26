<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    
    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    public function roomcheck()
    {
        return $this->belongsTo('App\RoomCheck', 'room_check_id');
    }

    public function property()
    {
        return $this->belongsTo('App\Property');
    }

    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    public function tenant()
    {
        return $this->belongsTo('App\User');
    }

    public function claim()
    {
        return $this->belongsTo('App\Claim');
    }

    public function issueby()
    {
        return $this->belongsTo('App\User', 'issueby');
    }

}
