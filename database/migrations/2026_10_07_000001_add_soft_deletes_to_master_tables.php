<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add soft delete (deleted_at) columns to all master entity tables.
     * Affects: customers, contractors, tenders, project_jobs
     */
    public function up(): void
    {
        if (!Schema::hasColumn('customers', 'deleted_at')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('contractors', 'deleted_at')) {
            Schema::table('contractors', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('tenders', 'deleted_at')) {
            Schema::table('tenders', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('project_jobs', 'deleted_at')) {
            Schema::table('project_jobs', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('contractors', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('tenders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('project_jobs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
