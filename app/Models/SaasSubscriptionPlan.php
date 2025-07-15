<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasSubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'duration_type',
        'duration_count',
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

    // ✅ Accessor (decode JSON automatically when getting value)
    public function getFeaturesAttribute($value)
    {
        return json_decode($value, true);
    }

    // ✅ Mutator (encode array into JSON before saving)
    public function setFeaturesAttribute($value)
    {
        $this->attributes['features'] = json_encode($value);
    }
}
