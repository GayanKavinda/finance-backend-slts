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
        Schema::create('cheque_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();

            // Payer (Drawer) Information
            $table->string('payer_name');
            $table->string('payer_account_number');
            $table->string('payer_bank_name');
            $table->string('payer_bank_branch')->nullable();
            $table->string('payer_bank_code')->nullable(); // Sort code / MICR

            // Cheque Details
            $table->string('cheque_number');
            $table->date('cheque_date');
            $table->decimal('amount', 15, 2);
            $table->string('amount_in_words');
            $table->string('payee_name');
            $table->string('signature_path')->nullable(); // Path to signature image

            $table->enum('status', ['Pending', 'Cleared', 'Bounced'])->default('Pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheque_transactions');
    }
};
