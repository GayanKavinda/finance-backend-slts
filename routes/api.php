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

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,1');

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user', function (Request $request) {
            $u = $request->user();
            if (!$u) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return response()->json([
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'avatar_url' => ($u->avatar_path ? '/storage/' . $u->avatar_path : null),
                'profile_updated_at' => $u->profile_updated_at,
                'profile_updated_by' => $u->profile_updated_by,
            ]);
        });

        Route::post('/update-profile', [AuthController::class, 'updateProfile']);
        Route::post('/update-password', [AuthController::class, 'updatePassword']);
        Route::post('/upload-avatar', [AuthController::class, 'uploadAvatar']);
        Route::post('/request-email-change', [AuthController::class, 'requestEmailChange']);
        Route::post('/confirm-email-change', [AuthController::class, 'confirmEmailChange']);
        Route::post('/deactivate-account', [AuthController::class, 'deactivateAccount']);

        // Security Features
        Route::get('/login-history', [AuthController::class, 'getLoginHistory']);
        Route::get('/active-sessions', [AuthController::class, 'getActiveSessions']);
        Route::delete('/revoke-session/{sessionId}', [AuthController::class, 'revokeSession']);
    });
});
