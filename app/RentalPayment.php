<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RentalPayment extends Model
{
    public function roomcontract()
    {
        return $this->belongsTo('App\RoomContract', 'room_contract_id');
    }

    public function claim()
    {
        return $this->belongsTo('App\Claim');
    }

    public function issueby()
    {
        return $this->belongsTo('App\User', 'issueby');
    }

    public function deletedby()
    {
        return $this->belongsTo('App\User', 'deletedby');
    }
}
