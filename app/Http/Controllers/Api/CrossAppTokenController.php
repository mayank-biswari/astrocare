<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CrossAppLoginToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CrossAppTokenController extends Controller
{
    /**
     * Generate a cross-app login token for the authenticated user.
     *
     * POST /api/auth/cross-app-token
     */
    public function store(Request $request): JsonResponse
    {
        try {
            CrossAppLoginToken::deleteExpired();

            $token = Str::random(64);

            CrossAppLoginToken::create([
                'user_id' => $request->user()->id,
                'token' => $token,
                'expires_at' => now()->addSeconds(60),
            ]);

            return response()->json([
                'token' => $token,
                'redirect_url' => config('app.url') . '/auth/cross-app-login',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to generate token.',
            ], 500);
        }
    }
}
