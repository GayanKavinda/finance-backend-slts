<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\LoginActivity;

class SecurityController extends Controller
{
    public function getLoginHistory(Request $request)
    {
        return response()->json(
            $request->user()->loginActivities()->orderBy('created_at', 'desc')->paginate(5)
        );
    }

    public function deleteLoginActivity(Request $request, $id)
    {
        $request->user()->loginActivities()->where('id', $id)->delete();
        return response()->json(['message' => 'Login activity removed']);
    }

    public function getActiveSessions(Request $request)
    {
        $sessions = DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->get(['id', 'ip_address', 'user_agent', 'last_activity']);

        // Format for frontend
        $formatted = $sessions->map(function ($session) use ($request) {
            return [
                'id' => $session->id,
                'is_current' => $session->id === $request->session()->getId(),
                'ip_address' => $session->ip_address,
                'user_agent' => $session->user_agent,
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->toIso8601String(),
            ];
        });

        return response()->json($formatted);
    }

    public function revokeSession(Request $request, $sessionId)
    {
        DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->where('id', $sessionId)
            ->delete();

        return response()->json(['message' => 'Session revoked']);
    }
}
