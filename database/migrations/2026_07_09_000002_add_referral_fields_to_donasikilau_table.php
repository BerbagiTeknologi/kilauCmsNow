<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $firstReferralColumnAfter = Schema::hasColumn('donasikilau', 'affiliate_sub')
            ? 'affiliate_sub'
            : 'email';

        Schema::table('donasikilau', function (Blueprint $table) use ($firstReferralColumnAfter) {
            if (! Schema::hasColumn('donasikilau', 'referral_code')) {
                $table->string('referral_code', 64)->nullable()->index()->after($firstReferralColumnAfter);
            }

            if (! Schema::hasColumn('donasikilau', 'referral_type')) {
                $table->string('referral_type', 32)->nullable()->index()->after('referral_code');
            }

            if (! Schema::hasColumn('donasikilau', 'referral_cms_user_id')) {
                $table->unsignedBigInteger('referral_cms_user_id')->nullable()->index()->after('referral_type');
            }

            if (! Schema::hasColumn('donasikilau', 'referral_global_user_id')) {
                $table->char('referral_global_user_id', 36)->nullable()->index()->after('referral_cms_user_id');
            }

            if (! Schema::hasColumn('donasikilau', 'referral_km12_user_id')) {
                $table->unsignedBigInteger('referral_km12_user_id')->nullable()->index()->after('referral_global_user_id');
            }

            if (! Schema::hasColumn('donasikilau', 'referral_karyawan_id')) {
                $table->unsignedBigInteger('referral_karyawan_id')->nullable()->index()->after('referral_km12_user_id');
            }

            if (! Schema::hasColumn('donasikilau', 'referral_name_snapshot')) {
                $table->string('referral_name_snapshot')->nullable()->after('referral_karyawan_id');
            }

            if (! Schema::hasColumn('donasikilau', 'referral_position_snapshot')) {
                $table->string('referral_position_snapshot')->nullable()->after('referral_name_snapshot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('donasikilau', function (Blueprint $table) {
            foreach ([
                'referral_code',
                'referral_type',
                'referral_cms_user_id',
                'referral_global_user_id',
                'referral_km12_user_id',
                'referral_karyawan_id',
            ] as $column) {
                if (Schema::hasColumn('donasikilau', $column)) {
                    $table->dropIndex([$column]);
                }
            }

            $columns = [
                'referral_code',
                'referral_type',
                'referral_cms_user_id',
                'referral_global_user_id',
                'referral_km12_user_id',
                'referral_karyawan_id',
                'referral_name_snapshot',
                'referral_position_snapshot',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('donasikilau', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
