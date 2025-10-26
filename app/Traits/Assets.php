<?php

namespace App\Traits;

trait Assets
{
    public function getAllAssets() {
        $ids = ['bitcoin', 'ethereum', 'ripple', 'solana', 'nasdag', 'oil', 's&p 500', 'gold', 'tesla', 'apple', 'amazon', 'at&t', 'nvidia' ];

        $formatted = [
            'Bitcoin'  => ['symbol' => 'BTC'],
            'Ethereum' => ['symbol' => 'ETH'],
            'Ripple'   => ['symbol' => 'XRP'],
            'Solana'   => ['symbol' => 'SOL'],
            'Nasdaq'   => ['symbol' => 'NASDAQ'],
            'Oil'      => ['symbol' => 'OIL'],
            'S&P 500'  => ['symbol' => 'SPX'],
            'Gold'     => ['symbol' => 'XAU'],
            'Tesla'    => ['symbol' => 'TSLA'],
            'Apple'    => ['symbol' => 'AAPL'],
            'Amazon'   => ['symbol' => 'AMZN'],
            'AT&T'     => ['symbol' => 'T'],
            'Nvidia'   => ['symbol' => 'NVDA'],
        ];

        return $formatted;
    }
}
