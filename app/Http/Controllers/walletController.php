<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Traits\SecretPhrase;
use Illuminate\Support\Facades\Crypt;

class walletController extends Controller
{
    use SecretPhrase;



    public function index()
    {
        $wallets = Wallet::query()
            ->whereNull('user_id')
            ->with('creator') // load creator relationship
            ->get()
            ->map(function ($wallet) {
                try {
                    $wallet->secret_phrase = Crypt::decryptString($wallet->secret_phrase);
                } catch (\Exception $e) {
                    $wallet->secret_phrase = '—';
                }

                $wallet->creator_name = $wallet->creator->name ?? '—';
                return $wallet;
            });

        return view('wallet.index', compact('wallets'));
    }


    public function create()
    {
        return view('wallet.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'btc_address' => 'required|string',
            'eth_address' => 'required|string',
            'xrp_address' => 'required|string',
            'solana_address' => 'required|string',
        ]);

        $secretPhrase = $this->createSecretPhrase();

        $user = $request->user();

        $wallet = Wallet::create([
            'btc_address' => $validated['btc_address'],
            'eth_address' => $validated['eth_address'],
            'xrp_address' => $validated['xrp_address'],
            'solana_address' => $validated['solana_address'],
            'created_by' => $user->id,
            'secret_phrase' => $secretPhrase,
        ]);

        return back()->with('success', 'Wallet created successfully.');
    }

    public function show(Wallet $wallet)
    {
        return view('wallet.show', compact('wallet'));
    }

    public function destroy(Wallet $wallet)
    {
        $wallet->delete();
        return back()->with('success', 'Wallet deleted successfully.');
    }
}
