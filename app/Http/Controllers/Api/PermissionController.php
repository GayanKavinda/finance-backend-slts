<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        // Return permissions with both `name` and `slug` (slug = name, required by frontend toggle)
        return Permission::orderBy('name')->get()->map(function ($perm) {
            return [
                'id'   => $perm->id,
                'name' => $perm->name,
                'slug' => $perm->name, // Frontend uses p.slug for toggles
            ];
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $slugName = Str::slug($validated['name']);

        // Check if exists
        if (Permission::where('name', $slugName)->exists()) {
            return response()->json(['message' => "Permission '{$slugName}' already exists."], 422);
        }

        $permission = Permission::create([
            'name' => $slugName,
            'guard_name' => 'web',
        ]);

        // Auto assign new permission to Super Admin and Admin
        $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permission);
        }
        $adminRole = \Spatie\Permission\Models\Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }

        // Reset Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        AuditLogger::log("Created Permission: {$permission->name}", 'Permission', $permission->id);

        return response()->json([
            'id'   => $permission->id,
            'name' => $permission->name,
            'slug' => $permission->name,
            'message' => 'Permission created successfully.',
        ], 201);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        // Protect critical core permissions
        $criticalPermissions = [
            'manage-users',
            'manage-roles',
            'manage-system',
            'view-invoice',
            'create-invoice',
            'edit-invoice',
            'approve-payment',
        ];

        if (in_array($permission->name, $criticalPermissions)) {
            return response()->json(['message' => 'This is a core system permission and cannot be deleted.'], 403);
        }

        $name = $permission->name;
        $permissionId = $permission->id;
        $permission->delete();

        // Reset Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        AuditLogger::log("Deleted Permission: {$name}", 'Permission', $permissionId);

        return response()->json(['message' => "Permission '{$name}' deleted successfully."]);
    }
}

