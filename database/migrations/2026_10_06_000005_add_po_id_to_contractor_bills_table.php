<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contractor_bills', function (Blueprint $table) {
            $table->foreignId('po_id')->nullable()->constrained('purchase_orders')->nullOnDelete()->after('job_id');
        });
    }

    public function down(): void
    {
        Schema::table('contractor_bills', function (Blueprint $table) {
            $table->dropForeign(['po_id']);
            $table->dropColumn('po_id');
        });
    }
};
