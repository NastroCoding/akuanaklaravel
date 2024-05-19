<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Administrator extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    protected $fillable = [
        'username', 'password', 'last_login_at', 'created_at', 'updated_at'
    ];

    protected $dates = [
        'last_login_at', 'created_at', 'updated_at'
    ];

    protected $casts = [
        'last_login_at' => 'datetime'
    ];

}
