<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymLog extends Model
{
    protected $fillable = [
        'gym_id',
        'model_type',
        'model_id',
        'user_id',
        'action',
        'changes',
    ];
    
    public function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
