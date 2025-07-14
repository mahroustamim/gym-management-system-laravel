<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasPayment extends Model
{
    protected $fillable = [
        'user_id',
        'discount_id',
        'price',
        'payment_status',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discount()
    {
        return $this->belongsTo(SaasDiscount::class);
    }

}
