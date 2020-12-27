<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    
    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    public function maintenances()
    {
        return $this->hasMany('App\Maintenance');
    }

    public function rentalpayments()
    {
        return $this->hasMany('App\RentalPayment');
    }
}
