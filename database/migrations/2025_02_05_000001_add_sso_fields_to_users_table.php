<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sso_sub')) {
                $table->string('sso_sub')->nullable()->unique()->after('password');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('sso_sub');
            }

            if (!Schema::hasColumn('users', 'sso_payload')) {
                $table->json('sso_payload')->nullable()->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sso_payload')) {
                $table->dropColumn('sso_payload');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'sso_sub')) {
                $table->dropColumn('sso_sub');
            }
        });
    }
};
