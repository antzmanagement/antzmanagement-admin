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
}
