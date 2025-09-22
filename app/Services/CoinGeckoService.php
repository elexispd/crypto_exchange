<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CoinGeckoService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.coingecko.base_url', 'https://api.coingecko.com/api/v3');
        $this->apiKey = config('services.coingecko.api_key');
    }

    /**
     * Get trending cryptocurrencies
     */
    public function getTrending()
    {
        return $this->makeRequest('/search/trending');
    }

    /**
     * Get simple price data for multiple coins
     */
    public function getPrices($coinIds, $vsCurrencies = 'usd', $include24hrChange = true)
    {
        $params = [
            'ids' => is_array($coinIds) ? implode(',', $coinIds) : $coinIds,
            'vs_currencies' => $vsCurrencies,
            'include_24hr_change' => $include24hrChange
        ];

        return $this->makeRequest('/simple/price', $params);
    }

    /**
     * Get coin data by ID
     */
    public function getCoin($coinId, $localization = false, $tickers = false, $marketData = true, $communityData = false, $developerData = false, $sparkline = false)
    {
        $params = [
            'localization' => $localization,
            'tickers' => $tickers,
            'market_data' => $marketData,
            'community_data' => $communityData,
            'developer_data' => $developerData,
            'sparkline' => $sparkline
        ];

        return $this->makeRequest("/coins/{$coinId}", $params);
    }

    /**
     * Get multiple coins data
     */
    public function getCoins($coinIds, $params = [])
    {
        $defaultParams = [
            'ids' => is_array($coinIds) ? implode(',', $coinIds) : $coinIds,
            'vs_currency' => 'usd',
            'order' => 'market_cap_desc',
            'per_page' => 100,
            'page' => 1,
            'sparkline' => false,
            'price_change_percentage' => '24h'
        ];

        $finalParams = array_merge($defaultParams, $params);

        return $this->makeRequest('/coins/markets', $finalParams);
    }

    /**
     * Generic method to make API requests
     */
    protected function makeRequest($endpoint, $params = [], $cacheTime = 300)
    {
        $cacheKey = 'coingecko_' . md5($endpoint . serialize($params));

        try {
            return Cache::remember($cacheKey, $cacheTime, function () use ($endpoint, $params) {
                $url = $this->baseUrl . $endpoint;

                $request = Http::withHeaders([
                    'Accept' => 'application/json',
                ]);

                // Add API key if available
                if ($this->apiKey) {
                    $request->withHeaders(['x-cg-demo-api-key' => $this->apiKey]);
                }

                // For GET requests with parameters
                if (!empty($params)) {
                    $response = $request->get($url, $params);
                } else {
                    $response = $request->get($url);
                }

                if ($response->failed()) {
                    Log::error('CoinGecko API request failed', [
                        'url' => $url,
                        'params' => $params,
                        'status' => $response->status(),
                        'response' => $response->body()
                    ]);

                    throw new \Exception("CoinGecko API request failed: " . $response->status());
                }

                return $response->json();
            });
        } catch (\Exception $e) {
            Log::error('CoinGecko service error: ' . $e->getMessage());
            throw $e;
        }
    }
}
