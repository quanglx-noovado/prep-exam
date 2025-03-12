<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    const MAX_ACTIVE_DEVICES = 3;

    protected $fillable = [
        'user_id',
        'name',
        'finger_print',
        'is_active',
        'last_login_at',
        'device_token',
        'verified_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
