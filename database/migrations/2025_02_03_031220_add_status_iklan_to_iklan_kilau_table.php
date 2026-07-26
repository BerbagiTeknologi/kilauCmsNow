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
        Schema::table('iklan_kilau', function (Blueprint $table) {
            $table->string('status_kilau')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iklan_kilau', function (Blueprint $table) {
            $table->string('status_kilau')->nullable();
        });
    }
};
