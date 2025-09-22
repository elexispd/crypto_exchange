<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Http\Requests\V1\LoginRequest;
use Illuminate\Http\Request;
use App\Http\Requests\V1\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

    public function register(RegisterRequest $request)
    {

        $data = $request->validated();

        $user = User::create([
            'name'               => $data['name'],
            'email'              => $data['email'],
            'password'           => Hash::make($data['password']),
            'country'            => $data['country'],
            'state'              => $data['state'],
            'pin'                => $data['pin'],
            'phone'              => $data['phone'],
            'username'           => $data['username']
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully',
            'data'    => [
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'username'      => $user->username,
                'country'       => $user->country,
                'state'         => $user->state,
                'phone'         => $user->phone,
                'kyc_status'    => $user->kyc_status,
                'is_Admin'      => $user->is_admin ? true : false,
                'created_at'    => $user->created_at
            ],
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'token'   => $token,
            'data'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'username'   => $user->username,
                'country'    => $user->country,
                'state'      => $user->state,
                'pin'        => $user->pin,
                'phone'      => $user->phone,
                'email'      => $user->email,
                'is_Admin'   => $user->is_admin ? true : false,
                'kyc_status' => $user->kyc_status,
                'created_at' => $user->created_at,
            ],
        ]);
    }


    public function verifySecretPhrase(Request $request)
    {
        $request->validate([
            'secret_phrase' => 'required|array|min:3',
            'secret_phrase.*' => 'string',
        ]);

        $user = $request->user();
        $phraseString = implode(' ', $request->secret_phrase);

        $isValid = Hash::check($phraseString, $user->secret_phrase_hash);

        return response()->json([
            'status'  => $isValid,
            'message' => $isValid ? 'Secret phrase verified successfully' : 'Secret phrase does not match',
        ], $isValid ? 200 : 401);
    }

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        $user = $request->user();

        if ($user->pin != $request->pin) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid pin',
            ], 401);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Pin verified successfully',
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully',
        ]);
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json([
            'status' => $status === Password::RESET_LINK_SENT,
            'message' => __($status),
        ], $status === Password::RESET_LINK_SENT ? 200 : 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return response()->json([
            'status' => $status === Password::PASSWORD_RESET,
            'message' => __($status),
        ], $status === Password::PASSWORD_RESET ? 200 : 400);
    }




}
