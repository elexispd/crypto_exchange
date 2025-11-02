<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CardController extends Controller
{
    /**
     * Display user cards
     */
    public function show(User $user)
    {
        $cards = $user->card()->latest()->get();

        return view('users.cards', compact('user', 'cards'));
    }

    /**
     * Toggle freeze status of a card
     */
    public function toggleFreeze(Request $request, Card $card)
    {
        try {
            $card->update([
                'is_frozen' => !$card->is_frozen,
                'updated_at' => now()
            ]);

            $status = $card->is_frozen ? 'frozen' : 'unfrozen';

            return back()->with('success', 'Card status updated successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Failed to update card status']);
        }
    }

    /**
     * Delete a card
     */
    public function destroy(Card $card)
    {
        try {
            $cardNumber = $card->card_number;
            $card->delete();

            return back()->with('success', "Card ending with " . substr($cardNumber, -4) . " has been deleted successfully.");

        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Failed to delete card']);
        }
    }

    /**
     * Create a new card for user
     */
    public function store(Request $request, User $user)
    {
        $request->validate([
            'card_type' => 'required|string',
            'fund_source' => 'required|string'
        ]);

        try {
            $card = $user->card()->create([
                'card_number' => $this->generateCardNumber(),
                'card_name' => $user->name,
                'cvv' => sprintf("%03d", rand(1, 999)),
                'expiry_month' => sprintf("%02d", rand(1, 12)),
                'expiry_year' => date('Y') + rand(1, 5),
                'card_variation_id' => \Illuminate\Support\Str::uuid(),
                'is_frozen' => 0,
                'balance' => 0.00,
                'fund_source' => $request->fund_source,
            ]);

            return back()->with('success', 'Card created successfully.')->with('card', $card);

        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Failed to create card']);
        }
    }

    /**
     * Generate a random card number
     */
    private function generateCardNumber(): string
    {
        // Generate 16-digit card number starting with common prefixes
        $prefixes = ['4', '5', '3'];
        $prefix = $prefixes[array_rand($prefixes)];

        $number = $prefix;
        for ($i = 0; $i < 15; $i++) {
            $number .= rand(0, 9);
        }

        return $number;
    }

    /**
     * Update card balance
     */
    public function updateBalance(Request $request, Card $card): JsonResponse
    {
        $request->validate([
            'balance' => 'required|numeric|min:0'
        ]);

        try {
            $card->update([
                'balance' => $request->balance,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Card balance updated successfully',
                'balance' => $card->balance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update card balance'
            ], 500);
        }
    }

    /**
     * Get card transactions
     */
    public function transactions(Card $card)
    {
        $transactions = $card->transactions()->latest()->get();

        return view('cards.transactions', compact('card', 'transactions'));
    }
}
