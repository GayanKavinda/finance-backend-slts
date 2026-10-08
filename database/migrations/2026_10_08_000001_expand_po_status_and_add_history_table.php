<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expand the purchase order lifecycle (Draft → Approved → Sent → Received)
     * and introduce a status history table for the procurement audit trail.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('Draft','Approved','Sent','Received','Cancelled') NOT NULL DEFAULT 'Draft'");

        Schema::create('purchase_order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('purchase_order_id');
            $table->index('changed_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_status_history');

        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('Draft','Approved') NOT NULL DEFAULT 'Draft'");
    }
};
