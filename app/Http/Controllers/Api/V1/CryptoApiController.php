<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Assets as ModelsAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Services\CoinGeckoService;
use App\Services\YahooService;
use App\Traits\Assets;
use Illuminate\Support\Facades\Auth;

class CryptoApiController extends Controller
{
    use Assets;

    protected $coinGecko;
    protected $yahoo;


    public function __construct(CoinGeckoService $coinGecko, YahooService $yahooService)
    {
        $this->coinGecko = $coinGecko;
        $this->yahoo = $yahooService;
    }


    /**
     * Get combined prices from CoinGecko (crypto) and Yahoo (traditional assets)
     */
    public function prices()
    {
        // Get crypto assets from CoinGecko
        $cryptoAssets = Asset::where('type', 'coin')->get();
        $cryptoIds = $cryptoAssets->pluck('name')->toArray();
        $cryptoPrices = $this->coinGecko->getPrices($cryptoIds, ['usd']);

        // Format crypto data
        $formattedCrypto = [];
        foreach ($cryptoAssets as $asset) {
            $formattedCrypto[$asset->name] = [
                'symbol' => $asset->symbol,
                'price' => $cryptoPrices[$asset->name]['usd'] ?? null,
                'type' => 'crypto',
                'source' => 'coingecko'
            ];
        }

        // Get traditional assets from Yahoo
        $traditionalAssets = Asset::where('type', '!=', 'coin')->get();
        $assetSymbols = $traditionalAssets->pluck('symbol', 'name')->toArray();
        $yahooPrices = $this->yahoo->getAll($assetSymbols);

        // Format Yahoo data to match structure
        $formattedTraditional = [];
        foreach ($yahooPrices as $name => $data) {
            $formattedTraditional[$name] = [
                'symbol' => $data['symbol'],
                'price' => $data['price'] ?? null,
                // 'logo' => $this->getIcon($name),
                'change_24h' => $data['change_percent'] ?? 0,
                'source' => 'yahoo'
            ];
        }

        // Combine both data sources
        $combinedData = array_merge($formattedCrypto, $formattedTraditional);

        return response()->json([
            'status' => true,
            'message' => 'Prices successfully retrieved',
            'data' => $combinedData
        ]);
    }

    /**
     * Get individual price - automatically detects source
     */
    public function price($id)
    {
        $asset = Asset::where('symbol', strtoupper($id))
            ->orWhere('name', strtolower($id))
            ->first();

        if (!$asset) {
            return response()->json([
                'status' => false,
                'message' => 'Asset not found',
                'data' => null
            ], 404);
        }

        // Determine data source based on asset type
        if ($asset->type === 'coin') {
            $priceData = $this->coinGecko->getPrice($asset->name, 'usd');
            $price = $priceData[$asset->name]['usd'] ?? null;
            $source = 'coingecko';
        } else {
            $priceData = $this->yahoo->getPrice($asset->symbol);
            $price = $priceData['price'] ?? null;
            $source = 'yahoo';
        }

        return response()->json([
            'status' => true,
            'message' => 'Price successfully retrieved',
            'data' => [
                'name' => $asset->name,
                'symbol' => $asset->symbol,
                'price' => $price,
                'source' => $source
            ]
        ]);
    }

