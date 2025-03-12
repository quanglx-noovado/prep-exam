<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'sent_type',
        'otp',
        'status',
        'purpose',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
