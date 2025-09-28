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
