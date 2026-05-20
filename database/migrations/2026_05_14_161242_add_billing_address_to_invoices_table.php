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
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('billing_address')->nullable()->after('invoice_date');
            $table->string('customer_po_number')->nullable()->after('billing_address');
            $table->text('customer_po_description')->nullable()->after('customer_po_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['billing_address', 'customer_po_number', 'customer_po_description']);
        });
    }
};
