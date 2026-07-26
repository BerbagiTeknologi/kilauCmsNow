<?php

use Illuminate\Contracts\Console\Kernel;

require dirname(__DIR__, 3).'/vendor/autoload.php';

$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$connection = config('database.connections.mysql');
$sourceDatabase = (string) $connection['database'];
$recoveryDatabase = 'klauindonesia_cms_recovery_20260721_141758';
$mysqlBinlog = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqlbinlog.exe';
$binlogDirectory = 'C:\\laragon\\data\\mysql-8';
$binlogs = [
    'binlog.000883',
    'binlog.000884',
    'binlog.000885',
    'binlog.000886',
    'binlog.000891',
    'binlog.000905',
    'binlog.000908',
    'binlog.000909',
    'binlog.000922',
    'binlog.000925',
    'binlog.000927',
    'binlog.000928',
];

if ($sourceDatabase !== 'klauindonesia_cms') {
    throw new RuntimeException('Database sumber tidak sesuai; recovery dihentikan.');
}

$host = (string) ($connection['host'] ?? '127.0.0.1');
$port = (int) ($connection['port'] ?? 3306);
$dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
$pdo = new PDO($dsn, (string) $connection['username'], (string) $connection['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$exists = $pdo->prepare(
    'SELECT COUNT(*) FROM information_schema.schemata WHERE schema_name = ?'
);
$exists->execute([$recoveryDatabase]);

if ((int) $exists->fetchColumn() !== 0) {
    throw new RuntimeException('Database recovery sudah ada; recovery dihentikan agar tidak menimpa data.');
}

$pdo->exec(
    "CREATE DATABASE `{$recoveryDatabase}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
);
$pdo->exec("USE `{$recoveryDatabase}`");

$pdo->exec(<<<'SQL'
CREATE TABLE `donasikilau` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `type_donasi` VARCHAR(255) NULL,
    `opsional_umum` VARCHAR(255) NULL,
    `id_program` BIGINT UNSIGNED NULL,
    `nama` VARCHAR(255) NULL,
    `total_donasi` DECIMAL(15,2) NULL,
    `status_donasi` VARCHAR(255) NULL,
    `feedback` TEXT NULL,
    `no_hp` VARCHAR(20) NULL,
    `email` VARCHAR(255) NULL,
    `donor_source` VARCHAR(32) NULL,
    `external_donor_id` CHAR(36) NULL,
    `is_anonymous` TINYINT(1) NOT NULL DEFAULT 0,
    `affiliate_sub` VARCHAR(64) NULL,
    `referral_code` VARCHAR(64) NULL,
    `referral_type` VARCHAR(32) NULL,
    `referral_cms_user_id` BIGINT UNSIGNED NULL,
    `referral_global_user_id` CHAR(36) NULL,
    `referral_km12_user_id` BIGINT UNSIGNED NULL,
    `referral_karyawan_id` BIGINT UNSIGNED NULL,
    `referral_name_snapshot` VARCHAR(255) NULL,
    `referral_position_snapshot` VARCHAR(255) NULL,
    `km12_sync_status` VARCHAR(32) NULL,
    `km12_transaksi_id` BIGINT UNSIGNED NULL,
    `km12_synced_at` TIMESTAMP NULL,
    `km12_sync_error` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    INDEX `donasikilau_donor_identity_idx` (`donor_source`, `external_donor_id`),
    INDEX `donasikilau_referral_code_index` (`referral_code`),
    INDEX `donasikilau_referral_type_index` (`referral_type`),
    INDEX `donasikilau_referral_cms_user_id_index` (`referral_cms_user_id`),
    INDEX `donasikilau_referral_global_user_id_index` (`referral_global_user_id`),
    INDEX `donasikilau_referral_km12_user_id_index` (`referral_km12_user_id`),
    INDEX `donasikilau_referral_karyawan_id_index` (`referral_karyawan_id`),
    INDEX `donasikilau_km12_sync_status_index` (`km12_sync_status`),
    INDEX `donasikilau_km12_transaksi_id_index` (`km12_transaksi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);

$pdo->exec(<<<'SQL'
CREATE TABLE `donasi_histories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `donasikilau_id` BIGINT UNSIGNED NOT NULL,
    `external_user_id` CHAR(36) NULL,
    `status_donasi` VARCHAR(255) NULL,
    `total_donasi` DECIMAL(15,2) NULL,
    `feedback` TEXT NULL,
    `token` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    INDEX `donasi_histories_donasikilau_id_foreign` (`donasikilau_id`),
    INDEX `donasi_histories_external_user_id_foreign` (`external_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);

$donationColumns = [
    13 => [
        'id', 'type_donasi', 'opsional_umum', 'id_program', 'nama', 'total_donasi',
        'status_donasi', 'feedback', 'no_hp', 'email', 'affiliate_sub', 'created_at',
        'updated_at',
    ],
    21 => [
        'id', 'type_donasi', 'opsional_umum', 'id_program', 'nama', 'total_donasi',
        'status_donasi', 'feedback', 'no_hp', 'email', 'affiliate_sub', 'referral_code',
        'referral_type', 'referral_cms_user_id', 'referral_global_user_id',
        'referral_km12_user_id', 'referral_karyawan_id', 'referral_name_snapshot',
        'referral_position_snapshot', 'created_at', 'updated_at',
    ],
    25 => [
        'id', 'type_donasi', 'opsional_umum', 'id_program', 'nama', 'total_donasi',
        'status_donasi', 'feedback', 'no_hp', 'email', 'affiliate_sub', 'referral_code',
        'referral_type', 'referral_cms_user_id', 'referral_global_user_id',
        'referral_km12_user_id', 'referral_karyawan_id', 'referral_name_snapshot',
        'referral_position_snapshot', 'km12_sync_status', 'km12_transaksi_id',
        'km12_synced_at', 'km12_sync_error', 'created_at', 'updated_at',
    ],
    28 => [
        'id', 'type_donasi', 'opsional_umum', 'id_program', 'nama', 'total_donasi',
        'status_donasi', 'feedback', 'no_hp', 'email', 'donor_source',
        'external_donor_id', 'is_anonymous', 'affiliate_sub', 'referral_code',
        'referral_type', 'referral_cms_user_id', 'referral_global_user_id',
        'referral_km12_user_id', 'referral_karyawan_id', 'referral_name_snapshot',
        'referral_position_snapshot', 'km12_sync_status', 'km12_transaksi_id',
        'km12_synced_at', 'km12_sync_error', 'created_at', 'updated_at',
    ],
];
$historyColumns = [
    'id', 'donasikilau_id', 'external_user_id', 'status_donasi', 'total_donasi',
    'feedback', 'token', 'created_at', 'updated_at',
];
$timestampColumns = [
    'km12_synced_at', 'created_at', 'updated_at',
];
$counters = [
    'donasikilau' => ['insert' => 0, 'update' => 0, 'delete' => 0],
    'donasi_histories' => ['insert' => 0, 'update' => 0, 'delete' => 0],
];
$event = null;

$flush = function () use (
    &$event,
    &$counters,
    $pdo,
    $donationColumns,
    $historyColumns,
    $timestampColumns
): void {
    if (!$event || $event['values'] === []) {
        $event = null;

        return;
    }

    $table = $event['table'];
    $operation = $event['operation'];
    $values = $event['values'];
    $columnCount = max(array_keys($values));
    $columns = $table === 'donasikilau'
        ? ($donationColumns[$columnCount] ?? null)
        : ($columnCount === count($historyColumns) ? $historyColumns : null);

    if (!$columns) {
        throw new RuntimeException("Jumlah kolom {$table} tidak dikenali: {$columnCount}.");
    }

    if ($operation === 'delete') {
        $pdo->exec("DELETE FROM `{$table}` WHERE `id` = {$values[1]['literal']}");
        $counters[$table]['delete']++;
        $event = null;

        return;
    }

    $columnSql = [];
    $valueSql = [];
    $updateSql = [];

    foreach ($columns as $offset => $column) {
        $position = $offset + 1;
        $columnSql[] = "`{$column}`";
        $literal = $values[$position]['literal'];
        $type = $values[$position]['type'];

        if (
            in_array($column, $timestampColumns, true)
            && str_starts_with($type, 'TIMESTAMP')
            && preg_match('/\A[0-9]+\z/', $literal)
        ) {
            $literal = "FROM_UNIXTIME({$literal})";
        }

        $valueSql[] = $literal;

        if ($column !== 'id') {
            $updateSql[] = "`{$column}` = VALUES(`{$column}`)";
        }
    }

    $sql = "INSERT INTO `{$table}` (".implode(', ', $columnSql).') VALUES ('
        .implode(', ', $valueSql).') ON DUPLICATE KEY UPDATE '.implode(', ', $updateSql);

    try {
        $pdo->exec($sql);
    } catch (Throwable $exception) {
        throw new RuntimeException(
            "Gagal memulihkan event {$operation} untuk {$table}.",
            previous: $exception
        );
    }

    $counters[$table][$operation]++;
    $event = null;
};

foreach ($binlogs as $binlog) {
    $path = $binlogDirectory.DIRECTORY_SEPARATOR.$binlog;
    $command = sprintf(
        '"%s" --base64-output=DECODE-ROWS -vv "%s"',
        $mysqlBinlog,
        $path
    );
    $process = proc_open($command, [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ], $pipes);

    if (!is_resource($process)) {
        throw new RuntimeException("Gagal membuka {$binlog}.");
    }

    fclose($pipes[0]);

    while (($line = fgets($pipes[1])) !== false) {
        $line = rtrim($line, "\r\n");

        if (preg_match(
            '/^### (INSERT INTO|UPDATE|DELETE FROM) `([^`]+)`\.`([^`]+)`$/',
            $line,
            $matches
        )) {
            $flush();
            $database = $matches[2];
            $table = $matches[3];

            if (
                $database === $sourceDatabase
                && in_array($table, ['donasikilau', 'donasi_histories'], true)
            ) {
                $event = [
                    'operation' => match ($matches[1]) {
                        'INSERT INTO' => 'insert',
                        'UPDATE' => 'update',
                        'DELETE FROM' => 'delete',
                    },
                    'table' => $table,
                    'phase' => null,
                    'values' => [],
                ];
            }

            continue;
        }

        if (!$event) {
            continue;
        }

        if ($line === '### WHERE') {
            $event['phase'] = $event['operation'] === 'delete' ? 'values' : 'before';

            continue;
        }

        if ($line === '### SET') {
            $event['phase'] = 'values';
            $event['values'] = [];

            continue;
        }

        if (
            $event['phase'] === 'values'
            && preg_match('/^###   @(\d+)=(.*) \/\* (.+) \*\/$/', $line, $matches)
        ) {
            $event['values'][(int) $matches[1]] = [
                'literal' => $matches[2],
                'type' => $matches[3],
            ];
        }
    }

    $flush();
    fclose($pipes[1]);
    $errors = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if ($exitCode !== 0) {
        throw new RuntimeException("mysqlbinlog gagal membaca {$binlog}: ".trim($errors));
    }
}

$donationCount = (int) $pdo->query('SELECT COUNT(*) FROM donasikilau')->fetchColumn();
$historyCount = (int) $pdo->query('SELECT COUNT(*) FROM donasi_histories')->fetchColumn();
$orphanCount = (int) $pdo->query(<<<'SQL'
SELECT COUNT(*)
FROM donasi_histories AS history
LEFT JOIN donasikilau AS donation ON donation.id = history.donasikilau_id
WHERE donation.id IS NULL
SQL)->fetchColumn();

echo json_encode([
    'database' => $recoveryDatabase,
    'events' => $counters,
    'donations' => $donationCount,
    'histories' => $historyCount,
    'orphan_histories' => $orphanCount,
], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
