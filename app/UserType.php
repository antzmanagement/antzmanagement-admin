<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    
    public function users()
    {
        return $this->belongsToMany('App\User','users_types', 'user_type_id', 'user_id')->withPivot('remark','status','created_at','updated_at');
    }
}
