<?php $__env->startSection('style'); ?>
    <style>
        .analytics-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(31, 45, 61, 0.08);
            min-height: 112px;
        }

        .analytics-card .card-body {
            padding: 1rem 1.15rem;
        }

        .analytics-card .metric-icon {
            align-items: center;
            background: rgba(21, 114, 232, 0.1);
            border-radius: 50%;
            color: #1572E8;
            display: inline-flex;
            font-size: 18px;
            height: 40px;
            justify-content: center;
            width: 40px;
        }

        .metric-label {
            color: #687385;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .metric-value {
            color: #1f2937;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.2;
        }

        .analytics-panel {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(31, 45, 61, 0.08);
        }

        .analytics-panel .card-header {
            background: #fff;
            padding: .85rem 1rem;
        }

        .analytics-panel .card-body {
            padding: 1rem;
        }

        .chart-wrap {
            height: 220px;
            min-height: 220px;
        }

        .analytics-table {
            font-size: 13px;
            margin-bottom: 0;
        }

        .table.analytics-table td,
        .table.analytics-table th {
            padding: .65rem .75rem;
            vertical-align: middle;
        }

        @media (max-width: 767.98px) {
            .analytics-card {
                min-height: auto;
            }

            .chart-wrap {
                height: 200px;
                min-height: 200px;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $summary = $dashboard['summary'];
        $realtime = $dashboard['realtime'];
        $realtimeSummary = $realtime['summary'];
        $dateValue = fn (string $value) => preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : '';
        $number = fn ($value) => number_format((float) $value, 0, ',', '.');
        $percent = fn ($value) => number_format((float) $value * 100, 1, ',', '.').'%';
        $duration = function ($seconds) {
            $seconds = (int) round((float) $seconds);
            $minutes = intdiv($seconds, 60);
            $remainingSeconds = $seconds % 60;

            return $minutes > 0 ? "{$minutes}m {$remainingSeconds}s" : "{$remainingSeconds}s";
        };
    ?>

    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                <div>
                    <h3 class="fw-bold mb-1">Dashboard Analytics</h3>
                    <p class="text-muted mb-0">Data GA4 untuk website publik Kilau Indonesia.</p>
                </div>
            </div>

            <div class="card analytics-panel mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal mulai</label>
                            <input type="date" name="start_date" value="<?php echo e($dateValue($startDate)); ?>" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal akhir</label>
                            <input type="date" name="end_date" value="<?php echo e($dateValue($endDate)); ?>" class="form-control">
                        </div>
                        <div class="col-md-6 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Tampilkan
                            </button>
                            <a href="<?php echo e(route('dashboard.analytics')); ?>" class="btn btn-outline-secondary">
                                Reset 28 hari
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <?php if($dashboard['error']): ?>
                <div class="alert alert-warning">
                    <?php echo e($dashboard['error']); ?>

                </div>
            <?php elseif($dashboard['realtimeError']): ?>
                <div class="alert alert-warning">
                    <?php echo e($dashboard['realtimeError']); ?>

                </div>
            <?php elseif(! $dashboard['hasData']): ?>
                <div class="alert alert-info">
                    Google Analytics belum mengirim data report standar atau konfigurasi belum lengkap. Realtime bisa lebih cepat muncul dibanding report standar GA4.
                </div>
            <?php endif; ?>

            <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div>
                    <h4 class="fw-bold mb-1">Realtime</h4>
                    <p class="text-muted mb-0">Aktivitas website dalam 30 menit terakhir.</p>
                </div>
                <span class="badge bg-primary px-3 py-2">Live dari GA4 Realtime API</span>
            </div>

            <div class="row">
                <div class="col-sm-6 col-lg-4 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Pengguna aktif</div>
                                <div class="metric-value"><?php echo e($number($realtimeSummary['activeUsers'])); ?></div>
                                <small class="text-muted">30 menit terakhir</small>
                            </div>
                            <div class="metric-icon"><i class="fas fa-bolt"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Tampilan</div>
                                <div class="metric-value"><?php echo e($number($realtimeSummary['screenPageViews'])); ?></div>
                                <small class="text-muted">screenPageViews realtime</small>
                            </div>
                            <div class="metric-icon"><i class="fas fa-eye"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Event</div>
                                <div class="metric-value"><?php echo e($number($realtimeSummary['eventCount'])); ?></div>
                                <small class="text-muted">semua event realtime</small>
                            </div>
                            <div class="metric-icon"><i class="fas fa-signal"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="card analytics-panel">
                        <div class="card-header">
                            <h5 class="mb-0">Device Realtime</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover analytics-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Device</th>
                                        <th>Users</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $realtime['devices']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($row['deviceCategory'] ?: '-'); ?></td>
                                            <td><?php echo e($number($row['activeUsers'])); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">Belum ada data realtime.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 mb-4">
                    <div class="card analytics-panel">
                        <div class="card-header">
                            <h5 class="mb-0">Halaman Aktif Realtime</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover analytics-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Halaman</th>
                                        <th>Views</th>
                                        <th>Users</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $realtime['pages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($row['unifiedScreenName'] ?: '-'); ?></td>
                                            <td><?php echo e($number($row['screenPageViews'])); ?></td>
                                            <td><?php echo e($number($row['activeUsers'])); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada data realtime.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card analytics-panel">
                        <div class="card-header">
                            <h5 class="mb-0">Lokasi Realtime</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover analytics-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Negara</th>
                                        <th>Kota</th>
                                        <th>Users</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $realtime['locations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($row['country'] ?: '-'); ?></td>
                                            <td><?php echo e($row['city'] ?: '-'); ?></td>
                                            <td><?php echo e($number($row['activeUsers'])); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada data realtime.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card analytics-panel">
                        <div class="card-header">
                            <h5 class="mb-0">Event Realtime</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover analytics-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Event</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $realtime['events']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($row['eventName'] ?: '-'); ?></td>
                                            <td><?php echo e($number($row['eventCount'])); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">Belum ada data realtime.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between flex-wrap mt-2 mb-3">
                <div>
                    <h4 class="fw-bold mb-1">Report Standar</h4>
                    <p class="text-muted mb-0">Data historis sesuai periode filter.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Total pengguna</div>
                                <div class="metric-value"><?php echo e($number($summary['activeUsers'])); ?></div>
                            </div>
                            <div class="metric-icon"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Pengguna baru</div>
                                <div class="metric-value"><?php echo e($number($summary['newUsers'])); ?></div>
                            </div>
                            <div class="metric-icon"><i class="fas fa-user-plus"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Returning users</div>
                                <div class="metric-value"><?php echo e($number($summary['returningUsers'])); ?></div>
                            </div>
                            <div class="metric-icon"><i class="fas fa-redo"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Sessions</div>
                                <div class="metric-value"><?php echo e($number($summary['sessions'])); ?></div>
                            </div>
                            <div class="metric-icon"><i class="fas fa-mouse-pointer"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Page views</div>
                                <div class="metric-value"><?php echo e($number($summary['screenPageViews'])); ?></div>
                            </div>
                            <div class="metric-icon"><i class="fas fa-file-alt"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Bounce rate</div>
                                <div class="metric-value"><?php echo e($percent($summary['bounceRate'])); ?></div>
                            </div>
                            <div class="metric-icon"><i class="fas fa-sign-out-alt"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Avg session</div>
                                <div class="metric-value"><?php echo e($duration($summary['averageSessionDuration'])); ?></div>
                            </div>
                            <div class="metric-icon"><i class="fas fa-clock"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Property ID</div>
                                <div class="metric-value" style="font-size: 22px;"><?php echo e(config('services.google_analytics.property_id') ?: '-'); ?></div>
                            </div>
                            <div class="metric-icon"><i class="fas fa-chart-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card analytics-panel">
                        <div class="card-header">
                            <h5 class="mb-0">New vs Returning</h5>
                        </div>
                        <div class="card-body chart-wrap">
                            <canvas id="newReturningChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 mb-4">
                    <div class="card analytics-panel">
                        <div class="card-header">
                            <h5 class="mb-0">Event GA4</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover analytics-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Event</th>
                                        <th>Jumlah</th>
                                        <th>Users</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $dashboard['events']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($row['eventName'] ?: '-'); ?></td>
                                            <td><?php echo e($number($row['eventCount'])); ?></td>
                                            <td><?php echo e($number($row['activeUsers'])); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada data event.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card analytics-panel">
                        <div class="card-header">
                            <h5 class="mb-0">Asal Traffic</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover analytics-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Channel</th>
                                        <th>Source / Medium</th>
                                        <th>Sessions</th>
                                        <th>Users</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $dashboard['trafficSources']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($row['sessionPrimaryChannelGroup'] ?: '-'); ?></td>
                                            <td><?php echo e($row['sessionSourceMedium'] ?: '-'); ?></td>
                                            <td><?php echo e($number($row['sessions'])); ?></td>
                                            <td><?php echo e($number($row['activeUsers'])); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Belum ada data.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card analytics-panel">
                        <div class="card-header">
                            <h5 class="mb-0">Lokasi Pengunjung</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover analytics-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Negara</th>
                                        <th>Kota</th>
                                        <th>Users</th>
                                        <th>Sessions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $dashboard['locations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($row['country'] ?: '-'); ?></td>
                                            <td><?php echo e($row['city'] ?: '-'); ?></td>
                                            <td><?php echo e($number($row['activeUsers'])); ?></td>
                                            <td><?php echo e($number($row['sessions'])); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Belum ada data.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card analytics-panel">
                        <div class="card-header">
                            <h5 class="mb-0">Halaman Bounce Rate Tinggi</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover analytics-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Halaman</th>
                                        <th>Views</th>
                                        <th>Bounce rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $dashboard['highBouncePages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($row['pageTitle'] ?: '-'); ?></strong><br>
                                                <small class="text-muted"><?php echo e($row['pagePath'] ?: '-'); ?></small>
                                            </td>
                                            <td><?php echo e($number($row['screenPageViews'])); ?></td>
                                            <td><?php echo e($percent($row['bounceRate'])); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada data cukup.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-4">
                    <div class="card analytics-panel">
                        <div class="card-header">
                            <h5 class="mb-0">Halaman Terpopuler</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover analytics-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>Path</th>
                                        <th>Views</th>
                                        <th>Users</th>
                                        <th>Avg session</th>
                                        <th>Bounce rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $dashboard['topPages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($row['pageTitle'] ?: '-'); ?></td>
                                            <td class="text-break"><?php echo e($row['pagePath'] ?: '-'); ?></td>
                                            <td><?php echo e($number($row['screenPageViews'])); ?></td>
                                            <td><?php echo e($number($row['activeUsers'])); ?></td>
                                            <td><?php echo e($duration($row['averageSessionDuration'])); ?></td>
                                            <td><?php echo e($percent($row['bounceRate'])); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Belum ada data.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') {
                return;
            }

            const newVsReturning = <?php echo json_encode($dashboard['newVsReturning'], 15, 512) ?>;

            if (newVsReturning.length > 0) {
                new Chart(document.getElementById('newReturningChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: newVsReturning.map(row => row.newVsReturning || '-'),
                        datasets: [{
                            data: newVsReturning.map(row => Number(row.activeUsers || 0)),
                            backgroundColor: ['#1572E8', '#31CE36', '#FFAD46'],
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    },
                });
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\Analytics\dashboard.blade.php ENDPATH**/ ?>