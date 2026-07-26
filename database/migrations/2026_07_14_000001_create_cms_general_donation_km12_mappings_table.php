<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableName = 'cms_general_donation_km12_mappings';

        if (! Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('donation_type', 30);
                $table->unsignedBigInteger('km12_program_penerimaan_id')->nullable();
                $table->unsignedBigInteger('km12_sumber_dana_id')->nullable();
                $table->string('km12_program_name')->nullable();
                $table->string('km12_sumber_dana_name')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('synced_at')->nullable();
                $table->timestamps();

                $table->unique('donation_type', 'cms_general_type_unique');
            });
        }

        $indexes = [
            'km12_program_penerimaan_id' => 'cms_general_program_idx',
            'km12_sumber_dana_id' => 'cms_general_source_idx',
            'is_active' => 'cms_general_active_idx',
        ];

        foreach ($indexes as $column => $indexName) {
            if (Schema::hasIndex($tableName, $indexName) || Schema::hasIndex($tableName, [$column])) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($column, $indexName) {
                $table->index($column, $indexName);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_general_donation_km12_mappings');
    }
};
