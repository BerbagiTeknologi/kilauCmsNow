<?php

namespace App\Services;

use Carbon\Carbon;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;
use Google\Analytics\Data\V1beta\RunRealtimeReportRequest;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Throwable;

class GoogleAnalyticsService
{
    private const READONLY_SCOPE = 'https://www.googleapis.com/auth/analytics.readonly';

    private ?BetaAnalyticsDataClient $client = null;
    private ?string $error = null;
    private ?string $realtimeError = null;
    private bool $apiFailed = false;

    public function dashboard(string $startDate = '28daysAgo', string $endDate = 'today'): array
    {
        $newVsReturning = $this->newVsReturning($startDate, $endDate);
        $summary = $this->summary($startDate, $endDate);
        $summary['returningUsers'] = $this->returningUsersFrom($newVsReturning);

        $data = [
            'summary' => $summary,
            'dailyTrend' => $this->dailyTrend($startDate, $endDate),
            'newVsReturning' => $newVsReturning,
            'trafficSources' => $this->trafficSources($startDate, $endDate),
            'locations' => $this->locations($startDate, $endDate),
            'devices' => $this->devices($startDate, $endDate),
            'topPages' => $this->topPages($startDate, $endDate),
            'highBouncePages' => $this->highBouncePages($startDate, $endDate),
            'events' => $this->events($startDate, $endDate),
            'realtime' => $this->realtime(),
            'error' => $this->error,
            'realtimeError' => $this->realtimeError,
        ];

        $data['hasData'] = $this->hasData($data);

        return $data;
    }

    public function summary(string $startDate = '28daysAgo', string $endDate = 'today'): array
    {
        $defaults = [
            'activeUsers' => 0,
            'newUsers' => 0,
            'returningUsers' => 0,
            'sessions' => 0,
            'screenPageViews' => 0,
            'bounceRate' => 0,
            'averageSessionDuration' => 0,
        ];

        $rows = $this->runReport(
            [],
            ['activeUsers', 'newUsers', 'sessions', 'screenPageViews', 'bounceRate', 'averageSessionDuration'],
            $startDate,
            $endDate,
            1
        );

        return array_merge($defaults, $rows[0] ?? []);
    }

    public function dailyTrend(string $startDate = '28daysAgo', string $endDate = 'today'): array
    {
        $rows = $this->runReport(
            ['date'],
            ['activeUsers', 'sessions', 'screenPageViews'],
            $startDate,
            $endDate,
            100,
            'date',
            false
        );

        return array_map(function (array $row): array {
            $row['date'] = $this->formatGaDate((string) ($row['date'] ?? ''));

            return $row;
        }, $rows);
    }

    public function newVsReturning(string $startDate = '28daysAgo', string $endDate = 'today'): array
    {
        return $this->runReport(
            ['newVsReturning'],
            ['activeUsers'],
            $startDate,
            $endDate,
            10,
            'activeUsers'
        );
    }

    public function trafficSources(string $startDate = '28daysAgo', string $endDate = 'today'): array
    {
        return $this->runReport(
            ['sessionPrimaryChannelGroup', 'sessionSourceMedium'],
            ['sessions', 'activeUsers'],
            $startDate,
            $endDate,
            10,
            'sessions'
        );
    }

    public function locations(string $startDate = '28daysAgo', string $endDate = 'today'): array
    {
        return $this->runReport(
            ['country', 'city'],
            ['activeUsers', 'sessions'],
            $startDate,
            $endDate,
            10,
            'activeUsers'
        );
    }

    public function devices(string $startDate = '28daysAgo', string $endDate = 'today'): array
    {
        return $this->runReport(
            ['deviceCategory'],
            ['activeUsers', 'sessions'],
            $startDate,
            $endDate,
            10,
            'activeUsers'
        );
    }

    public function topPages(string $startDate = '28daysAgo', string $endDate = 'today', int $limit = 10): array
    {
        return $this->runReport(
            ['pageTitle', 'pagePath'],
            ['screenPageViews', 'activeUsers', 'averageSessionDuration', 'bounceRate'],
            $startDate,
            $endDate,
            $limit,
            'screenPageViews'
        );
    }

