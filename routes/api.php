<?php

use App\Http\Controllers\Api\UserAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('token', [UserAuthController::class, 'generateToken']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('revoke/{tokenId}', [UserAuthController::class, 'revokeToken']);
        Route::post('revoke-all', [UserAuthController::class, 'revokeAllTokens']);
    })->middleware('auth:sanctum');
});
