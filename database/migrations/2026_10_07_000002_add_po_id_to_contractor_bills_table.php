<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add purchase_order_id (po_id) foreign key to contractor_bills table.
     * This allows tracking which Purchase Order a contractor bill is tied to.
     */
    public function up(): void
    {
        Schema::table('contractor_bills', function (Blueprint $table) {
            // Nullable: a bill may not always have an associated PO
            $table->foreignId('po_id')
                  ->nullable()
                  ->after('contractor_id')
                  ->constrained('purchase_orders')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractor_bills', function (Blueprint $table) {
            $table->dropForeign(['po_id']);
            $table->dropColumn('po_id');
        });
    }
};
