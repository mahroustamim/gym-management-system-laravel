<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'logo',
        'subscription_plan_id',
        'status',
        'user_id',
    ];

    public function saasPlan()
    {
        return $this->belongsTo(SaasSubscriptionPlan::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function gymPlans()
    {
        return $this->hasMany(GymSubscriptionPlan::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function trainers()
    {
        return $this->hasMany(Trainer::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function logs()
    {
        return $this->hasMany(GymLog::class);
    }
}
