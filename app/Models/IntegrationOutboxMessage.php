<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationOutboxMessage extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_DEAD_LETTER = 'dead_letter';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'event_type',
        'aggregate_type',
        'aggregate_id',
        'payload',
        'payload_hash',
        'status',
        'attempts',
        'available_at',
        'locked_at',
        'lock_token',
        'delivered_at',
        'dead_lettered_at',
        'last_error_code',
        'last_http_status',
    ];

    protected $casts = [
        'payload' => 'encrypted:array',
        'attempts' => 'integer',
        'last_http_status' => 'integer',
        'available_at' => 'datetime',
        'locked_at' => 'datetime',
        'delivered_at' => 'datetime',
        'dead_lettered_at' => 'datetime',
    ];
}
