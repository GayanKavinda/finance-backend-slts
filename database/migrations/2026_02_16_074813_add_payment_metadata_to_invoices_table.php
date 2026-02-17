<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('status');
            $table->string('payment_method')->nullable()->after('payment_reference');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
            $table->unsignedBigInteger('recorded_by')->nullable()->after('paid_at');
            $table->text('payment_notes')->nullable()->after('recorded_by');

            $table->foreign('recorded_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
            $table->dropColumn([
                'payment_reference',
                'payment_method',
                'paid_at',
                'recorded_by',
                'payment_notes'
            ]);
        });
    }
};
