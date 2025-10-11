<?php

namespace App\Http\Controllers;

use App\Models\adminWallet;
use Illuminate\Http\Request;

class AdminWalletController extends Controller
{
    public function create()
    {
        return view('admin_wallets.create');
    }
    public function index()
    {
        $wallets = AdminWallet::query()
            ->latest()
            ->get();
        return view('admin_wallets.index', compact('wallets'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'network' => 'required|string|max:50',
            'address' => 'required|string|max:255',
        ]);

        $network = strtoupper(trim($validated['network']));

        // Check if an active wallet with this network already exists
        $existingWallet = AdminWallet::where('network', $network)
            ->where('status', 'active')
            ->first();

        if ($existingWallet) {
            return back()->with('error', "A wallet for {$validated['network']} already exists and is active. Disable it first.");
        }

        // If no active wallet exists, insert new one
        $wallet = new AdminWallet();
        $wallet->network = strtoupper($validated['network']); // optional normalization
        $wallet->address = $validated['address'];
        $wallet->save();

        return back()->with('success', 'Wallet added successfully.');
    }


    public function updateStatus(AdminWallet $wallet)
    {
        // Toggle the current status
        $newStatus = $wallet->status === 'active' ? 'inactive' : 'active';

        // If toggling to active, make sure no other wallet for same network is active
        if ($newStatus === 'active') {
            AdminWallet::where('network', $wallet->network)
                ->where('id', '!=', $wallet->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);
        }

        // Update current wallet status
        $wallet->status = $newStatus;
        $wallet->save();

        return back()->with('success', "Wallet status changed to {$newStatus} successfully.");
    }
}
