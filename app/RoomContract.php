<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomContract extends Model
{
    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    public function rentalpayments()
    {
        return $this->hasMany('App\RentalPayment' , 'room_contract_id');
    }
    
    public function tenant()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function contract()
    {
        return $this->belongsTo('App\Contract');
    }
}
