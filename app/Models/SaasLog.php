<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasLog extends Model
{
    protected $fillable = [
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
