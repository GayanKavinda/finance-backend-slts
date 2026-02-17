<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    /**
     * List all users with their roles.
     * Includes soft-deleted users so admins can see deactivated accounts.
     */
    public function index(Request $request)
    {
        $query = User::withTrashed()
            ->with('roles:id,name')
            ->withCount('loginActivities')
            ->select(['id', 'name', 'email', 'avatar_path', 'created_at', 'deleted_at'])
            ->latest();

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
        $user = User::withTrashed()
            ->with(['roles:id,name'])
            ->withCount('loginActivities')
            ->findOrFail($id);

        // Last login
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
     * Assign a role to a user (replaces existing role).
     * One user = one role in this system.
     */
    public function assignRole(Request $request, $id)
    {
        $data = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($id);

        // Prevent admin from changing their own role
        if ($user->id === auth()->id()) {
            abort(422, 'You cannot change your own role.');
        }

        $user->syncRoles([$data['role']]);

        return response()->json([
            'message' => "Role '{$data['role']}' assigned to {$user->name}.",
            'user'    => $user->fresh()->load('roles:id,name'),
        ]);
    }

    /**
     * Get all available roles (for the role assignment dropdown).
     */
    public function roles()
    {
        $roles = Role::select('id', 'name')->get();
        return response()->json($roles);
    }

    /**
     * Deactivate (soft delete) a user.
     */
    public function deactivate($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            abort(422, 'You cannot deactivate your own account.');
        }

        // Revoke all tokens so they are immediately logged out
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => "{$user->name} has been deactivated.",
        ]);
    }

    /**
     * Reactivate a previously deactivated user.
     */
    public function reactivate($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (!$user->trashed()) {
            abort(422, 'User is already active.');
        }

        $user->restore();

        return response()->json([
            'message' => "{$user->name} has been reactivated.",
            'user'    => $user->fresh()->load('roles:id,name'),
        ]);
    }
}
