<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    
    public function rooms()
    {
        return $this->belongsToMany('App\Room','rooms_types', 'room_type_id', 'room_id')->withPivot('remark','status','created_at','updated_at');
    }
}
