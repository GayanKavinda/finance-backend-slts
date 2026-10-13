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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('category', 50);
            $table->string('priority', 20)->default('Medium');
            $table->string('subject', 200);
            $table->text('description');
            $table->string('status', 20)->default('Open');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
