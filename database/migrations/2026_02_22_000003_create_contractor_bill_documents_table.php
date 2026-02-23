<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_bill_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_bill_id')->constrained('contractor_bills')->cascadeOnDelete();
            $table->string('document_type'); // Contractor Bill, Work Certificate, Photos, etc.
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_bill_documents');
    }
};
