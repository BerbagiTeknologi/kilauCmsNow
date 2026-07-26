<?php

return [
    'url' => env('KM12_SERVICE_URL', 'http://127.0.0.1:8002'),
    'internal_key' => env('KM12_SERVICE_INTERNAL_KEY', env('AUTH_SERVICE_INTERNAL_KEY')),
    'timeout' => (int) env('KM12_SERVICE_TIMEOUT', 10),
    'donation_sync_enabled' => (bool) env('KM12_DONATION_SYNC_ENABLED', false),
    'donation_path' => env('KM12_DONATION_PATH', '/api/internal/cms/donations'),
    'profile_path' => env('KM12_PROFILE_PATH', '/api/internal/cms/donor-profiles'),
    'integration_secret' => env('KM12_DONATION_INTEGRATION_SECRET'),
    'outbox_connection' => env('KM12_OUTBOX_QUEUE_CONNECTION', 'database'),
    'outbox_queue' => env('KM12_OUTBOX_QUEUE', 'integrations'),
    'outbox_max_attempts' => (int) env('KM12_OUTBOX_MAX_ATTEMPTS', 5),
    'outbox_retry_delays' => [60, 300, 900, 3600, 21600],
    'outbox_lock_seconds' => (int) env('KM12_OUTBOX_LOCK_SECONDS', 600),
];
