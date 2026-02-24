<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support MODIFY COLUMN for ENUMs easily, so we handle it gracefully.
        // For MySQL, we would use ALTER TABLE.

        Schema::table('contractor_bills', function (Blueprint $table) {
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('submitted_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
        });

        // Update status enum if on MySQL
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE contractor_bills MODIFY COLUMN status ENUM('Draft', 'Verified', 'Submitted', 'Approved', 'Paid', 'Rejected') NOT NULL DEFAULT 'Draft'");
        }
    }

    public function down(): void
    {
        Schema::table('contractor_bills', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn([
                'submitted_by',
                'submitted_at',
                'rejected_by',
                'rejected_at',
                'rejection_reason'
            ]);
        });
    }
};
