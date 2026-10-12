<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    /**
     * Current user's notification preferences (defaults created on first read).
     */
    public function show(Request $request)
    {
        return response()->json($request->user()->preferences());
    }

    /**
     * Update the current user's notification preferences.
     * Critical alerts cannot be disabled (system / audit safety).
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'email_alerts' => 'sometimes|boolean',
            'browser_alerts' => 'sometimes|boolean',
            'critical_alerts' => 'sometimes|boolean',
        ]);

        // Critical alerts are mandatory regardless of client input.
        $validated['critical_alerts'] = true;

        $prefs = $request->user()->preferences();
        $prefs->update($validated);

        return response()->json([
            'message' => 'Notification preferences updated.',
            'preferences' => $prefs->fresh(),
        ]);
    }
}
