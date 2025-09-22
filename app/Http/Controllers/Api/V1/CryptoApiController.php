<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Services\CoinGeckoService;

class CryptoApiController extends Controller
{
    protected $coinGeckoService;

    public function __construct(CoinGeckoService $coinGeckoService)
    {
        $this->coinGeckoService = $coinGeckoService;
    }

    /**
     * Get trending cryptocurrencies
     */
    public function getTrending()
    {
        try {
            $trendingData = $this->coinGeckoService->getTrending();

            // Format the data
            $formattedData = [];
            foreach ($trendingData['coins'] as $coin) {
                $coinData = $coin['item']['data'];
                $symbol = strtoupper($coin['item']['symbol']);

                $formattedData[$symbol] = [
                    'name' => $coin['item']['name'],
                    'symbol' => $symbol,
                    'price' => number_format($coinData['price'], 2),
                    'change' => number_format($coinData['price_change_percentage_24h']['usd'], 2)
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get portfolio data using the service
     */
    public function getPortfolio()
    {
        try {
            // Define the coins in our portfolio
            $portfolioCoins = ['tether', 'bitcoin', 'ethereum'];

            // Fetch current prices and changes using the service
            $priceData = $this->coinGeckoService->getPrices($portfolioCoins);

            // Format the portfolio data
            $formattedData = [];

            // USDT BNB Smart Chain
            $formattedData[] = [
                'name' => 'USDT BNB Smart Chain',
                'balance' => '0.00',
                'value' => number_format($priceData['tether']['usd'] * 0.00, 2),
                'change' => number_format($priceData['tether']['usd_24h_change'] ?? 0, 2) . '%'
            ];

            // BTC Bitcoin
            $btcBalance = 0.000008;
            $formattedData[] = [
                'name' => 'BTC Bitcoin',
                'balance' => number_format($btcBalance, 8),
                'value' => number_format($priceData['bitcoin']['usd'] * $btcBalance, 2),
                'change' => number_format($priceData['bitcoin']['usd_24h_change'] ?? 0, 2) . '%'
            ];

            // ETH Ethereum
            $ethBalance = 0.000008;
            $formattedData[] = [
                'name' => 'ETH Ethereum',
                'balance' => number_format($ethBalance, 8),
                'value' => number_format($priceData['ethereum']['usd'] * $ethBalance, 2),
                'change' => number_format($priceData['ethereum']['usd_24h_change'] ?? 0, 2) . '%'
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all data (trending + portfolio)
     */
    public function getAllData()
    {
        try {
            // Get trending data using service
            $trendingData = $this->coinGeckoService->getTrending();

            // Get portfolio prices using service
            $portfolioCoins = ['tether', 'bitcoin', 'ethereum'];
            $priceData = $this->coinGeckoService->getPrices($portfolioCoins);

            // Format trending data
            $formattedTrending = [];
            foreach ($trendingData['coins'] as $coin) {
                $coinData = $coin['item']['data'];
                $symbol = strtoupper($coin['item']['symbol']);

                $formattedTrending[$symbol] = [
                    'name' => $coin['item']['name'],
                    'symbol' => $symbol,
                    'price' => number_format($coinData['price'], 2),
                    'change' => number_format($coinData['price_change_percentage_24h']['usd'], 2)
                ];
            }

            // Format portfolio data
            $btcBalance = 0.000008;
            $ethBalance = 0.000008;

            $formattedPortfolio = [
                [
                    'name' => 'USDT BNB Smart Chain',
                    'balance' => '0.00',
                    'value' => number_format($priceData['tether']['usd'] * 0.00, 2),
                    'change' => number_format($priceData['tether']['usd_24h_change'] ?? 0, 2) . '%'
                ],
                [
                    'name' => 'BTC Bitcoin',
                    'balance' => number_format($btcBalance, 8),
                    'value' => number_format($priceData['bitcoin']['usd'] * $btcBalance, 2),
                    'change' => number_format($priceData['bitcoin']['usd_24h_change'] ?? 0, 2) . '%'
                ],
                [
                    'name' => 'ETH Ethereum',
                    'balance' => number_format($ethBalance, 8),
                    'value' => number_format($priceData['ethereum']['usd'] * $ethBalance, 2),
                    'change' => number_format($priceData['ethereum']['usd_24h_change'] ?? 0, 2) . '%'
                ]
            ];

            return response()->json([
                'success' => true,
                'trending' => $formattedTrending,
                'portfolio' => $formattedPortfolio
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific coin data using service
     */
    public function getCoinData($coinId)
    {
        try {
            $data = $this->coinGeckoService->getCoin($coinId);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
