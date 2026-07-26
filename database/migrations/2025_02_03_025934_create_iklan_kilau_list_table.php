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
        Schema::create('iklan_kilau_list', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();

            $table->unsignedBigInteger('iklan_kilau_id')->nullable();
            $table->foreign('iklan_kilau_id')->references('id')->on('iklan_kilau')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iklan_kilau_list');
    }
};
