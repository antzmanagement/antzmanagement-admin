<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    
    public function module()
    {
        return $this->belongsTo('App\Module');
    }
}
