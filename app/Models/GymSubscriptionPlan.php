<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymSubscriptionPlan extends Model
{
    protected $fillable = [
        'gym_id',
        'name',
        'duration_days',
        'price',
        'notes',
    ];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'subscription_plan_id');
    }
}
