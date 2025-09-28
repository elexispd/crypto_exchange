<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasApiTokens, HasFactory,  HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'secret_phrase',
        'btc_address',
        'eth_address',
        'xrp_address',
        'solana_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
