<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contractor_bills', function (Blueprint $table) {
            // Update status enum logic is better handled by changing the string or using a new column if necessary
            // But for visibility we add the missing fields
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();

            // Note: status is already enum('Uploaded', 'Verified', 'Approved')
            // We'll treat 'Uploaded' as 'Draft' for internal mapping if needed, 
            // or we could try to change the enum. Changing enum in SQLite/MySQL can be tricky.
            // Let's add 'Paid' to status if possible.
        });

        // Use raw query to update enum if needed, or just handle in code.
    }

    public function down(): void
    {
        Schema::table('contractor_bills', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'approved_by',
                'approved_at',
                'paid_at',
                'payment_reference',
                'notes'
            ]);
        });
    }
};
