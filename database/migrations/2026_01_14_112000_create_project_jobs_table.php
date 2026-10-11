<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->decimal('project_value', 15, 2)->nullable();
            $table->text('description')->nullable();

            $table->foreignId('contractor_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('selected_contractor_id')
                ->nullable()
                ->constrained('contractors')
                ->nullOnDelete();

            $table->decimal('contractor_quote_amount', 15, 2)->nullable();
            $table->date('contractor_quote_date')->nullable();
            $table->date('work_start_date')->nullable();
            $table->date('work_completion_date')->nullable();

            $table->string('status')->default('Pending');

            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_jobs');
    }
};
