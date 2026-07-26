<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('referral_codes')) {
            return;
        }

        Schema::table('referral_codes', function (Blueprint $table) {
            if (! Schema::hasColumn('referral_codes', 'photo_url_snapshot')) {
                $table->string('photo_url_snapshot')->nullable()->after('position_snapshot');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('referral_codes')) {
            return;
        }

        Schema::table('referral_codes', function (Blueprint $table) {
            if (Schema::hasColumn('referral_codes', 'photo_url_snapshot')) {
                $table->dropColumn('photo_url_snapshot');
            }
        });
    }
};
