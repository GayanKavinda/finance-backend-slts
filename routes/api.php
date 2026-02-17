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
use App\Http\Controllers\Api\NotificationController;

Route::post('/check-email-exists', [AuthController::class, 'checkEmailExists'])
    ->middleware('throttle:30,1');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:10,1');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1');

Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/forgot-password-otp', [PasswordResetController::class, 'forgotPassword'])
    ->middleware('throttle:5,1');

Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp'])
    ->middleware('throttle:10,1');

Route::post('/reset-password-otp', [PasswordResetController::class, 'resetPasswordWithOtp'])
    ->middleware('throttle:5,1');

Route::middleware(['auth:sanctum'])->group(function () {

    // ── User & Profile ────────────────────────────────────────────

    Route::get('/user', function (Request $request) {
        $u = $request->user()->load('roles');
        return response()->json([
            'id'          => $u->id,
            'name'        => $u->name,
            'email'       => $u->email,
            'avatar_url'  => $u->avatar_path ? '/storage/' . $u->avatar_path : null,
            'roles'       => $u->roles->pluck('name'),
            'permissions' => $u->getAllPermissions()->pluck('name'),
        ]);
    });

    Route::get('/profile', function (Request $request) {
        $u = $request->user()->load('roles');
        return response()->json([
            'id'          => $u->id,
            'name'        => $u->name,
            'email'       => $u->email,
            'avatar_url'  => $u->avatar_path ? '/storage/' . $u->avatar_path : null,
            'roles'       => $u->roles->pluck('name'),
            'permissions' => $u->getAllPermissions()->pluck('name'),
        ]);
    });

    Route::post('/update-profile',       [ProfileController::class, 'updateProfile']);
    Route::post('/update-password',      [ProfileController::class, 'updatePassword']);
    Route::post('/upload-avatar',        [ProfileController::class, 'uploadAvatar']);
    Route::post('/request-email-change', [ProfileController::class, 'requestEmailChange']);
    Route::post('/confirm-email-change', [ProfileController::class, 'confirmEmailChange']);
    Route::post('/deactivate-account',   [ProfileController::class, 'deactivateAccount']);

    // ── Security ──────────────────────────────────────────────────

    Route::get('/login-history',                 [SecurityController::class, 'getLoginHistory']);
    Route::delete('/login-history/{id}',         [SecurityController::class, 'deleteLoginActivity']);
    Route::get('/active-sessions',               [SecurityController::class, 'getActiveSessions']);
    Route::delete('/revoke-session/{sessionId}', [SecurityController::class, 'revokeSession']);

    // ── Master Data ───────────────────────────────────────────────

    Route::apiResource('customers',   CustomerController::class);
    Route::apiResource('contractors', ContractorController::class);
    Route::apiResource('tenders',     TenderController::class);
    Route::apiResource('jobs',        JobController::class);

    // ── Contractor Bills ──────────────────────────────────────────

    Route::get('/contractor-bills',               [ContractorBillController::class, 'index']);
    Route::post('/contractor-bills',              [ContractorBillController::class, 'store']);
    Route::post('/contractor-bills/{id}/verify',  [ContractorBillController::class, 'verify']);
    Route::post('/contractor-bills/{id}/approve', [ContractorBillController::class, 'approve']);

    // ── Purchase Orders & Tax ─────────────────────────────────────

    Route::apiResource('purchase-orders', PurchaseOrderController::class)->only(['index', 'store', 'show']);
    Route::post('/tax-invoices', [TaxInvoiceController::class, 'store']);

    // ── Invoices ──────────────────────────────────────────────────
    // Static paths BEFORE parameterized {id} routes.

    Route::get('invoices/status-breakdown', [InvoiceController::class, 'statusBreakdown'])
        ->middleware('can:view-invoice');

    Route::get('invoices/monthly-trend', [InvoiceController::class, 'monthlyTrend'])
        ->middleware('can:view-invoice');

    Route::get('/invoice-summary', [InvoiceController::class, 'summary'])
        ->middleware('can:view-invoice');

    Route::get('invoices', [InvoiceController::class, 'index'])
        ->middleware('can:view-invoice');

    Route::post('invoices', [InvoiceController::class, 'store'])
        ->middleware('can:create-invoice');

    Route::put('invoices/{id}', [InvoiceController::class, 'update'])
        ->middleware('can:edit-invoice');

    Route::post('invoices/{id}/submit-to-finance', [InvoiceController::class, 'submitToFinance'])
        ->middleware('can:submit-invoice');

    Route::post('invoices/{id}/approve', [InvoiceController::class, 'approveInvoice'])
        ->middleware('can:approve-payment');

    Route::post('invoices/{id}/reject', [InvoiceController::class, 'rejectInvoice'])
        ->middleware('can:reject-invoice');

    Route::post('invoices/{id}/mark-paid', [InvoiceController::class, 'markPaid'])
        ->middleware('can:approve-payment');

    Route::get('invoices/{id}/audit-trail', [InvoiceController::class, 'getAuditTrail'])
        ->middleware('can:view-audit-trail');

    Route::get('/invoices/{id}/pdf', [InvoicePdfController::class, 'download']);

    // ── Notifications ─────────────────────────────────────────────
    //
    // ⚠️  Same ordering rule applies here:
    //     /notifications/unread   — static, must come FIRST
    //     /notifications/read-all — static, must come FIRST
    //     /notifications/{id}/... — parameterized, must come AFTER
    //
    // If {id} routes appear first, Laravel matches "unread" and "read-all"
    // as notification IDs, returning 404 "notification not found".

    Route::get('/notifications',             [NotificationController::class, 'index']);

    // Static sub-routes BEFORE {id} sub-routes
    Route::get('/notifications/unread',      [NotificationController::class, 'unread']);
    Route::post('/notifications/read-all',   [NotificationController::class, 'markAllAsRead']);

    // Parameterized sub-routes AFTER
    Route::post('/notifications/{id}/read',  [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}',     [NotificationController::class, 'destroy']);

    // ── Admin: User Management ────────────────────────────────────

    Route::prefix('admin')->middleware('can:manage-users')->group(function () {
        Route::get('/users',                    [\App\Http\Controllers\Api\UserManagementController::class, 'index']);
        Route::get('/users/{id}',               [\App\Http\Controllers\Api\UserManagementController::class, 'show']);
        Route::post('/users/{id}/assign-role',  [\App\Http\Controllers\Api\UserManagementController::class, 'assignRole']);
        Route::delete('/users/{id}/deactivate', [\App\Http\Controllers\Api\UserManagementController::class, 'deactivate']);
        Route::post('/users/{id}/reactivate',   [\App\Http\Controllers\Api\UserManagementController::class, 'reactivate']);
        Route::get('/roles',                    [\App\Http\Controllers\Api\UserManagementController::class, 'roles']);
    });
});
