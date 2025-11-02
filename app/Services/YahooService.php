<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class YahooService
{
    protected string $baseUrl = 'https://query1.finance.yahoo.com/v8/finance/chart/';

    /**
     * ✅ Fetch a single asset's live data from Yahoo Finance
     */
    public function getPrice(string $symbol): ?array
    {
        try {
            $cacheKey = "yahoo_price_{$symbol}";

            return Cache::remember($cacheKey, 43200, function () use ($symbol) {
                $response = Http::withoutVerifying()->get($this->baseUrl . $symbol);

                if ($response->failed()) {
                    Log::warning("Yahoo request failed for {$symbol}");
                    return null;
                }

                $data = $response->json();
                $meta = $data['chart']['result'][0]['meta'] ?? null;

                if (!$meta) {
                    return null;
                }

                $price = $meta['regularMarketPrice'] ?? 0;
                $prev = $meta['previousClose'] ?? 0;
                $change = $price - $prev;
                $changePercent = $prev > 0 ? ($change / $prev) * 100 : 0;

                return [
                    'symbol' => $symbol,
                    'price' => round($price, 2),
                    'previous_close' => round($prev, 2),
                    'change' => round($change, 2),
                    'change_percent' => round($changePercent, 2),
                    'currency' => $meta['currency'] ?? 'USD',
                    'exchange' => $meta['exchangeName'] ?? '',
                    'status' => 'success',
                ];
            });
        } catch (\Throwable $e) {
            Log::error("YahooService getPrice error for {$symbol}: " . $e->getMessage());
            return null;
        }
    }



    /**
     * ✅ Fetch all predefined assets (Nasdaq, Gold, Oil, etc.)
     */
    public function getAll($assets): array
    {

        $results = [];

        foreach ($assets as $name => $symbol) {
            $priceData = $this->getPrice($symbol);

            $results[strtolower($name)] = [
                'name' => $name,
                'symbol' => $symbol,
                'price' => $priceData['price'] ?? 0,
                'previous_close' => $priceData['previous_close'] ?? 0,
                'change' => $priceData['change'] ?? 0,
                'change_percent' => $priceData['change_percent'] ?? 0,
                'currency' => $priceData['currency'] ?? 'USD',
                'exchange' => $priceData['exchange'] ?? '',
                'status' => $priceData['status'] ?? 'failed',
            ];
        }

        return $results;
    }
}
