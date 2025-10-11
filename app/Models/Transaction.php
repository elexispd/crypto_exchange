<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected $fillable = [
        'id', 'user_id', 'wallet_id',
        'type', 'currency', 'amount',
        'from_currency', 'to_currency', 'from_amount', 'to_amount',
        'status', 'to_address', 'narrative', 'fee'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
