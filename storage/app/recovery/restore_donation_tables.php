<?php

use Illuminate\Contracts\Console\Kernel;

require dirname(__DIR__, 3).'/vendor/autoload.php';

$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$connection = config('database.connections.mysql');
$activeDatabase = (string) $connection['database'];
$recoveryDatabase = 'klauindonesia_cms_recovery_20260721_141758';
$stagingDonationTable = 'donasikilau_restore_20260721';
$stagingHistoryTable = 'donasi_histories_restore_20260721';
$expectedCounts = [
    'donasikilau' => 38,
    'donasi_histories' => 13,
];

if ($activeDatabase !== 'klauindonesia_cms') {
    throw new RuntimeException('Database aktif tidak sesuai; restore dihentikan.');
}

$host = (string) ($connection['host'] ?? '127.0.0.1');
$port = (int) ($connection['port'] ?? 3306);
$dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
$pdo = new PDO($dsn, (string) $connection['username'], (string) $connection['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$schemaExists = $pdo->prepare(
    'SELECT COUNT(*) FROM information_schema.schemata WHERE schema_name = ?'
);
$schemaExists->execute([$recoveryDatabase]);

if ((int) $schemaExists->fetchColumn() !== 1) {
    throw new RuntimeException('Database recovery tidak ditemukan; restore dihentikan.');
}

$tableExists = $pdo->prepare(<<<'SQL'
SELECT COUNT(*)
FROM information_schema.tables
WHERE table_schema = ? AND table_name = ?
SQL);

$assertTableState = function (string $database, string $table, bool $expected) use ($tableExists): void {
    $tableExists->execute([$database, $table]);
    $exists = (int) $tableExists->fetchColumn() === 1;

    if ($exists !== $expected) {
        $state = $expected ? 'tidak ditemukan' : 'sudah ada';

        throw new RuntimeException("Tabel {$database}.{$table} {$state}; restore dihentikan.");
    }
};

foreach (array_keys($expectedCounts) as $table) {
    $assertTableState($recoveryDatabase, $table, true);
    $assertTableState($activeDatabase, $table, false);
}

foreach ([$stagingDonationTable, $stagingHistoryTable] as $table) {
    $assertTableState($activeDatabase, $table, false);
}

foreach ($expectedCounts as $table => $expectedCount) {
    $count = (int) $pdo
        ->query("SELECT COUNT(*) FROM `{$recoveryDatabase}`.`{$table}`")
        ->fetchColumn();

    if ($count !== $expectedCount) {
        throw new RuntimeException("Jumlah data recovery {$table} berubah; restore dihentikan.");
    }
}

$recoveryOrphans = (int) $pdo->query(<<<SQL
SELECT COUNT(*)
FROM `{$recoveryDatabase}`.`donasi_histories` AS history
LEFT JOIN `{$recoveryDatabase}`.`donasikilau` AS donation
    ON donation.id = history.donasikilau_id
WHERE donation.id IS NULL
SQL)->fetchColumn();

if ($recoveryOrphans !== 0) {
    throw new RuntimeException('Recovery memiliki riwayat orphan; restore dihentikan.');
}

$outboxMissing = (int) $pdo->query(<<<SQL
SELECT COUNT(*)
FROM `{$activeDatabase}`.`integration_outbox_messages` AS outbox
LEFT JOIN `{$recoveryDatabase}`.`donasikilau` AS donation
    ON donation.id = CAST(outbox.aggregate_id AS UNSIGNED)
WHERE outbox.aggregate_type = 'donation'
    AND donation.id IS NULL
SQL)->fetchColumn();

if ($outboxMissing !== 0) {
    throw new RuntimeException('Ada referensi outbox yang tidak tersedia di recovery; restore dihentikan.');
}

$renamed = false;

try {
    $pdo->exec(<<<SQL
CREATE TABLE `{$activeDatabase}`.`{$stagingDonationTable}`
LIKE `{$recoveryDatabase}`.`donasikilau`
SQL);
    $pdo->exec(<<<SQL
CREATE TABLE `{$activeDatabase}`.`{$stagingHistoryTable}`
LIKE `{$recoveryDatabase}`.`donasi_histories`
SQL);

    $pdo->exec(<<<SQL
INSERT INTO `{$activeDatabase}`.`{$stagingDonationTable}`
SELECT * FROM `{$recoveryDatabase}`.`donasikilau`
SQL);
    $pdo->exec(<<<SQL
INSERT INTO `{$activeDatabase}`.`{$stagingHistoryTable}`
SELECT * FROM `{$recoveryDatabase}`.`donasi_histories`
SQL);

    $stagingCounts = [
        'donasikilau' => (int) $pdo
            ->query("SELECT COUNT(*) FROM `{$activeDatabase}`.`{$stagingDonationTable}`")
            ->fetchColumn(),
        'donasi_histories' => (int) $pdo
            ->query("SELECT COUNT(*) FROM `{$activeDatabase}`.`{$stagingHistoryTable}`")
            ->fetchColumn(),
    ];

    if ($stagingCounts !== $expectedCounts) {
        throw new RuntimeException('Jumlah data staging tidak sesuai; restore dihentikan.');
    }

    $stagingOrphans = (int) $pdo->query(<<<SQL
SELECT COUNT(*)
FROM `{$activeDatabase}`.`{$stagingHistoryTable}` AS history
LEFT JOIN `{$activeDatabase}`.`{$stagingDonationTable}` AS donation
    ON donation.id = history.donasikilau_id
WHERE donation.id IS NULL
SQL)->fetchColumn();

    if ($stagingOrphans !== 0) {
        throw new RuntimeException('Staging memiliki riwayat orphan; restore dihentikan.');
    }

    $pdo->exec(<<<SQL
RENAME TABLE
    `{$activeDatabase}`.`{$stagingDonationTable}` TO `{$activeDatabase}`.`donasikilau`,
    `{$activeDatabase}`.`{$stagingHistoryTable}` TO `{$activeDatabase}`.`donasi_histories`
SQL);
    $renamed = true;
} finally {
    if (! $renamed) {
        $pdo->exec("DROP TABLE IF EXISTS `{$activeDatabase}`.`{$stagingHistoryTable}`");
        $pdo->exec("DROP TABLE IF EXISTS `{$activeDatabase}`.`{$stagingDonationTable}`");
    }
}

echo json_encode([
    'restored' => true,
    'donations' => $expectedCounts['donasikilau'],
    'histories' => $expectedCounts['donasi_histories'],
    'orphan_histories' => 0,
    'missing_outbox_donations' => 0,
], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
