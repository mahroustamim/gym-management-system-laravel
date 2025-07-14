<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    protected $fillable = [
        'gym_id',
        'name',
        'phone',
        'email',
        'specialty',
        'status',
        'price',
    ];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
