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
        Schema::create('home_kilau', function (Blueprint $table) {
            $table->id();
            $table->string('judul_home')->nullable();
            $table->string('file_home')->nullable();
            $table->string('status_home_kilau')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_kilau');
    }
};
