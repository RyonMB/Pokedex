<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateTokenRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\TokenResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final class UserAuthController extends Controller
{
    /**
     * Generate a token for a user
     *
     * @return TokenResource
     */
    public function generateToken(GenerateTokenRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials',
            ], 401);
        }

        $token = $user->generateToken($request->expires_at);

        return new TokenResource($token);
    }

    /**
     * Register a new user
     */
    public function register(UserRegistrationRequest $request): TokenResource
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'language' => $request->language,
        ]);

        $token = $user->generateToken($request->expires_at);

        return new TokenResource($token);
    }

    /**
     * Revoke a token
     *
     * @param  string  $tokenId
     * @return JsonResponse
     */
    public function revokeToken(Request $request, $tokenId)
    {
        $request->user()->tokens()->findOrFail($tokenId)->delete();

        return response()->json([
            'message' => 'Revoked Successfully',
        ]);
    }

    /**
     * Revoke all tokens
     *
     * @return JsonResponse
     */
    public function revokeAllTokens(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Revoked All Tokens Successfully',
        ]);
    }
}
