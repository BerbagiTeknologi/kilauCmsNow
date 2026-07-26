<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('donasikilau', function (Blueprint $table) {
            // Sub user SSO fundraiser/affiliate yang mengarahkan donatur.
            if (!Schema::hasColumn('donasikilau', 'affiliate_sub')) {
                $table->string('affiliate_sub', 64)->nullable()->index()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('donasikilau', function (Blueprint $table) {
            if (Schema::hasColumn('donasikilau', 'affiliate_sub')) {
                $table->dropIndex(['affiliate_sub']);
                $table->dropColumn('affiliate_sub');
            }
        });
    }
};

