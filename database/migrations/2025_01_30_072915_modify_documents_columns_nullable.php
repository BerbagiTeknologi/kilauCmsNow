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
        Schema::table('documents', function (Blueprint $table) {
            $table->text('text_document')->nullable()->change();
            $table->string('file', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Mengembalikan kolom text_document menjadi NOT NULL
            $table->text('text_document')->nullable(false)->change();
            $table->string('file', 255)->nullable(false)->change();
        });
    }
};
