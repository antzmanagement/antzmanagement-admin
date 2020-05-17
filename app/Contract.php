<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    
    public function roomcontracts()
    {
        return $this->hasMany('App\RoomContract', 'room_contract_id');
    }
}
