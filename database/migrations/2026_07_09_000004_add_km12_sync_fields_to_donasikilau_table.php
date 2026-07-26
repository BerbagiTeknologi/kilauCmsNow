<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('donasikilau', function (Blueprint $table) {
            if (! Schema::hasColumn('donasikilau', 'km12_sync_status')) {
                $table->string('km12_sync_status', 32)->nullable()->index()->after('referral_position_snapshot');
            }

            if (! Schema::hasColumn('donasikilau', 'km12_transaksi_id')) {
                $table->unsignedBigInteger('km12_transaksi_id')->nullable()->index()->after('km12_sync_status');
            }

            if (! Schema::hasColumn('donasikilau', 'km12_synced_at')) {
                $table->timestamp('km12_synced_at')->nullable()->after('km12_transaksi_id');
            }

            if (! Schema::hasColumn('donasikilau', 'km12_sync_error')) {
                $table->text('km12_sync_error')->nullable()->after('km12_synced_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('donasikilau', function (Blueprint $table) {
            foreach (['km12_sync_status', 'km12_transaksi_id'] as $column) {
                if (Schema::hasColumn('donasikilau', $column)) {
                    $table->dropIndex([$column]);
                }
            }

            foreach ([
                'km12_sync_status',
                'km12_transaksi_id',
                'km12_synced_at',
                'km12_sync_error',
            ] as $column) {
                if (Schema::hasColumn('donasikilau', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
