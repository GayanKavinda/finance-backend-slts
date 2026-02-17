<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add finance workflow columns to invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('submitted_by')->nullable()->after('recorded_by')->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable()->after('submitted_by');
            $table->foreignId('approved_by')->nullable()->after('submitted_at')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->foreignId('rejected_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
        });

        // Update status enum to include Approved and Rejected
        // Using raw DB statement because Schema::table enum update can be tricky in some versions
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Draft', 'Tax Generated', 'Submitted', 'Approved', 'Rejected', 'Paid') NOT NULL DEFAULT 'Draft'");
        }

        // Create invoice status history table for audit trail
        Schema::create('invoice_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('invoice_id');
            $table->index('changed_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop invoice status history table
        Schema::dropIfExists('invoice_status_history');

        // Revert status enum (Careful: this will fail if invoices already have 'Approved' or 'Rejected' statuses)
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Draft', 'Tax Generated', 'Submitted', 'Paid') NOT NULL DEFAULT 'Draft'");

        // Remove finance workflow columns
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn([
                'submitted_by',
                'submitted_at',
                'approved_by',
                'approved_at',
                'rejected_by',
                'rejected_at',
                'rejection_reason'
            ]);
        });
    }
};
