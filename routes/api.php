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
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\TaxInvoiceController;
use App\Http\Controllers\Api\InvoicePdfController;
use App\Http\Controllers\Api\InvoiceSummaryController;

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

    Route::apiResource('purchase-orders', PurchaseOrderController::class)->only(['index', 'store', 'show']);
    Route::post('/tax-invoices', [TaxInvoiceController::class, 'store']);
    Route::get('/invoices/{id}/pdf', [InvoicePdfController::class, 'download']);
    
    Route::middleware(['throttle:60,1'])->get('/invoice-summary', [InvoiceSummaryController::class, 'index']);

});