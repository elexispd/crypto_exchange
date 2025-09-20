<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'state',
        'pin',
        'username',
        'password',
        'secret_phrase_hash',
        'is_admin',
        'kyc_status',
        'kyc_verified_at',
    ];

    protected $hidden = [
        'password',
        'secret_phrase_hash',
        'remember_token',
    ];

    protected $casts = [
        'kyc_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',

    ];
}
