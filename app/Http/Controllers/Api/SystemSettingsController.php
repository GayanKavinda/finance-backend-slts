<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    /**
     * Maintenance mode state — readable by any authenticated user so the
     * frontend can show a banner and explain blocked writes.
     */
    public function maintenanceStatus()
    {
        return response()->json([
            'maintenance_mode' => SystemSetting::isMaintenanceMode(),
        ]);
    }

    /**
     * Toggle maintenance mode (Super Admin only).
     */
    public function updateMaintenanceMode(Request $request)
    {
        $validated = $request->validate([
            'maintenance_mode' => 'required|boolean',
        ]);

        SystemSetting::set(SystemSetting::KEY_MAINTENANCE_MODE, (bool) $validated['maintenance_mode']);

        return response()->json([
            'message' => $validated['maintenance_mode']
                ? 'Maintenance mode enabled. Write operations are now blocked for all non-super-admin users.'
                : 'Maintenance mode disabled. All operations restored.',
            'maintenance_mode' => (bool) $validated['maintenance_mode'],
        ]);
    }
}
