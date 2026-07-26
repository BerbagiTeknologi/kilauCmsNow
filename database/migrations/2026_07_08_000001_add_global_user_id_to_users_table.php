<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'global_user_id')) {
                $table->uuid('global_user_id')->nullable()->unique()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'global_user_id')) {
                $table->dropUnique(['global_user_id']);
                $table->dropColumn('global_user_id');
            }
        });
    }
};
