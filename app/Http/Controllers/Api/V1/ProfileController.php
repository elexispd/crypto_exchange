<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Profile retrieved successfully',
            'data'    => [
                'user' => $user->only(['id', 'name', 'email', 'phone', 'address', 'city', 'state', 'zip', 'country']),
                'wallet' => $user->wallet->except(['secret_phrase']),
            ],
        ]);
    }
}
