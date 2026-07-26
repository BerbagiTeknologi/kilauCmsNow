<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_outbox_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_type', 100);
            $table->string('aggregate_type', 64);
            $table->string('aggregate_id', 191);
            $table->longText('payload');
            $table->char('payload_hash', 64);
            $table->string('status', 32)->index();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('available_at')->nullable()->index();
            $table->timestamp('locked_at')->nullable();
            $table->uuid('lock_token')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('dead_lettered_at')->nullable();
            $table->string('last_error_code', 64)->nullable();
            $table->unsignedSmallInteger('last_http_status')->nullable();
            $table->timestamps();

            $table->unique(
                ['event_type', 'aggregate_type', 'aggregate_id'],
                'integration_outbox_aggregate_unique'
            );
            $table->index(
                ['status', 'available_at'],
                'integration_outbox_delivery_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_outbox_messages');
    }
};
