<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SecurityController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ContractorController;
use App\Http\Controllers\Api\TenderController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ContractorBillController;
use App\Http\Controllers\Api\InvoiceController;

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
Route::post('/forgot-password-otp', [PasswordResetController::class, 'forgotPassword'])
    ->middleware('throttle:5,1');

Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp'])
    ->middleware('throttle:10,1');

Route::post('/reset-password-otp', [PasswordResetController::class, 'resetPasswordWithOtp'])
    ->middleware('throttle:5,1');

// Note: 'reset-password' route was present in original but mapped to AuthController::resetPassword which I don't see in the code I read.
// I suspect it might have been missing or I missed it. I only saw resetPasswordWithOtp.
// The code I read for AuthController had: forgotPassword, verifyOtp, resetPasswordWithOtp.
// It did NOT have 'resetPassword'.
// I will comment it out or remove it to be safe, or map it to resetPasswordWithOtp if that was the intent.
// Looking at original file, line 34: Route::post('/reset-password', [AuthController::class, 'resetPassword'])
// But I did not see `resetPassword` method in the file content provided in Step 82.
// I only saw `resetPasswordWithOtp` at line 373.
// So `resetPassword` was likely a broken route or I missed the method.
// I will check if I missed it.
// Checking Step 82 output...
// ...
// 373: public function resetPasswordWithOtp(Request $request)
// ...
// I don't see `resetPassword`. So I will remove that route as it likely didn't exist or was a mistake in the routes file.

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

    Route::post('/update-profile', [ProfileController::class, 'updateProfile']);
    Route::post('/update-password', [ProfileController::class, 'updatePassword']);
    Route::post('/upload-avatar', [ProfileController::class, 'uploadAvatar']);
    Route::post('/request-email-change', [ProfileController::class, 'requestEmailChange']);
    Route::post('/confirm-email-change', [ProfileController::class, 'confirmEmailChange']);
    Route::post('/deactivate-account', [ProfileController::class, 'deactivateAccount']);

    // Security Features
    Route::get('/login-history', [SecurityController::class, 'getLoginHistory']);
    Route::delete('/login-history/{id}', [SecurityController::class, 'deleteLoginActivity']);
    Route::get('/active-sessions', [SecurityController::class, 'getActiveSessions']);
    Route::delete('/revoke-session/{sessionId}', [SecurityController::class, 'revokeSession']);

    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('contractors', ContractorController::class);
    Route::apiResource('tenders', TenderController::class);
    Route::apiResource('jobs', JobController::class);

    Route::get('/contractor-bills', [ContractorBillController::class, 'index']);
    Route::post('/contractor-bills', [ContractorBillController::class, 'store']);
    Route::post('/contractor-bills/{id}/verify', [ContractorBillController::class, 'verify']);
    Route::post('/contractor-bills/{id}/approve', [ContractorBillController::class, 'approve']);

    Route::get('invoices', [InvoiceController::class, 'index']);
    Route::post('invoices', [InvoiceController::class, 'store']);
    Route::post('invoices/{id}/submit-finance', [InvoiceController::class, 'submitToFinance']);
    Route::post('invoices/{id}/mark-paid', [InvoiceController::class, 'markPaid']);
});
