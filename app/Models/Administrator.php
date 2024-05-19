<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;

class Administrator extends Model implements Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, AuthenticatableTrait;

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