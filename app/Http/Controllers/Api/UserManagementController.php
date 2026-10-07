<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * List users. Admin cannot see Super Admin users.
     * Super Admin can see everyone.
     */
    public function index(Request $request)
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');

        $query = User::withTrashed()
            ->with('roles:id,name')
            ->withCount('loginActivities')
            ->select(['id', 'name', 'email', 'avatar_path', 'created_at', 'deleted_at'])
            ->latest();

        // Admin cannot see Super Admin users
        if (!$isSuperAdmin) {
            $query->whereDoesntHave('roles', fn($q) => $q->where('name', 'Super Admin'));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        return response()->json($query->paginate(15));
    }

    /**
     * Show a single user with full details.
     */
    public function show($id)
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');

        $user = User::withTrashed()
            ->with(['roles:id,name'])
            ->withCount('loginActivities')
            ->findOrFail($id);

        // Admin cannot view Super Admin users
        if (!$isSuperAdmin && $user->hasRole('Super Admin')) {
            abort(403, 'Access denied.');
        }

        $lastLogin = $user->loginActivities()
            ->where('status', 'success')
            ->latest('created_at')
            ->first(['ip_address', 'browser', 'platform', 'created_at']);

        return response()->json([
            'user'       => $user,
            'last_login' => $lastLogin,
        ]);
    }

    /**
     * Assign a role to a user.
     * Admin cannot assign or remove Super Admin role.
     */
    public function assignRole(Request $request, $id)
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');

        $data = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($id);

        // Prevent changing own role
        if ($user->id === auth()->id()) {
            abort(422, 'You cannot change your own role.');
        }

        // Admin cannot manage Super Admin users
        if (!$isSuperAdmin && ($user->hasRole('Super Admin') || $data['role'] === 'Super Admin')) {
            abort(403, 'You cannot assign or manage the Super Admin role.');
        }

        $oldRole = $user->roles->pluck('name')->implode(', ') ?: 'None';
        $user->syncRoles([$data['role']]);

        AuditLogger::log(
            "Assigned Role: {$oldRole} → {$data['role']}",
            'User',
            $user->id
        );

        return response()->json([
            'message' => "Role '{$data['role']}' assigned to {$user->name}.",
            'user'    => $user->fresh()->load('roles:id,name'),
        ]);
    }

    /**
     * Deactivate (soft delete) a user.
     */
    public function deactivate($id)
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');

        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            abort(422, 'You cannot deactivate your own account.');
        }

        // Admin cannot deactivate Super Admin users
        if (!$isSuperAdmin && $user->hasRole('Super Admin')) {
            abort(403, 'You cannot deactivate a Super Admin account.');
        }

        $user->tokens()->delete();
        $user->delete();

        AuditLogger::log('Deactivated User', 'User', $user->id);

        return response()->json(['message' => "{$user->name} has been deactivated."]);
    }

    /**
     * Reactivate a previously deactivated user.
     */
    public function reactivate($id)
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');

        $user = User::withTrashed()->findOrFail($id);

        if (!$user->trashed()) {
            abort(422, 'User is already active.');
        }

        // Admin cannot reactivate Super Admin users
        if (!$isSuperAdmin && $user->hasRole('Super Admin')) {
            abort(403, 'You cannot reactivate a Super Admin account.');
        }

        $user->restore();

        AuditLogger::log('Reactivated User', 'User', $user->id);

        return response()->json([
            'message' => "{$user->name} has been reactivated.",
            'user'    => $user->fresh()->load('roles:id,name'),
        ]);
    }

    /**
     * Permanently delete a user.
     */
    public function permanentDelete($id)
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');

        $user = User::withTrashed()->findOrFail($id);

        if ($user->id === auth()->id()) {
            abort(422, 'You cannot permanently delete your own account from here.');
        }

        // Admin cannot permanently delete Super Admin users
        if (!$isSuperAdmin && $user->hasRole('Super Admin')) {
            abort(403, 'You cannot delete a Super Admin account.');
        }

        $userName = $user->name;
        $userId   = $user->id;

        $user->tokens()->delete();
        $user->forceDelete();

        AuditLogger::log("Permanently Deleted User: {$userName}", 'User', $userId);

        return response()->json(['message' => "{$userName} has been permanently deleted."]);
    }
}
