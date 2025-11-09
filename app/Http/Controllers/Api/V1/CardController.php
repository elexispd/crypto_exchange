<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\CardVariation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    /**
     * Create a new card for a user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'card_type' => 'required|exists:card_variations,id',
        ]);

        // Generate unique card number
        $cardNumber = $this->generateUniqueCardNumber();

        // Generate expiry date (4 years ahead)
        $expiryMonth = date('m');
        $expiryYear = date('Y', strtotime('+4 years'));

        $user = Auth::user();

        // Check if user already has a card of the same type
        if (Card::where('user_id', $user->id)
            ->where('card_variation_id', $validated['card_type'])
            ->exists()
        ) {
            return response()->json([
                'status' => false,
                'message' => 'You already have a card of this type.',
            ], 400);
        }

        // Create card record
        $card = Card::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'card_variation_id' => $validated['card_type'],
            'card_number' => $cardNumber,
            'card_name' => $user->name,
            'cvv' => rand(100, 999),
            'expiry_month' => $expiryMonth,
            'expiry_year' => $expiryYear,
            'is_frozen' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Card created successfully',
            'data' => $card,
        ], 201);
    }

    public function show(Request $request)
    {
        $cards = Card::where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $cards,
        ]);
    }

    public function freeze(Request $request)
    {
        $request->validate([
            'card_id' => 'required|uuid|exists:cards,id',
        ]);

        $card = Card::where('id', $request->card_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$card) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid card',
            ], 400);
        }

        $card->is_frozen = true;
        $card->save();

        return response()->json([
            'status' => true,
            'message' => 'Card frozen successfully',
            'data' => $card,
        ], 200);
    }

    public function unfreeze(Request $request)
    {
        $request->validate([
            'card_id' => 'required|uuid|exists:cards,id'
        ]);
        $card = Card::where('id', $request->card_id)
            ->first();

        if (!$card) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid card',
            ], 400);
        }

        $card->is_frozen = false;
        $card->save();

        return response()->json([
            'status' => true,
            'message' => 'Card unfrozen successfully',
            'data' => $card,
        ], 200);
    }

    public function destroy($card_id)
    {
        $card = Card::find($card_id);

        if (!$card) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid card',
            ], 400);
        }

        $card->delete();

        return response()->json([
            'status' => true,
            'message' => 'Card deleted successfully',
            'data' => $card,
        ], 200);
    }

    public function fundingSource(Request $request)
    {
        $validated = $request->validate([
            'card_id' => 'required|uuid|exists:cards,id',
            'source' => 'required|string|in:btc,eth,xrp,sol',
        ]);
        // update fund_source column of card.
        $card = Card::findOrFail($request->card_id);
        $card->fund_source = $validated['source'];
        $card->save();
        return response()->json([
            'status' => true,
            'message' => 'Card fund source updated successfully',
            'data' => $card,
        ], 200);
    }


    public function variations()
    {
        $variations = CardVariation::all();
        return response()->json([
            'status' => true,
            'message' => 'Card variations retrieved successfully',
            'data' => $variations,
        ], 200);
    }

    /**
     * Generate a unique card number.
     *
     * Format example: 5162 45XX XXXX XXXX
     */
    private function generateUniqueCardNumber()
    {
        do {
            // Example: 16-digit card number starting with 5162
            $cardNumber = '5162' . mt_rand(100000000000, 999999999999);
        } while (
            DB::table('cards')->where('card_number', $cardNumber)->exists()
        );

        return $cardNumber;
    }
}
