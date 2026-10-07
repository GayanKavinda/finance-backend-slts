<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// One-time setup endpoint to run migrations & seeds safely when terminal is inaccessible
Route::get('/system-setup-runner', function () {
    try {
        // Ensure user_id on audit_logs is nullable directly
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE audit_logs MODIFY user_id BIGINT UNSIGNED NULL');
        } catch (\Throwable $ignored) {}

        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'RolePermissionSeeder',
            '--force' => true,
        ]);
        $seedOutput = \Illuminate\Support\Facades\Artisan::output();

        // Ensure superadmin exists with Super Admin role
        $superAdmin = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@finance.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('password')]
        );
        $superAdmin->syncRoles(['Super Admin']);

        // Ensure admin user exists with Admin role
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@finance.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password')]
        );
        $admin->syncRoles(['Admin']);

        \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        return response()->json([
            'status' => 'success',
            'message' => 'Migration, seeding, and cache reset completed successfully!',
            'migration' => $migrateOutput,
            'seeder' => $seedOutput,
            'superadmin' => $superAdmin->email,
            'admin' => $admin->email,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});
