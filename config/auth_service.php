<?php

return [
    'url' => env('AUTH_SERVICE_URL', 'http://127.0.0.1:8001'),
    'internal_key' => env('AUTH_SERVICE_INTERNAL_KEY'),
    'timeout' => (int) env('AUTH_SERVICE_TIMEOUT', 10),
    'default_role' => env('AUTH_SERVICE_DEFAULT_ROLE', 'user'),
    'app_name' => env('AUTH_SERVICE_APP_NAME', 'kilauCms'),
    'local_database' => env('AUTH_SERVICE_LOCAL_DATABASE', env('DB_DATABASE', 'klauindonesia_cms')),
    'local_user_table' => env('AUTH_SERVICE_LOCAL_USER_TABLE', 'users'),
];
