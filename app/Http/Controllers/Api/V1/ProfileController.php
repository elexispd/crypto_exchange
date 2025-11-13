<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                'wallet' => $user->wallet,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20|unique:users,phone,' . $user->id,
            'country' => 'sometimes|nullable|string|max:100',
            'state' => 'sometimes|nullable|string|max:100',
            'zip' => 'sometimes|nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update only the fields that are present in the request
        $updateData = [];
        $fields = ['name', 'email', 'phone', 'country', 'state', 'address', 'city', 'zip'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }

        $user->update($updateData);
        $user->refresh();

        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'data'    => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'state' => $user->state,
                    'zip' => $user->zip,
                    'country' => $user->country,
                ],
                'wallet' => $user->wallet,
            ],
        ]);
    }
}
