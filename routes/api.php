<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware(['web'])->group(function () {

    // ✅ Fast email existence check (higher rate limit - used while typing)
    Route::post('/check-email-exists', [AuthController::class, 'checkEmailExists'])
        ->middleware('throttle:30,1'); // 30 requests per minute

    // Registration with full MX validation
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:10,1');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1');

    Route::post('/logout', [AuthController::class, 'logout']);

    // OTP Password Reset Routes
    Route::post('/forgot-password-otp', [AuthController::class, 'forgotPassword'])
        ->middleware('throttle:5,1');

    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])
        ->middleware('throttle:10,1');

    Route::post('/reset-password-otp', [AuthController::class, 'resetPasswordWithOtp'])
        ->middleware('throttle:5,1');

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::post('/update-profile', [AuthController::class, 'updateProfile']);
        Route::post('/update-password', [AuthController::class, 'updatePassword']);
    });
});
