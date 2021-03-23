<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtherPaymentTitle extends Model
{

    public function payments()
    {
        return $this->belongsToMany('App\Payment','other_payment_titles_payments', 'other_payment_title_id', 'payment_id')->withPivot('price','status','created_at','updated_at');
    }
}
