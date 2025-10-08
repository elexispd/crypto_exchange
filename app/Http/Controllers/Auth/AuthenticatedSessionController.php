<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */

    public function create(): View
    {
        return view('login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $user = Auth::user();

        if ($user->status === 'inactive') {
            // Log the user out immediately
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Return with error message
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support at '. config('emails.support'),
            ]);
        }
        // Proceed with normal login for active users
        $request->session()->regenerate();

        if ($user->is_admin) {
            return redirect()->intended(route('dashboard'));
        } else {
            return back()->withErrors([
                'error' => 'Authorized Access',
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
