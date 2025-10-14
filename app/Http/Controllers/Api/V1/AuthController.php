<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Http\Requests\V1\LoginRequest;
use Illuminate\Http\Request;
use App\Http\Requests\V1\RegisterRequest;
use App\Mail\PasswordResetOtpMail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{



    public function register(RegisterRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();

            $user = User::create([
                'name'               => $data['name'],
                'email'              => $data['email'],
                'password'           => Hash::make($data['password']),
                'country'            => $data['country'],
                'state'              => $data['state'],
                'phone'              => $data['phone'],
                'username'           => $data['username']
            ]);

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Send email outside transaction for better performance
            Mail::to($data['email'])->queue(new WelcomeMail($user));

            return response()->json([
                'status'  => true,
                'message' => 'User registered successfully',
                'token'   => $token,
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
        });
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

        if ($user->status != 'active') {
            return response()->json([
                'status'  => false,
                'message' => 'Account is not active',
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

    public function createPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4',
        ]);
        $user = $request->user();
        $user->pin = $request->pin;
        $user->save();
        return response()->json([
            'status'  => true,
            'message' => 'Pin created successfully',
            'data'    => [
                'pin'        => $user->pin
            ],
        ]);
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


    public function requestReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $otp = rand(100000, 999999); // 6-digit OTP
        $email = $request->email;

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $otp,
                'created_at' => Carbon::now()
            ]
        );

        // get user by email
        $user = User::where('email', $email)->first();

        Mail::to($email)->queue(new PasswordResetOtpMail($otp, $user->first_name));

        return response()->json(['status' => true, 'message' => 'OTP sent to your email.'], 200);
    }

    // Step 2: Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || $record->token !== $request->otp) {
            return response()->json(['message' => 'Invalid OTP.'], 400);
        }

        // Optional: Check OTP expiration (10 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            return response()->json(['message' => 'OTP expired.'], 400);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully.'
        ], 200);
    }

    // Step 3: Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || $record->token !== $request->otp) {
            return response()->json(['message' => 'Invalid OTP.'], 400);
        }

        if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            return response()->json(['message' => 'OTP expired.'], 400);
        }

        // Update user password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete OTP record
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully.'
        ], 200);
    }
}
