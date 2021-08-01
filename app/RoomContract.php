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

    public function payments()
    {
        return $this->hasMany('App\Payment' , 'room_contract_id');
    }
    
    public function tenant()
    {
        return $this->belongsTo('App\User', 'tenant_id');
    }
    
    public function creator()
    {
        return $this->belongsTo('App\User', 'pic');
    }
    
    public function contract()
    {
        return $this->belongsTo('App\Contract', 'contract_id');
    }

    public function parentroomcontract()
    {
        return $this->belongsTo('App\RoomContract', 'room_contract_id');
    }

    public function childrenroomcontracts()
    {
        return $this->hasMany('App\RoomContract', 'room_contract_id');
    }

    public function origservices()
    {
        return $this->belongsToMany('App\Service','room_contract_orig_services','room_contract_id', 'service_id')->withPivot('remark','status','created_at','updated_at');
    }

    public function addonservices()
    {
        return $this->belongsToMany('App\Service','room_contract_add_on_services','room_contract_id', 'service_id')->withPivot('remark','status','created_at','updated_at');
    }
}
