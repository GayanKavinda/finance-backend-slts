<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->after('name');
            $table->string('email')->nullable()->after('contact_person');
            $table->string('phone')->nullable()->after('email');
            $table->string('bank_account_number')->nullable()->after('bank_details');
            $table->string('bank_name')->nullable()->after('bank_account_number');
            $table->string('status')->default('Active')->after('bank_name'); // Active/Blacklisted
            $table->integer('rating')->default(0)->after('status');
            $table->text('notes')->nullable()->after('rating');
        });
    }

    public function down(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->dropColumn([
                'contact_person',
                'email',
                'phone',
                'bank_account_number',
                'bank_name',
                'status',
                'rating',
                'notes'
            ]);
        });
    }
};
