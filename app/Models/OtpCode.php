<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class OtpCode extends Model
{
    use Notifiable;

    protected $fillable = ['email', 'code', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
