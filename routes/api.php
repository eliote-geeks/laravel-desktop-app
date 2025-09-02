<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::prefix('v1')->group(function () {
    Route::middleware(['throttle:5,1'])->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });
    
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        Route::get('/dashboard', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $request->user(),
                    'stats' => [
                        'total_users' => \App\Models\User::count(),
                        'redis_status' => app(\App\Services\CacheService::class)->getRedisInfo(),
                    ]
                ]
            ]);
        });
    });
});