<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CoinGeckoService;
use App\Services\TraditionalAssetService;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    protected $coinGeckoService;
    protected $traditionalAssetService;

    public function __construct(CoinGeckoService $coinGeckoService, TraditionalAssetService $traditionalAssetService)
    {
        $this->coinGeckoService = $coinGeckoService;
        $this->traditionalAssetService = $traditionalAssetService;
    }

    public function show($id)
    {
        $user = User::with('wallet')->findOrFail($id);

        // Get crypto prices from CoinGecko
        $cryptoPrices = $this->coinGeckoService->getPrices(['bitcoin', 'ethereum', 'ripple', 'solana']);

        // Get traditional assets prices from Yahoo Finance (FREE - no API key)
        $traditionalPrices = $this->traditionalAssetService->getAllTraditionalAssetsWithPrices();

        return $traditionalPrices;

        return view('users.show', compact('user', 'cryptoPrices', 'traditionalPrices'));
    }

    /**
     * Test route to check if Yahoo Finance is working
     */
    public function testYahoo()
    {
        return $this->traditionalAssetService->testConnection();
    }


}
