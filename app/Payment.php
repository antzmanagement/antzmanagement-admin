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

    public function otherpayments()
    {
        return $this->belongsToMany('App\OtherPaymentTitle','other_payment_titles_payments', 'payment_id', 'other_payment_title_id')->withPivot('price','status','created_at','updated_at');
    }

    public function issueby()
    {
        return $this->belongsTo('App\User', 'issueby');
    }
}
