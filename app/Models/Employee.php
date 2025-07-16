<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'gym_id',
        'name',
        'job_name',
        'email',
        'password',
        'phone',
        'permissions',
    ];

    public function casts(): array
    {
        return [
            'permissions' => 'array',
            'password' => 'hashed',
        ];
    }

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }
}
