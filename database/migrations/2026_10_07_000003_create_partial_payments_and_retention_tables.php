<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates:
     * 1. invoice_payments (Tracks customer partial/milestone payments & retainage)
     * 2. contractor_bill_payments (Tracks disbursements/installments to contractors & retainage)
     */
    public function up(): void
    {
        if (!Schema::hasTable('invoice_payments')) {
            Schema::create('invoice_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
                $table->decimal('amount', 15, 2);
                $table->decimal('retention_amount', 15, 2)->default(0.00);
                $table->string('milestone_name')->nullable(); // e.g. "Advance Payment", "Milestone 1", "Final Settlement", "Retention Release"
                $table->string('payment_method')->default('Cheque'); // Cheque, Bank Transfer, Cash
                $table->string('cheque_number')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('receipt_number')->nullable()->unique();
                $table->date('payment_date');
                $table->text('notes')->nullable();
                $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('contractor_bill_payments')) {
            Schema::create('contractor_bill_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contractor_bill_id')->constrained('contractor_bills')->cascadeOnDelete();
                $table->decimal('amount', 15, 2);
                $table->decimal('retention_amount', 15, 2)->default(0.00);
                $table->string('milestone_name')->nullable(); // e.g. "Mobilization Advance", "Progress Payment 1", "Retention Release"
                $table->string('payment_method')->default('Bank Transfer');
                $table->string('payment_reference')->nullable();
                $table->string('bank_name')->nullable();
                $table->date('payment_date');
                $table->text('notes')->nullable();
                $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_bill_payments');
        Schema::dropIfExists('invoice_payments');
    }
};
