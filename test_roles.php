<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class);
$k->bootstrap();

try {
    $roles = Spatie\Permission\Models\Role::with('permissions')
        ->withCount(['users' => function ($query) {
            $query->where('model_type', config('auth.providers.users.model', App\Models\User::class));
        }])
        ->get();
    echo "Roles OK! Count: " . count($roles) . "\n";
    foreach ($roles as $role) {
        echo "  - {$role->name}: {$role->users_count} users, " . count($role->permissions) . " permissions\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
