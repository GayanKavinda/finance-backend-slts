<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /**
     * Return paginated audit logs with user info.
     * Accessible to Admin and Super Admin.
     */
    public function index(Request $request)
    {
        $query = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select([
                'audit_logs.id',
                'audit_logs.action',
                'audit_logs.entity_type',
                'audit_logs.entity_id',
                'audit_logs.timestamp',
                'users.id as user_id',
                'users.name as user_name',
                'users.email as user_email',
            ])
            ->orderByDesc('audit_logs.timestamp');

        // Search filter
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('audit_logs.action', 'like', "%{$q}%")
                    ->orWhere('audit_logs.entity_type', 'like', "%{$q}%")
                    ->orWhere('users.name', 'like', "%{$q}%")
                    ->orWhere('users.email', 'like', "%{$q}%");
            });
        }

        // Action filter
        if ($request->filled('action')) {
            $query->where('audit_logs.action', $request->action);
        }

        // Entity type filter
        if ($request->filled('entity_type')) {
            $query->where('audit_logs.entity_type', $request->entity_type);
        }

        // Date range
        if ($request->filled('from')) {
            $query->whereDate('audit_logs.timestamp', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('audit_logs.timestamp', '<=', $request->to);
        }

        $perPage = (int) ($request->per_page ?? 25);
        $results = $query->paginate(min($perPage, 100));

        return response()->json($results);
    }

    /**
     * Return distinct actions and entity types for filter dropdowns.
     */
    public function meta()
    {
        $actions = DB::table('audit_logs')
            ->distinct()
            ->pluck('action')
            ->sort()
            ->values();

        $entityTypes = DB::table('audit_logs')
            ->distinct()
            ->pluck('entity_type')
            ->sort()
            ->values();

        return response()->json([
            'actions'      => $actions,
            'entity_types' => $entityTypes,
        ]);
    }
}
