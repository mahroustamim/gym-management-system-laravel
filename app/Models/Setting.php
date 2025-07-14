<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'logo',
        'favicon',
        'name',
        'description',
        'email',
        'phone',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube'
    ];
}
