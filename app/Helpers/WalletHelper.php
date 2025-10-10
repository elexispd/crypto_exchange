<?php

namespace App\Helpers;

use App\Models\Wallet;

class WalletHelper
{
    /**
     * Get the wallet address for a specific currency.
     *
     * @param  \App\Models\Wallet|null  $wallet
     * @param  string  $currency
     * @return string|null
     */
    public static function getAddress(?Wallet $wallet, string $currency): ?string
    {
        if (! $wallet) {
            return null;
        }

        $map = [
            'btc' => 'btc_address',
            'eth' => 'eth_address',
            'xrp' => 'xrp_address',
            'sol' => 'solana_address',
            'gold' => null,
            'sp500' => null,
            'nasdaq' => null,
            'oil' => null,
        ];

        $currency = strtolower($currency);

        if (! isset($map[$currency]) || ! $map[$currency]) {
            return null;
        }

        return $wallet->{$map[$currency]} ?? null;
    }
}