    public function highBouncePages(string $startDate = '28daysAgo', string $endDate = 'today'): array
    {
        $pages = array_filter($this->topPages($startDate, $endDate, 50), function (array $page): bool {
            return (float) ($page['screenPageViews'] ?? 0) >= 3;
        });

        usort($pages, function (array $a, array $b): int {
            return (float) ($b['bounceRate'] ?? 0) <=> (float) ($a['bounceRate'] ?? 0);
        });

        return array_slice($pages, 0, 10);
    }

    public function events(string $startDate = '28daysAgo', string $endDate = 'today'): array
    {
        return $this->runReport(
            ['eventName'],
            ['eventCount', 'activeUsers'],
            $startDate,
            $endDate,
            15,
            'eventCount'
        );
    }

    public function realtime(): array
    {
        $summary = $this->realtimeSummary();

        return [
            'summary' => $summary,
            'minutes' => $this->realtimeMinutes(),
            'pages' => $this->runRealtimeReport(
                ['unifiedScreenName'],
                ['screenPageViews', 'activeUsers'],
                8,
                'screenPageViews'
            ),
            'devices' => $this->runRealtimeReport(
                ['deviceCategory'],
                ['activeUsers'],
                8,
                'activeUsers'
            ),
            'locations' => $this->runRealtimeReport(
                ['country', 'city'],
                ['activeUsers'],
                8,
                'activeUsers'
            ),
            'events' => $this->runRealtimeReport(
                ['eventName'],
                ['eventCount'],
                8,
                'eventCount'
            ),
        ];
    }

    public function realtimeSummary(): array
    {
        $defaults = [
            'activeUsers' => 0,
            'screenPageViews' => 0,
            'eventCount' => 0,
        ];

        $rows = $this->runRealtimeReport([], ['activeUsers', 'screenPageViews', 'eventCount'], 1);

        return array_merge($defaults, $rows[0] ?? []);
    }

    public function realtimeMinutes(): array
    {
        $rows = $this->runRealtimeReport(
            ['minutesAgo'],
            ['activeUsers'],
            30,
            'minutesAgo',
            true
        );

        usort($rows, fn (array $a, array $b): int => (int) ($b['minutesAgo'] ?? 0) <=> (int) ($a['minutesAgo'] ?? 0));

        return $rows;
    }

    private function runReport(
        array $dimensions,
        array $metrics,
        string $startDate,
        string $endDate,
        int $limit = 10,
        ?string $orderBy = null,
        bool $orderDesc = true
    ): array {
        if ($this->apiFailed) {
            return [];
        }

        $client = $this->client();

        if (!$client) {
            return [];
        }

        try {
            $request = new RunReportRequest([
                'property' => 'properties/'.$this->propertyId(),
                'date_ranges' => [
                    new DateRange([
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]),
                ],
                'dimensions' => array_map(fn (string $name) => new Dimension(['name' => $name]), $dimensions),
                'metrics' => array_map(fn (string $name) => new Metric(['name' => $name]), $metrics),
                'limit' => $limit,
            ]);

            if ($orderBy) {
                $request->setOrderBys([
                    $this->orderBy($orderBy, in_array($orderBy, $metrics, true), $orderDesc),
                ]);
            }

            return $this->mapRows($client->runReport($request), $dimensions, $metrics);
        } catch (Throwable $exception) {
            $this->apiFailed = true;
            $this->error = 'Gagal mengambil data Google Analytics. Pastikan credential punya akses Viewer ke property GA4.';

            report($exception);

            return [];
        }
    }

    private function runRealtimeReport(
        array $dimensions,
        array $metrics,
        int $limit = 10,
        ?string $orderBy = null,
        bool $orderDesc = true
    ): array {
        $client = $this->client();

        if (!$client) {
            return [];
        }

        try {
            $request = new RunRealtimeReportRequest([
                'property' => 'properties/'.$this->propertyId(),
                'dimensions' => array_map(fn (string $name) => new Dimension(['name' => $name]), $dimensions),
                'metrics' => array_map(fn (string $name) => new Metric(['name' => $name]), $metrics),
                'limit' => $limit,
            ]);

            if ($orderBy) {
                $request->setOrderBys([
                    $this->orderBy($orderBy, in_array($orderBy, $metrics, true), $orderDesc),
                ]);
            }

            return $this->mapRows($client->runRealtimeReport($request), $dimensions, $metrics);
        } catch (Throwable $exception) {
            $this->realtimeError = 'Gagal mengambil data realtime Google Analytics.';

            report($exception);

            return [];
        }
    }

