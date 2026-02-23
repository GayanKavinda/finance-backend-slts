<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_jobs', function (Blueprint $table) {
            $table->foreignId('selected_contractor_id')->nullable()->constrained('contractors')->nullOnDelete();
            $table->decimal('contractor_quote_amount', 15, 2)->nullable();
            $table->date('contractor_quote_date')->nullable();
            $table->date('work_start_date')->nullable();
            $table->date('work_completion_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('project_jobs', function (Blueprint $table) {
            $table->dropForeign(['selected_contractor_id']);
            $table->dropColumn([
                'selected_contractor_id',
                'contractor_quote_amount',
                'contractor_quote_date',
                'work_start_date',
                'work_completion_date'
            ]);
        });
    }
};
