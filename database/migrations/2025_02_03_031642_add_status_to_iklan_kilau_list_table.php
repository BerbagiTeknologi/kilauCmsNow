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
        Schema::table('iklan_kilau_list', function (Blueprint $table) {
            $table->string('status_iklan_kilau_list')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iklan_kilau_list', function (Blueprint $table) {
            $table->dropColumn('status_iklan_kilau_list');
        });
    }
};
