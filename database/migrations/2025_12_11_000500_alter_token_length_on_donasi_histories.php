<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('donasi_histories', function (Blueprint $table) {
            // Kolom token perlu lebih panjang untuk JWT; ubah ke TEXT supaya aman.
            $table->text('token')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('donasi_histories', function (Blueprint $table) {
            // Kembali ke panjang awal; sesuaikan jika semula berbeda.
            $table->string('token', 255)->nullable()->change();
        });
    }
};
