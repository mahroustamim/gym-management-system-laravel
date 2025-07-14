<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasDiscount extends Model
{
    protected $fillable = [
        'name',
        'type',
        'value',
        'max_usage',
        'current_usage',
        'start_date',
        'end_date',
        'is_active',
    ];

    public function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

}
