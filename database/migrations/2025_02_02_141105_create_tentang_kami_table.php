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
        Schema::create('tentang_kami', function (Blueprint $table) {
            $table->id();
            $table->string('judul_tentang_kami'); 
            $table->string('deskripsi');
            $table->string('file'); 
            $table->string('status_tentang_kami');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tentang_kami');
    }
};