    public function market()
    {
        // Cryptocurrency data
        $cryptoIds = Asset::where('type', 'coin')->pluck('name')->toArray();

        $cryptoData = $this->coinGecko->getSelectedMarketData($cryptoIds, 'usd');
        $wallet = Auth::user()->wallet;

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
        $cacheKey = 'traditional_assets_data';
        $cacheDuration = 43200; // 12 hours in seconds

        return Cache::remember($cacheKey, $cacheDuration, function () {
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
                    $response = Http::timeout(10)->get($url);
                    $data = $response->json();

                    if (isset($data['chart']['result'][0]['meta']['regularMarketPrice'])) {
                        $price = $data['chart']['result'][0]['meta']['regularMarketPrice'];
                        $previousClose = $data['chart']['result'][0]['meta']['previousClose'] ?? $price;
                        $changePercent = (($price - $previousClose) / $previousClose) * 100;

                        $traditionalData[$name] = [
                            'symbol' => $symbol,
                            'icon' => $this->getIcon($name),
                            'price' => number_format($price, 2),
                            'change_24h' => round($changePercent, 2),
                            'balance' => '0.00', // Users don't hold these in wallet
                            'usd_equiv' => '0.00',
                        ];
                    } else {
                        // If API returns data but no price, use mock data
                        $traditionalData[$name] = $this->getMockTraditionalAsset($name, $symbol);
                    }
                } catch (\Exception $e) {
                    // Fallback to mock data on any error
                    return null;
                }
            }

            return $traditionalData;
        });
    }

    private function getIcon($assetName)
    {
        $icons = [
            'Bitcoin' => config('services.base_url') . '/assets/img/icons/btc.png',
            'Ethereum' => config('services.base_url') . '/assets/img/icons/eth.png',
            'Ripple' => config('services.base_url') . '/assets/img/icons/xrp.png',
            'Solana' => config('services.base_url') . '/assets/img/icons/sol.png',
            'Nasdaq' => config('services.base_url') . '/assets/img/icons/naz.png',
            'S&p 500' => config('services.base_url') . '/assets/img/icons/sp.png',
            'Gold' => config('services.base_url') . '/assets/img/icons/gold.png',
            'Oil' => config('services.base_url') . '/assets/img/icons/oil.png',
            'Tesla' => config('services.base_url') . '/assets/img/icons/tesla.png',
            'Apple' => config('services.base_url') . '/assets/img/icons/apple.png',
            'Amazon' => config('services.base_url') . '/assets/img/icons/amazon.png',
            'AT&T' => config('services.base_url') . '/assets/img/icons/at.png',
            'Nvidia' => config('services.base_url') . '/assets/img/icons/nvi.png',
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
            'icon' => $this->getIcon($name),
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
        $assets = Asset::all();
        $formatted = [];

        foreach ($assets as $asset) {
            $formatted[$asset->name] = [
                'name' => $asset->name,
                'symbol' => $asset->symbol,
                'icon' => $this->getIcon(ucwords($asset->name)),
                'type' => $asset->type,
            ];
        }
        return response()->json([
            'status'  => true,
            'message' => 'Assets successfully retrieved',
            'data'  => $formatted,
        ]);
    }

    public function getCommodityAssets()
    {
        $assets = Asset::where('type', '!=', 'coin')->get();
        $formatted = [];

        foreach ($assets as $asset) {
            $formatted[$asset->name] = [
                'name' => $asset->name,
                'symbol' => $asset->symbol,
                'icon' => $this->getIcon(ucwords($asset->name)),
                'type' => $asset->type,
            ];
        }
        return response()->json([
            'status'  => true,
            'message' => 'Assets successfully retrieved',
            'data'  => $formatted,
        ]);
    }

    public function getCoinAssets()
    {
        $assets = Asset::where('type', 'coin')->get();
        $formatted = [];

        foreach ($assets as $asset) {
            $formatted[$asset->name] = [
                'name' => $asset->name,
                'symbol' => $asset->symbol,
                'icon' => $this->getIcon(ucwords($asset->name)),
                'type' => $asset->type,
            ];
        }
        return response()->json([
            'status'  => true,
            'message' => 'Assets successfully retrieved',
            'data'  => $formatted,
        ]);
    }

    /**
     * Get single market/asset portfolio info from CoinGecko or Yahoo
     */
    public function getSingleMarket($network)
    {
        try {
            $user = Auth::user();
            $wallet = $user->wallet;

            // Get asset from database
            $asset = Asset::where('name', strtolower($network))
                ->orWhere('symbol', strtoupper($network))
                ->first();

            if (!$asset) {
                return response()->json([
                    'status' => false,
                    'message' => 'Asset not found',
                    'data' => null
                ], 404);
            }

            // Map balance field for crypto assets
            $balanceFieldMap = [
                'bitcoin' => 'btc_balance',
                'ethereum' => 'eth_balance',
                'ripple' => 'xrp_balance',
                'solana' => 'sol_balance',
            ];

            $balanceField = $balanceFieldMap[$asset->name] ?? null;
            $balance = $balanceField ? (float) ($wallet->{$balanceField} ?? 0) : 0;

            // Get data from appropriate source based on asset type
            if ($asset->type === 'coin') {
                // Get from CoinGecko
                $coinData = $this->coinGecko->getSelectedMarketData([$asset->name], 'usd');

                if (empty($coinData)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Coin data not found',
                        'data' => null
                    ], 404);
                }

                $data = $coinData[0];
                $price = $data['current_price'];
                $usdValue = $balance * $price;

                $formattedData = [
                    'name' => ucfirst($asset->name),
                    'symbol' => strtoupper($data['symbol']),
                    'icon' => $data['image'],
                    'price' => number_format($price, 2),
                    'change_24h' => round($data['price_change_percentage_24h'], 2),
                    'balance' => number_format($balance, 8),
                    'usd_equiv' => number_format($usdValue, 2),
                    'market_cap' => number_format($data['market_cap'] ?? 0, 2),
                    'volume_24h' => number_format($data['total_volume'] ?? 0, 2),
                    'high_24h' => number_format($data['high_24h'] ?? 0, 2),
                    'low_24h' => number_format($data['low_24h'] ?? 0, 2),
                    'source' => 'coingecko',
                    'type' => 'crypto'
                ];
            } else {
                // Get from Yahoo - use asset symbol for Yahoo API
                $yahooData = $this->yahoo->getPrice($asset->symbol);

                if (!$yahooData) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Asset data not found',
                        'data' => null
                    ], 404);
                }

                $price = $yahooData['price'];
                $usdValue = $balance * $price;

                $formattedData = [
                    'name' => ucfirst($asset->name),
                    'symbol' => $asset->symbol,
                    'icon' => $this->getIcon(ucfirst($asset->name)),
                    'price' => number_format($price, 2),
                    'change_24h' => round($yahooData['change_percent'], 2),
                    'balance' => number_format($balance, 2),
                    'usd_equiv' => number_format($usdValue, 2),
                    'previous_close' => number_format($yahooData['previous_close'], 2),
                    'currency' => $yahooData['currency'],
                    'exchange' => $yahooData['exchange'],
                    'source' => 'yahoo',
                    'type' => 'traditional'
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Market data successfully retrieved',
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve market data: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
