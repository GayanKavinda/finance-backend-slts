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

            $table->string('status')->default('Pending'); // Changed to string to allow flexibility, or enum if safe. Original was enum. Let's start with string or enum. Controller validation uses 'in:Pending,In Progress,Completed'. I will use string for safety against future changes or enum if preferred. Original used enum. I'll stick to string for better future-proofing or enum if DB supports it. Let's use string to match 'status' => 'required|string' in update validation, but 'in:...' in store. Enum is fine. I'll use string with default.

            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_jobs');
    }
};
