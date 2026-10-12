<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks write operations across procurement and financial ledgers while
 * maintenance mode is enabled. Super Admins (manage-system) are exempt so
 * they can keep operating and turn maintenance mode back off.
 */
class EnsureNotInMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!SystemSetting::isMaintenanceMode()) {
            return $next($request);
        }

        // Read-only traffic is always allowed (the app must stay browsable).
        if ($request->isMethodSafe()) {
            return $next($request);
        }

        $user = $request->user();

        if ($user && $user->hasPermissionTo('manage-system')) {
            return $next($request);
        }

        return response()->json([
            'message' => 'The system is currently under maintenance. Write operations are temporarily disabled.',
            'maintenance_mode' => true,
        ], 503);
    }
}
