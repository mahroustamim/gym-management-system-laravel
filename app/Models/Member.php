<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'gym_id',
        'subscription_plan_id',
        'trainer_id',
        'name',
        'code',
        'email',
        'phone',
        'status',
        'avatar',
        'notes',
    ];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function plan()
    {
        return $this->belongsTo(GymSubscriptionPlan::class, 'subscription_plan_id');
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
