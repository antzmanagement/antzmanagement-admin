<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    
    public function modules()
    {
        return $this->belongsToMany('App\Module','module_role', 'role_id', 'module_id')->withPivot('status','created_at','updated_at');
    }
    
    public function users()
    {
        return $this->hasMany('App\User');
    }
}
