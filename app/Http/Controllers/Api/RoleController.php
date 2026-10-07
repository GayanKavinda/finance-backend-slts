<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    // Roles that cannot be deleted or have their permissions changed via UI
    const PROTECTED_ROLES = ['Super Admin'];

    public function index()
    {
        $roles = Role::with('permissions')
            ->addSelect([
                'users_count' => DB::table('model_has_roles')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('model_has_roles.role_id', 'roles.id')
                    ->where('model_has_roles.model_type', \App\Models\User::class),
            ])
            ->get();

        // Attach a "protected" flag so the UI can disable editing
        $roles->each(function ($role) {
            $role->is_protected = in_array($role->name, self::PROTECTED_ROLES);
        });

        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:60|unique:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        AuditLogger::log("Created Role: {$role->name}", 'Role', $role->id);

        $role->is_protected = false;
        return response()->json($role->load('permissions'), 201);
    }

    public function show(Role $role)
    {
        $role->is_protected = in_array($role->name, self::PROTECTED_ROLES);
        return $role->load('permissions');
    }

    public function update(Request $request, Role $role)
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');

        // Super Admin role itself cannot be modified
        if ($role->name === 'Super Admin') {
            return response()->json(['message' => 'The Super Admin role is immutable and cannot be modified.'], 403);
        }

        // Only Super Admin can modify the Admin role
        if ($role->name === 'Admin' && !$isSuperAdmin) {
            return response()->json(['message' => 'Only Super Admin can modify the Admin role.'], 403);
        }

        $validated = $request->validate([
            'name'          => 'required|string|max:60|unique:roles,name,' . $role->id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $oldName = $role->name;
        // Keep system role names intact
        if (in_array($role->name, ['Super Admin', 'Admin'])) {
            $validated['name'] = $role->name;
        }

        $role->update(['name' => $validated['name']]);

        if (array_key_exists('permissions', $validated)) {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        // Flush permissions cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        AuditLogger::log(
            "Updated Role: {$oldName} (permissions updated by " . (auth()->user()->name ?? 'User') . ")",
            'Role',
            $role->id
        );

        $role->is_protected = in_array($role->name, self::PROTECTED_ROLES);
        return response()->json($role->load('permissions'));
    }

    public function destroy(Role $role)
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');

        // Super Admin cannot be deleted
        if ($role->name === 'Super Admin') {
            return response()->json(['message' => 'The Super Admin role cannot be deleted.'], 403);
        }

        // Admin role cannot be deleted even if empty
        if ($role->name === 'Admin') {
            return response()->json(['message' => 'The default Admin role cannot be deleted, but permissions can be adjusted.'], 422);
        }

        $assignedUsersCount = DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->where('model_type', \App\Models\User::class)
            ->count();

        if ($assignedUsersCount > 0) {
            return response()->json([
                'message' => "Cannot delete role '{$role->name}' because it has {$assignedUsersCount} active user(s) assigned. Reassign the users first."
            ], 422);
        }

        $roleName = $role->name;
        $roleId   = $role->id;
        $role->delete();

        // Flush permissions cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        AuditLogger::log("Deleted Role: {$roleName}", 'Role', $roleId);

        return response()->json(['message' => 'Role deleted successfully.']);
    }
}