    private function client(): ?BetaAnalyticsDataClient
    {
        if ($this->client) {
            return $this->client;
        }

        $propertyId = $this->propertyId();
        $credentialsPath = $this->readableCredentialsPath();

        if (!$propertyId || config('services.google_analytics.credentials_path') === null) {
            $this->error = 'Konfigurasi Google Analytics belum lengkap.';

            return null;
        }

        if (!$credentialsPath) {
            $this->error = 'Credential Google Analytics tidak ditemukan atau tidak bisa dibaca.';

            return null;
        }

        try {
            $json = json_decode((string) file_get_contents($credentialsPath), true, 512, JSON_THROW_ON_ERROR);
            $credentials = new ServiceAccountCredentials([self::READONLY_SCOPE], $json);

            $this->client = new BetaAnalyticsDataClient([
                'credentials' => $credentials,
                'transport' => 'rest',
            ]);

            return $this->client;
        } catch (Throwable $exception) {
            $this->error = 'Credential Google Analytics tidak valid.';

            report($exception);

            return null;
        }
    }

    private function mapRows($response, array $dimensions, array $metrics): array
    {
        $rows = [];

        foreach ($response->getRows() as $row) {
            $item = [];
            $dimensionValues = iterator_to_array($row->getDimensionValues());
            $metricValues = iterator_to_array($row->getMetricValues());

            foreach ($dimensions as $index => $name) {
                $item[$name] = $dimensionValues[$index]->getValue() ?? '';
            }

            foreach ($metrics as $index => $name) {
                $value = $metricValues[$index]->getValue() ?? 0;
                $item[$name] = is_numeric($value) ? $value + 0 : $value;
            }

            $rows[] = $item;
        }

        return $rows;
    }

    private function orderBy(string $name, bool $isMetric, bool $desc): OrderBy
    {
        if ($isMetric) {
            return new OrderBy([
                'metric' => new MetricOrderBy(['metric_name' => $name]),
                'desc' => $desc,
            ]);
        }

        return new OrderBy([
            'dimension' => new DimensionOrderBy(['dimension_name' => $name]),
            'desc' => $desc,
        ]);
    }

    private function returningUsersFrom(array $rows): int
    {
        foreach ($rows as $row) {
            if (strtolower((string) ($row['newVsReturning'] ?? '')) === 'returning') {
                return (int) ($row['activeUsers'] ?? 0);
            }
        }

        return 0;
    }

    private function hasData(array $data): bool
    {
        return array_sum(array_map('count', [
            $data['dailyTrend'],
            $data['newVsReturning'],
            $data['trafficSources'],
            $data['locations'],
            $data['devices'],
            $data['topPages'],
            $data['events'],
        ])) > 0;
    }

    private function formatGaDate(string $value): string
    {
        try {
            return Carbon::createFromFormat('Ymd', $value)->toDateString();
        } catch (Throwable) {
            return $value;
        }
    }

    private function propertyId(): ?string
    {
        $propertyId = config('services.google_analytics.property_id');

        return $propertyId ? (string) $propertyId : null;
    }

    private function readableCredentialsPath(): ?string
    {
        foreach ($this->credentialsPathCandidates() as $path) {
            if (is_file($path) && is_readable($path)) {
                return $path;
            }
        }

        return null;
    }

    private function credentialsPathCandidates(): array
    {
        $path = trim((string) config('services.google_analytics.credentials_path'));

        if ($path === '') {
            return [];
        }

        $paths = [
            $this->isAbsolutePath($path)
                ? $path
                : base_path(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path)),
        ];

        if (str_starts_with(str_replace('\\', '/', $path), 'storage/app/')) {
            $paths[] = storage_path(substr(str_replace('\\', '/', $path), strlen('storage/')));
        }

        $paths[] = storage_path('app/google-analytics/service-account.json');
        $paths[] = storage_path('app/google-analytic/service-account.json');

        return array_values(array_unique($paths));
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, '/')
            || str_starts_with($path, '\\')
            || preg_match('/^[A-Za-z]:[\/\\\\]/', $path) === 1;
    }
}
