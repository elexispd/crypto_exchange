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
        'solana_address',
        'btc_balance',
        'eth_balance',
        'xrp_balance',
        'sol_balance',
        'gold_balance',
        'sp500_balance',
        'nasdaq_balance',
        'oil_balance',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Map currency codes to DB column names
     */
    protected function getBalanceColumn(string $currency): string
    {
        $map = [
            'btc' => 'btc_balance',
            'eth' => 'eth_balance',
            'xrp' => 'xrp_balance',
            'sol' => 'sol_balance',
            'gold' => 'gold_balance',
            'sp500' => 'sp500_balance',
            'nasdaq' => 'nasdaq_balance',
            'oil' => 'oil_balance',
        ];

        $currency = strtolower($currency);

        if (! isset($map[$currency])) {
            throw new \InvalidArgumentException("Unsupported currency: $currency");
        }

        return $map[$currency];
    }

    /**
     * Get balance for a given currency
     */
    public function getBalance(string $currency): float
    {
        $column = $this->getBalanceColumn($currency);
        return (float) $this->{$column};
    }

    /**
     * Increase balance
     */
    public function incrementBalance(string $currency, float $amount): void
    {
        $column = $this->getBalanceColumn($currency);
        $this->increment($column, $amount);
    }

    /**
     * Decrease balance
     */
    public function decrementBalance(string $currency, float $amount): void
    {
        $column = $this->getBalanceColumn($currency);

        if ($this->{$column} < $amount) {
            throw new \RuntimeException("Insufficient balance for $currency");
        }

        $this->decrement($column, $amount);
    }


}
