<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cms_guest_donors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->timestamps();
        });

        Schema::table('donasikilau', function (Blueprint $table) {
            $table->string('donor_source', 32)->nullable()->after('email');
            $table->char('external_donor_id', 36)->nullable()->after('donor_source');
            $table->boolean('is_anonymous')->default(false)->after('external_donor_id');
            $table->index(
                ['donor_source', 'external_donor_id'],
                'donasikilau_donor_identity_idx'
            );
        });

        $this->widenHistoryExternalUserId();
    }

    public function down(): void
    {
        Schema::table('donasikilau', function (Blueprint $table) {
            $table->dropIndex('donasikilau_donor_identity_idx');
            $table->dropColumn([
                'donor_source',
                'external_donor_id',
                'is_anonymous',
            ]);
        });

        Schema::dropIfExists('cms_guest_donors');

        // Tipe history tetap string agar rollback tidak merusak UUID yang sudah tersimpan.
    }

    private function widenHistoryExternalUserId(): void
    {
        match (DB::getDriverName()) {
            'mysql' => DB::statement(
                'ALTER TABLE donasi_histories MODIFY external_user_id CHAR(36) NULL'
            ),
            'pgsql' => DB::statement(
                'ALTER TABLE donasi_histories ALTER COLUMN external_user_id TYPE VARCHAR(36) '
                .'USING external_user_id::VARCHAR'
            ),
            'sqlsrv' => DB::statement(
                'ALTER TABLE donasi_histories ALTER COLUMN external_user_id NVARCHAR(36) NULL'
            ),
            'sqlite' => null,
            default => throw new RuntimeException('Driver database tidak didukung untuk perubahan external_user_id.'),
        };
    }
};
