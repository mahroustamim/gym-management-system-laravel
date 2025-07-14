<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasSubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'duration_days',
        'price',
        'employee_limit',
        'features',
        'notes',
    ];

    public function catsts(): array
    {
        return [
            'features' => 'array',
        ];
    }

    public function gym()
    {
        return $this->hasMany(Gym::class);
    }
}
