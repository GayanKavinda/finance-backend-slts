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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('invoice_amount', 15, 2);
            $table->date('invoice_date');

            // Status with new values
            $table->enum('status', ['Draft', 'Tax Generated', 'Submitted', 'Approved', 'Rejected', 'Paid'])->default('Draft');

            // Payment Metadata
            $table->string('payment_reference')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('payment_notes')->nullable();

            // Roadmap Payment Fields
            $table->string('cheque_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->decimal('payment_amount', 15, 2)->nullable();
            $table->date('payment_received_date')->nullable();

            // Internal Receipt
            $table->string('receipt_number')->nullable()->unique();

            // Banking Fields
            $table->boolean('is_banked')->default(false);
            $table->date('banked_at')->nullable();
            $table->string('bank_reference')->nullable();

            // Finance Workflow
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
