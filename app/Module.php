<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    
    public function actions()
    {
        return $this->hasMany('App\Action');
    }
    
    public function roles()
    {
        return $this->belongsToMany('App\Role','module_role', 'module_id', 'role_id')->withPivot('status','created_at','updated_at');
    }
}
