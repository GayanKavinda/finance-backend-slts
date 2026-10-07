<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Log an action to the audit_logs table.
     *
     * @param  string       $action      e.g. "Created Invoice", "Assigned Role"
     * @param  string       $entityType  e.g. "Invoice", "User", "Role"
     * @param  int|null     $entityId    The primary key of the affected entity
     * @param  int|null     $userId      Override user (defaults to authenticated user)
     */
    public static function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?int $userId = null
    ): void {
        try {
            AuditLog::create([
                'user_id'     => $userId ?? Auth::id(),
                'action'      => $action,
                'entity_type' => $entityType,
                'entity_id'   => $entityId ?? 0,
                'timestamp'   => now(),
            ]);
        } catch (\Throwable $e) {
            // Never let audit logging break the main request
            \Illuminate\Support\Facades\Log::warning('AuditLogger failed: ' . $e->getMessage());
        }
    }
}
