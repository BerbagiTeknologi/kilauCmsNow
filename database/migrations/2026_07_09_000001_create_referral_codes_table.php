<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('referral_codes')) {
            return;
        }

        Schema::create('referral_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->char('global_user_id', 36)->nullable()->index();
            $table->string('code', 64)->unique();
            $table->string('referral_type', 32)->index();
            $table->unsignedBigInteger('km12_user_id')->nullable()->index();
            $table->unsignedBigInteger('karyawan_id')->nullable()->index();
            $table->string('name_snapshot')->nullable();
            $table->string('email_snapshot')->nullable();
            $table->string('position_snapshot')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('employee_verified_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['cms_user_id', 'referral_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_codes');
    }
};
