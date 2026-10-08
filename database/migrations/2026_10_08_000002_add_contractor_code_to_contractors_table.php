<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add contractor_code as nullable — skip if already present
        if (!Schema::hasColumn('contractors', 'contractor_code')) {
            Schema::table('contractors', function (Blueprint $table) {
                $table->string('contractor_code', 20)->nullable()->after('id');
            });
        }

        // Step 2: Backfill any contractors that don't yet have a code
        DB::table('contractors')
            ->whereNull('contractor_code')
            ->orderBy('id')
            ->chunk(100, function ($contractors) {
                foreach ($contractors as $contractor) {
                    DB::table('contractors')
                        ->where('id', $contractor->id)
                        ->update([
                            'contractor_code' => 'CON-' . str_pad($contractor->id, 6, '0', STR_PAD_LEFT),
                        ]);
                }
            });

        // Step 3: Add unique constraint — skip if it already exists
        $indexes = collect(DB::select("SHOW INDEX FROM contractors WHERE Key_name = 'contractors_contractor_code_unique'"));
        if ($indexes->isEmpty()) {
            Schema::table('contractors', function (Blueprint $table) {
                $table->unique('contractor_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->dropUnique(['contractor_code']);
            $table->dropColumn('contractor_code');
        });
    }
};
