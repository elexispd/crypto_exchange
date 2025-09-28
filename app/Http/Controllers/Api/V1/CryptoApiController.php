<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Services\CoinGeckoService;

class CryptoApiController extends Controller
{

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

        return response()->json($formatted);
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
            'name'   => ucfirst($id),
            'symbol' => $map[$id] ?? strtoupper($id),
            'price'  => $price[$id]['usd'] ?? null,
        ]);
    }

    public function market()
    {
        $ids = ['bitcoin', 'ethereum', 'ripple', 'solana'];

        $data = $this->coinGecko->getSelectedMarketData($ids, 'usd');

        $formatted = collect($data)->mapWithKeys(function ($coin) {
            return [
                ucfirst($coin['id']) => [
                    'symbol'      => strtoupper($coin['symbol']),
                    'price'       => $coin['current_price'],
                    'change_24h'  => $coin['price_change_percentage_24h'],
                    'market_cap'  => $coin['market_cap'],
                ]
            ];
        });

        return response()->json($formatted);
    }
}
