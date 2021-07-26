<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cleaning extends Model
{  
    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    public function roomcheck()
    {
        return $this->belongsTo('App\RoomCheck', 'room_check_id');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

    public function tenant()
    {
        return $this->belongsTo('App\User', 'tenant_id');
    }

    public function issueby()
    {
        return $this->belongsTo('App\User', 'issue_by');
    }
}
