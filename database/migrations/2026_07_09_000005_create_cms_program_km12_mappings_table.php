<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('cms_program_km12_mappings')) {
            return;
        }

        Schema::create('cms_program_km12_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_program_id')->unique()->constrained('programs')->cascadeOnDelete();
            $table->unsignedBigInteger('km12_program_penerimaan_id')->nullable()->index();
            $table->unsignedBigInteger('km12_sumber_dana_id')->nullable()->index();
            $table->string('km12_program_name')->nullable();
            $table->string('km12_sumber_dana_name')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_program_km12_mappings');
    }
};
