<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('project_jobs')->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained('contractors')->cascadeOnDelete();
            $table->decimal('quotation_amount', 15, 2);
            $table->date('quotation_date');
            $table->text('work_scope')->nullable();
            $table->integer('estimated_days')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('Submitted'); // Submitted/Selected/Rejected
            $table->foreignId('entered_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_quotations');
    }
};
