<?php

namespace App\Traits;

trait Assets
{
    public function getAllAssets() {
        return [
            'Nasdaq' => '^IXIC',
            'S&P 500' => '^GSPC',
            'Gold' => 'GC=F',
            'Oil' => 'CL=F',
            'Tesla' => 'TSLA',
            'Apple' => 'AAPL',
            'Amazon' => 'AMZN',
            'AT&T' => 'T',
            'Nvidia' => 'NVDA',
            'Bitcoin' => 'BTC',
            'Ethereum' => 'ETH',
            'Ripple' => 'XRP',
            'Solana' => 'SOL',
            'Gold' => 'GC=F',
            'Sp500' => '^GSPC',
            'Nasdaq' => '^IXIC',
            'Oil' => 'CL=F',
        ];
    }
    public function getAssetSymbols(): array
    {
        return [
            'Nasdaq' => '^IXIC',
            'S&P 500' => '^GSPC',
            'Gold' => 'GC=F',
            'Oil' => 'CL=F',
            'Tesla' => 'TSLA',
            'Apple' => 'AAPL',
            'Amazon' => 'AMZN',
            'AT&T' => 'T',
            'Nvidia' => 'NVDA',
        ];
    }

    public function getAssetIcons(): array
    {
        return [
            'Nasdaq' => '/icons/nasdaq.png',
            'S&P 500' => '/icons/sp500.png',
            'Gold' => '/icons/gold.png',
            'Oil' => '/icons/oil.png',
            'Tesla' => '/icons/tesla.png',
            'Apple' => '/icons/apple.png',
            'Amazon' => '/icons/amazon.png',
            'AT&T' => '/icons/att.png',
            'Nvidia' => '/icons/nvidia.png',
        ];
    }

    public function getCoinIds(): array
    {
        return ['bitcoin', 'ethereum', 'ripple', 'solana'];
    }

    public function getCoinSymbols(): array
    {
        return [
            'Bitcoin' => 'BTC',
            'Ethereum' => 'ETH',
            'Ripple' => 'XRP',
            'Solana' => 'SOL',
        ];
    }
}
