<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIklanKilauTable extends Migration
{
    /**
     * Jalankan migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iklan_kilau', function (Blueprint $table) {
            $table->id(); 
            $table->string('judul'); 
            $table->text('deskripsi');
            $table->string('file')->nullable(); 
            $table->integer('jumlah_yayasan')->default(0);
            $table->integer('jumlah_donatur')->default(0); 
            $table->timestamps(); 
        });
    }

    /**
     * Membalikkan perubahan yang dilakukan oleh migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iklan_kilau');
    }
}
