<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CrossAppLoginToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrossAppLoginController extends Controller
{
    /**
     * Handle the cross-app login token consumption.
     *
     * Validates the token, authenticates the associated user,
     * and redirects to the dashboard.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $tokenString = $request->query('token');

        if (empty($tokenString)) {
            return redirect()->route('login')->with('error', 'Login token is missing.');
        }

        $token = CrossAppLoginToken::where('token', $tokenString)->first();

        if (!$token) {
            return redirect()->route('login')->with('error', 'Login token is invalid.');
        }

        if ($token->isUsed()) {
            return redirect()->route('login')->with('error', 'Login token is invalid.');
        }

        if ($token->isExpired()) {
            return redirect()->route('login')->with('error', 'Login token has expired.');
        }

        $user = $token->user;

        if (!$user) {
            $token->markAsUsed();
            return redirect()->route('login')->with('error', 'Account is unavailable.');
        }

        $token->markAsUsed();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard');
    }
}
