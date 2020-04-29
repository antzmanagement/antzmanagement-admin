<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomTypeImage extends Model
{
    
    public function roomType()
    {
        return $this->belongsTo('App\RoomType');
    }
}
