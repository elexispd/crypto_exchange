<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Services\CoinGeckoService;
use App\Traits\Assets;

class CryptoApiController extends Controller
{
    use Assets;

    protected $coinGecko;


    public function __construct(CoinGeckoService $coinGecko)
    {
        $this->coinGecko = $coinGecko;
    }


    // Get multiple prices
    public function prices()
    {
        $ids = ['bitcoin', 'ethereum', 'ripple', 'solana'];
        $currencies = ['usd'];

        $prices = $this->coinGecko->getPrices($ids, $currencies);

        $formatted = [
            'Bitcoin'  => ['symbol' => 'BTC', 'price' => $prices['bitcoin']['usd'] ?? null],
            'Ethereum' => ['symbol' => 'ETH', 'price' => $prices['ethereum']['usd'] ?? null],
            'Ripple'   => ['symbol' => 'XRP', 'price' => $prices['ripple']['usd'] ?? null],
            'Solana'   => ['symbol' => 'SOL', 'price' => $prices['solana']['usd'] ?? null],
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Prices successfully retrieved',
            'data'  => $formatted,
        ]);
    }

    // Get individual price
    public function price($id)
    {
        $map = [
            'bitcoin'  => 'BTC',
            'ethereum' => 'ETH',
            'ripple'   => 'XRP',
            'solana'   => 'SOL',
        ];

        $price = $this->coinGecko->getPrice($id, 'usd');

        return response()->json([
            'status'  => true,
            'message' => 'Price successfully retrieved',
            'data'  => [
                'name'   => ucfirst($id),
                'symbol' => $map[$id] ?? strtoupper($id),
                'price'  => $price[$id]['usd'] ?? null,
            ],
        ]);
    }


    public function market()
    {
        // Cryptocurrency data
        $cryptoIds = ['bitcoin', 'ethereum', 'ripple', 'solana'];
        $cryptoData = $this->coinGecko->getSelectedMarketData($cryptoIds, 'usd');
        $wallet = auth()->user()->wallet;

        $coins = collect($cryptoData)->mapWithKeys(function ($coin) use ($wallet) {
            $id = $coin['id'];
            $price = $coin['current_price'];
            $symbol = strtoupper($coin['symbol']);

            $balance_field = match ($id) {
                'bitcoin'  => 'btc_balance',
                'ethereum' => 'eth_balance',
                'ripple'   => 'xrp_balance',
                'solana'   => 'sol_balance',
            };

            $balance = (float) ($wallet->$balance_field ?? 0);
            $usd_value = $balance * $price;

            return [
                ucfirst($id) => [
                    'symbol'      => $symbol,
                    'icon'        => $coin['image'],
                    'price'       => number_format($price, 2),
                    'change_24h'  => round($coin['price_change_percentage_24h'], 2),
                    'balance'     => number_format($balance, 8),
                    'usd_equiv'   => number_format($usd_value, 2),
                ]
            ];
        });

        // Traditional assets data
        $assets = $this->getTraditionalAssets();

        // Calculate totals separately
        $cryptoTotal = number_format($coins->sum(function ($coin) {
            return str_replace(',', '', $coin['usd_equiv']);
        }), 2);

        $assetsTotal = number_format(collect($assets)->sum(function ($asset) {
            return str_replace(',', '', $asset['usd_equiv'] ?? '0');
        }), 2);

        $grandTotal = number_format((float)str_replace(',', '', $cryptoTotal) + (float)str_replace(',', '', $assetsTotal), 2);

        return response()->json([
            'status'  => true,
            'message' => 'Market successfully retrieved',
            'data'  => [
                'coins' => $coins,
                'assets' => $assets,
                'totals' => [
                    'crypto' => $cryptoTotal,
                    'assets' => $assetsTotal,
                    'grand_total' => $grandTotal,
                ]
            ],
        ]);
    }

    private function getTraditionalAssets()
    {
        $assets = [
            'Nasdaq' => '^IXIC',
            'S&P 500' => '^GSPC',
            'Gold' => 'GC=F',
            'Oil' => 'CL=F',
            'Tesla' => 'TSLA',
            'Apple' => 'AAPL',
            'Amazon' => 'AMZN',
            'AT&T' => 'T',
            'Nvidia' => 'NVDA'
        ];

        $traditionalData = [];

        foreach ($assets as $name => $symbol) {
            try {
                // Using Yahoo Finance unofficial API
                $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}";
                $response = Http::get($url);
                $data = $response->json();

                if (isset($data['chart']['result'][0]['meta']['regularMarketPrice'])) {
                    $price = $data['chart']['result'][0]['meta']['regularMarketPrice'];
                    $previousClose = $data['chart']['result'][0]['meta']['previousClose'] ?? $price;
                    $changePercent = (($price - $previousClose) / $previousClose) * 100;

                    $traditionalData[$name] = [
                        'symbol' => $symbol,
                        'icon' => $this->getAssetIcon($name),
                        'price' => number_format($price, 2),
                        'change_24h' => round($changePercent, 2),
                        'balance' => '0.00', // Users don't hold these in wallet
                        'usd_equiv' => '0.00',
                    ];
                }
            } catch (\Exception $e) {
                // Fallback to mock data
                $traditionalData[$name] = $this->getMockTraditionalAsset($name, $symbol);
            }
        }

        return $traditionalData;
    }

    private function getAssetIcon($assetName)
    {
        $icons = [
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

        return $icons[$assetName] ?? '/icons/default.png';
    }

    private function getMockTraditionalAsset($name, $symbol)
    {
        $mockData = [
            'Nasdaq' => ['price' => 14500.25, 'change' => 0.87],
            'S&P 500' => ['price' => 4550.75, 'change' => 0.78],
            'Gold' => ['price' => 1950.80, 'change' => 0.64],
            'Oil' => ['price' => 78.45, 'change' => -0.95],
            'Tesla' => ['price' => 245.60, 'change' => 3.70],
            'Apple' => ['price' => 178.85, 'change' => 1.22],
            'Amazon' => ['price' => 145.30, 'change' => 1.29],
            'AT&T' => ['price' => 16.45, 'change' => -0.90],
            'Nvidia' => ['price' => 485.25, 'change' => 3.37],
        ];

        $data = $mockData[$name] ?? ['price' => 0, 'change' => 0];

        return [
            'symbol' => $symbol,
            'icon' => $this->getAssetIcon($name),
            'price' => number_format($data['price'], 2),
            'change_24h' => $data['change'],
            'balance' => '0.00',
            'usd_equiv' => '0.00',
        ];
    }






    // endpoint to get the four coins and it's symble
    public function coins()
    {
        $ids = ['bitcoin', 'ethereum', 'ripple', 'solana'];

        $formatted = [
            'Bitcoin'  => ['symbol' => 'BTC'],
            'Ethereum' => ['symbol' => 'ETH'],
            'Ripple'   => ['symbol' => 'XRP'],
            'Solana'   => ['symbol' => 'SOL'],
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Coins successfully retrieved',
            'data'  => $formatted,
        ]);
    }

    public function allAssets()
    {
        return response()->json([
            'status'  => true,
            'message' => 'Assets successfully retrieved',
            'data'  => $this->getAllAssets(),
        ]);
    }
}
