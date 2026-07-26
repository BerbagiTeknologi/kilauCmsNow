<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_guest_donors', function (Blueprint $table): void {
            $table->unsignedBigInteger('profile_version')->default(1)->after('no_hp');
            $table->boolean('is_active')->default(true)->after('profile_version');
            $table->timestamp('anonymized_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('cms_guest_donors', function (Blueprint $table): void {
            $table->dropColumn(['profile_version', 'is_active', 'anonymized_at']);
        });
    }
};
