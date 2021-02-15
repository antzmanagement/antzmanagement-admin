<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function roomcontract()
    {
        return $this->belongsTo('App\RoomContract', 'room_contract_id');
    }

    public function services()
    {
        return $this->belongsToMany('App\Service','payments_services','payment_id', 'service_id')->withPivot('price', 'remark','status','created_at','updated_at');
    }
}
