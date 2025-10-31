<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CoinGeckoService
{
    protected $baseUrl;
    public function __construct()
    {
        $this->baseUrl = config('services.coingecko.base_url', 'https://api.coingecko.com/api/v3/');
    }

    protected $symbolMap = [
        'btc'    => 'bitcoin',
        'eth'    => 'ethereum',
        'xrp'    => 'ripple',
        'sol'    => 'solana',
        'bitcoin' => 'bitcoin',
        'ethereum' => 'ethereum',
        'ripple' => 'xrp',
        'solana' => 'sol',
        'gold'   => 'tether-gold',     // example
        'sp500'  => 's-p-500',         // if you track synthetic assets, you may need another API
        'nasdaq' => 'nasdaq-100',      // same here
        'oil'    => 'crude-oil',       // may not exist in CoinGecko
    ];

    public function getCoinList() {
        return $this->symbolMap;
    }

    public function mapSymbolToId(string $symbol): ?string
    {
        $symbol = strtolower($symbol);
        return $this->symbolMap[$symbol] ?? null;
    }

    /**
     * Get multiple crypto prices (cached)
     */
    public function getPrices(array $ids, array $currencies = ['usd'], int $ttl = 43200) // 12 hours
    {
        $cacheKey = 'crypto_prices_' . md5(implode(',', $ids) . implode(',', $currencies));

        return Cache::remember($cacheKey, $ttl, function () use ($ids, $currencies) {
            $response = Http::get($this->baseUrl . 'simple/price', [
                'ids' => implode(',', $ids),
                'vs_currencies' => implode(',', $currencies),
            ]);

            return $response->json();
        });
    }

    /**
     * Get single crypto price (cached)
     */
    public function getPrice(string $id, string $currency = 'usd', int $ttl = 43200) // 12 hours
    {
        $id = $this->mapSymbolToId($id);
        $cacheKey = "crypto_price_{$id}_{$currency}";

        return Cache::remember($cacheKey, $ttl, function () use ($id, $currency) {
            $response = Http::get($this->baseUrl . 'simple/price', [
                'ids' => $id,
                'vs_currencies' => $currency,
            ]);

            return $response->json();
        });
    }

    public function getSelectedMarketData(array $ids, string $currency = 'usd', int $ttl = 600)
    {
        $cacheKey = 'market_data_' . md5(implode(',', $ids) . $currency);

        return Cache::remember($cacheKey, $ttl, function () use ($ids, $currency) {
            $response = Http::get($this->baseUrl . 'coins/markets', [
                'vs_currency' => $currency,
                'ids' => implode(',', $ids),  // only fetch selected coins
                'order' => 'market_cap_desc',
                'sparkline' => 'false'
            ]);

            return $response->json();
        });
    }
















}
