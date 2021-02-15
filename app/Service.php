<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function room_types()
    {
        return $this->belongsToMany('App\RoomType','room_types_services','service_id', 'room_type_id')->withPivot('remark','status','created_at','updated_at');
    }

    public function room_contracts_with_orig()
    {
        return $this->belongsToMany('App\RoomContract','room_contract_orig_services','service_id', 'room_contract_id')->withPivot('remark','status','created_at','updated_at');
    }

    public function room_contracts_with_add_on()
    {
        return $this->belongsToMany('App\RoomContract','room_contract_orig_services','service_id', 'room_contract_id')->withPivot('remark','status','created_at','updated_at');
    }

    public function rentalpayments()
    {
        return $this->belongsToMany('App\RentalPayment','rental_payments_services','service_id', 'rental_payment_id')->withPivot('remark','status','created_at','updated_at');
    }

    public function payments()
    {
        return $this->belongsToMany('App\Payment','payments_services','service_id', 'payment_id')->withPivot('price','remark','status','created_at','updated_at');
    }

}
