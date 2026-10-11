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
        Schema::create('contractor_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('project_jobs')->cascadeOnDelete();
            $table->foreignId('po_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->string('bill_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->date('bill_date');
            $table->string('document_path')->nullable(); // Legacy PDF/scan path, documents now stored in contractor_bill_documents
            $table->enum('status', ['Draft', 'Uploaded', 'Verified', 'Submitted', 'Approved', 'Paid', 'Rejected'])->default('Draft');
            
            // Workflow & Verification
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Payment metadata
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('bank_name')->nullable();
            $table->decimal('payment_amount', 15, 2)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_bills');
    }
};
