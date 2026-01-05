<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('password');
            $table->timestamp('profile_updated_at')->nullable()->after('avatar_path');
            $table->string('profile_updated_by')->nullable()->after('profile_updated_at');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_path', 'profile_updated_at', 'profile_updated_by']);
            $table->dropSoftDeletes();
        });
    }
};