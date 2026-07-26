<?php $__env->startSection('style'); ?>
    <style>
        .donasi-summary-card {
            background-color: #1572E8;
            color: #fff;
            border-radius: 10px;
            min-height: 120px;
            padding: 16px;
        }

        .donasi-summary-card h4,
        .donasi-summary-card p {
            color: #fff;
            margin: 0;
        }

        .donasi-summary-card h4 {
            font-size: 24px;
            font-weight: 700;
        }

        .donasi-summary-card .icon-big {
            color: #fff;
            font-size: 36px;
        }

        #donasi-table {
            min-width: 1120px;
            table-layout: fixed;
        }

        #donasi-table th {
            color: #111827;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: uppercase;
            white-space: nowrap;
        }

        #donasi-table td {
            color: #111827;
            font-size: 13px;
            line-height: 1.35;
            padding: 10px 12px;
            vertical-align: middle;
        }

        .donasi-cell-title {
            color: #111827;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .donasi-cell-meta {
            color: #6b7280;
            font-size: 12px;
            margin-top: 3px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .donasi-contact-link {
            color: #16a34a;
            display: inline-flex;
            align-items: center;
            font-size: 12px;
            margin-top: 5px;
            max-width: 100%;
            text-decoration: none;
            white-space: nowrap;
        }

        .donasi-contact-link span {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .donasi-contact-link i {
            margin-right: 5px;
        }

        .donasi-feedback {
            color: #4b5563;
            font-size: 12px;
            margin-top: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .donasi-nominal {
            color: #111827;
            font-weight: 700;
            white-space: nowrap;
        }

        .donasi-action {
            font-size: 1rem;
            line-height: 1;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                <div>
                    <h3 class="fw-bold mb-1">Data Donasi</h3>
                    <p class="text-muted mb-0">Transaksi donasi umum dan donasi program Kilau Indonesia.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 col-md-3 mb-3">
                    <div class="donasi-summary-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4><?php echo e(number_format($summary['total_transaksi'], 0, ',', '.')); ?></h4>
                                <p>Total Transaksi</p>
                            </div>
                            <div class="icon-big">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 mb-3">
                    <div class="donasi-summary-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4>Rp <?php echo e(number_format($summary['total_nominal'], 0, ',', '.')); ?></h4>
                                <p>Total Nominal</p>
                            </div>
                            <div class="icon-big">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 mb-3">
                    <div class="donasi-summary-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4><?php echo e(number_format($summary['total_pending'], 0, ',', '.')); ?></h4>
                                <p>Pending</p>
                            </div>
                            <div class="icon-big">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 mb-3">
                    <div class="donasi-summary-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4><?php echo e(number_format($summary['total_aktif'], 0, ',', '.')); ?></h4>
                                <p>Berdonasi</p>
                            </div>
                            <div class="icon-big">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0">Daftar Donasi</h4>
                                <form id="filter-form" class="d-flex flex-wrap gap-2 align-items-center">
                                    <input type="search" name="keyword" id="keywordFilter" class="form-control"
                                        placeholder="Cari donatur/email/program" style="min-width: 240px;">
                                    <select name="month" id="monthFilter" class="form-control" style="min-width: 170px;">
                                        <option value="">Semua Bulan</option>
                                        <?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($month); ?>">
                                                <?php echo e(\Carbon\Carbon::createFromFormat('m', $month)->translatedFormat('F')); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <select name="year" id="yearFilter" class="form-control" style="min-width: 140px;">
                                        <option value="">Semua Tahun</option>
                                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <select name="status" id="statusFilter" class="form-control" style="min-width: 170px;">
                                        <option value="">Semua Status</option>
                                        <option value="<?php echo e(\App\Models\DonasiKilau::DONASI_PENDING); ?>">Pending</option>
                                        <option value="<?php echo e(\App\Models\DonasiKilau::DONASI_AKTIVE); ?>">Berdonasi</option>
                                        <option value="<?php echo e(\App\Models\DonasiKilau::DONASI_EXPIRED); ?>">Expired</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <button type="button" id="resetFilter" class="btn btn-outline-secondary">
                                        Reset
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="donasi-table" class="display table table-striped table-hover align-middle"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 28%;">Donatur</th>
                                            <th style="width: 25%;">Donasi</th>
                                            <th style="width: 13%;">Nominal</th>
                                            <th style="width: 10%;">Status</th>
                                            <th style="width: 12%;">KM12</th>
                                            <th style="width: 10%;">Tanggal</th>
                                            <th style="width: 6%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $donasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $isProgram = (int) $data->type_donasi === \App\Models\DonasiKilau::TYPE_DONASI_PROGRAM;
                                                $donasiLabel = $isProgram
                                                    ? ($data->program ? $data->program->judul : 'Program Tanpa Judul')
                                                    : ((int) $data->opsional_umum === \App\Models\DonasiKilau::OPSIONAL_UMUM_ZAKAT ? 'Zakat' : 'Infaq');
                                                $hp = null;

                                                if ($data->no_hp) {
                                                    $hp = preg_replace('/\D+/', '', $data->no_hp);

                                                    if (str_starts_with($hp, '0')) {
                                                        $hp = '62' . substr($hp, 1);
                                                    } elseif (str_starts_with($hp, '8')) {
                                                        $hp = '62' . $hp;
                                                    }
                                                }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="donasi-cell-title" title="<?php echo e($data->nama); ?>">
                                                        <?php echo e($data->nama); ?>

                                                    </div>
                                                    <div class="donasi-cell-meta" title="<?php echo e($data->email ?? '-'); ?>">
                                                        <?php echo e($data->email ?? '-'); ?>

                                                    </div>
                                                    <?php if($hp): ?>
                                                        <a href="https://wa.me/<?php echo e($hp); ?>" target="_blank"
                                                            class="donasi-contact-link" title="<?php echo e($data->no_hp); ?>">
                                                            <i class="fab fa-whatsapp"></i>
                                                            <span><?php echo e($data->no_hp); ?></span>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo e($isProgram ? 'badge-primary' : 'badge-secondary'); ?>">
                                                        <?php echo e($isProgram ? 'Program' : 'Umum'); ?>

                                                    </span>
                                                    <div class="donasi-cell-title mt-1" title="<?php echo e($donasiLabel); ?>">
                                                        <?php echo e($donasiLabel); ?>

                                                    </div>
                                                    <?php if($data->feedback): ?>
                                                        <div class="donasi-feedback" title="<?php echo e($data->feedback); ?>">
                                                            <?php echo e(\Illuminate\Support\Str::limit($data->feedback, 56)); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="donasi-nominal">Rp <?php echo e(number_format($data->total_donasi, 0, ',', '.')); ?></td>
                                                <td>
                                                    <?php if($data->status_donasi == \App\Models\DonasiKilau::DONASI_PENDING): ?>
                                                        <span class="badge badge-warning">Pending</span>
                                                    <?php elseif($data->status_donasi == \App\Models\DonasiKilau::DONASI_AKTIVE): ?>
                                                        <span class="badge badge-success">Berdonasi</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Expired</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $syncStatus = $data->km12_sync_status ?: null;
                                                        $syncBadge = match ($syncStatus) {
                                                            'synced' => ['badge-success', 'Synced'],
                                                            'pending' => ['badge-warning', 'Menunggu Sync'],
                                                            'syncing' => ['badge-info', 'Syncing'],
                                                            'failed' => ['badge-danger', 'Failed'],
                                                            default => ['badge-light', 'Belum Sync'],
                                                        };
                                                    ?>
                                                    <?php if($data->status_donasi == \App\Models\DonasiKilau::DONASI_AKTIVE): ?>
                                                        <span class="badge <?php echo e($syncBadge[0]); ?>"><?php echo e($syncBadge[1]); ?></span>
                                                        <?php if($data->km12_transaksi_id): ?>
                                                            <div class="donasi-cell-meta">#<?php echo e($data->km12_transaksi_id); ?></div>
                                                        <?php endif; ?>
                                                        <?php if(in_array($syncStatus, ['synced', 'failed'], true)): ?>
                                                            <form method="POST" action="<?php echo e(route('admin.donasi.retryKm12Sync', $data)); ?>" class="mt-1"
                                                                <?php if($syncStatus === 'synced'): ?> onsubmit="return confirm('Sinkronisasi ulang transaksi ini ke KM12?')" <?php endif; ?>>
                                                                <?php echo csrf_field(); ?>
                                                                <button type="submit" class="btn btn-xs btn-outline-primary py-0 px-1"
                                                                    title="<?php echo e($syncStatus === 'synced' ? 'Resync KM12' : 'Retry sync KM12'); ?>">
                                                                    <i class="fas fa-sync-alt"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                        <?php if($data->km12_sync_error): ?>
                                                            <div class="donasi-cell-meta text-danger" title="<?php echo e($data->km12_sync_error); ?>">
                                                                <?php echo e(\Illuminate\Support\Str::limit($data->km12_sync_error, 28)); ?>

                                                            </div>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="badge badge-light">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-nowrap"><?php echo e($data->formatted_date); ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-link text-danger p-0 btnHapusDonasi"
                                                        data-id="<?php echo e($data->id); ?>"
                                                        title="Hapus Donasi">
                                                        <i class="fas fa-trash-alt donasi-action"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0">Mapping Program CMS ke KM12</h4>
                                <div class="text-muted small">
                                    Failed: <?php echo e(number_format($summary['km12_failed'], 0, ',', '.')); ?> |
                                    Belum sync: <?php echo e(number_format($summary['km12_unsynced'], 0, ',', '.')); ?>

                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if(empty($km12ProgramOptions)): ?>
                                <div class="alert alert-warning mb-3">
                                    Opsi program KM12 tidak tersedia. Pastikan KM12 aktif dan internal key sama.
                                </div>
                            <?php endif; ?>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width:35%;">Program CMS</th>
                                            <th style="width:40%;">Program KM12</th>
                                            <th style="width:12%;">Aktif</th>
                                            <th style="width:13%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $programMappings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $mapping = $program->relationLoaded('km12Mapping') ? $program->km12Mapping : null;
                                                $formId = 'mapping-form-' . $program->id;
                                                $selectedProgramId = $mapping?->km12_program_penerimaan_id;
                                                $selectedInOptions = collect($km12ProgramOptions)
                                                    ->contains(fn ($option) => (int) ($option['id'] ?? 0) === (int) $selectedProgramId);
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="donasi-cell-title"><?php echo e($program->judul); ?></div>
                                                    <div class="donasi-cell-meta">CMS #<?php echo e($program->id); ?></div>
                                                </td>
                                                <td>
                                                    <form id="<?php echo e($formId); ?>" method="POST"
                                                        action="<?php echo e(route('admin.donasi.programMapping.update', $program)); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PUT'); ?>
                                                    </form>
                                                    <select form="<?php echo e($formId); ?>" name="km12_program_penerimaan_id"
                                                        class="form-control form-control-sm km12-program-select"
                                                        data-form-id="<?php echo e($formId); ?>">
                                                        <option value="">Tidak dimapping</option>
                                                        <?php if($selectedProgramId && !$selectedInOptions): ?>
                                                            <option value="<?php echo e($selectedProgramId); ?>"
                                                                data-source-id="<?php echo e($mapping?->km12_sumber_dana_id); ?>"
                                                                data-source-name="<?php echo e($mapping?->km12_sumber_dana_name); ?>"
                                                                data-program-name="<?php echo e($mapping?->km12_program_name); ?>"
                                                                selected>
                                                                <?php echo e($mapping?->km12_program_name ?: ('KM12 #' . $selectedProgramId)); ?>

                                                            </option>
                                                        <?php endif; ?>
                                                        <?php $__currentLoopData = $km12ProgramOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                                                $source = $option['sumber_dana'] ?? [];
                                                                $optionId = (int) ($option['id'] ?? 0);
                                                            ?>
                                                            <option value="<?php echo e($optionId); ?>"
                                                                data-source-id="<?php echo e($source['id'] ?? ''); ?>"
                                                                data-source-name="<?php echo e($source['name'] ?? ''); ?>"
                                                                data-program-name="<?php echo e($option['name'] ?? ''); ?>"
                                                                <?php if((int) $selectedProgramId === $optionId): echo 'selected'; endif; ?>>
                                                                <?php echo e($option['name'] ?? ('KM12 #' . $optionId)); ?>

                                                                <?php if(!empty($source['name'])): ?>
                                                                    - <?php echo e($source['name']); ?>

                                                                <?php endif; ?>
                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                    <input form="<?php echo e($formId); ?>" type="hidden" name="km12_sumber_dana_id"
                                                        value="<?php echo e($mapping?->km12_sumber_dana_id); ?>">
                                                    <input form="<?php echo e($formId); ?>" type="hidden" name="km12_program_name"
                                                        value="<?php echo e($mapping?->km12_program_name); ?>">
                                                    <input form="<?php echo e($formId); ?>" type="hidden" name="km12_sumber_dana_name"
                                                        value="<?php echo e($mapping?->km12_sumber_dana_name); ?>">
                                                </td>
                                                <td>
                                                    <input form="<?php echo e($formId); ?>" type="checkbox" name="is_active" value="1"
                                                        <?php if(!$mapping || $mapping->is_active): echo 'checked'; endif; ?>>
                                                </td>
                                                <td class="text-end">
                                                    <button form="<?php echo e($formId); ?>" type="submit" class="btn btn-sm btn-primary">
                                                        Simpan
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0">Grafik Donasi</h4>
                                <button class="btn btn-outline-primary btn-sm" type="button" data-toggle="collapse"
                                    data-target="#donasiChartSection" aria-expanded="false"
                                    aria-controls="donasiChartSection" id="toggleDonasiChart">
                                    <i class="fas fa-chart-bar"></i> Tampilkan Grafik
                                </button>
                            </div>
                        </div>
                        <div class="collapse" id="donasiChartSection">
                            <div class="card-body">
                                <div id="donasiFilter" class="btn-group btn-group-sm mb-3" role="group">
                                    <button class="btn btn-outline-primary" data-group="daily">Harian</button>
                                    <button class="btn btn-outline-primary active" data-group="monthly">Bulanan</button>
                                    <button class="btn btn-outline-primary" data-group="yearly">Tahunan</button>
                                </div>
                                <canvas id="donasiChart" height="90"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalHapusDonasi" tabindex="-1" aria-labelledby="modalHapusLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                        <div class="modal-header bg-gradient bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="modalHapusLabel">
                                <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Donasi
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body py-4 text-center">
                            <p class="fs-5 mb-0 text-danger">
                                Anda yakin ingin menghapus data donasi ini?<br>
                                <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
                            </p>
                        </div>
                        <div class="modal-footer d-flex justify-content-between px-4 pb-4">
                            <form id="hapusDonasiForm" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="fas fa-trash-alt me-1"></i> Ya, Hapus
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        $(function() {
            const deleteUrl = '<?php echo e(url('/admin/donasi')); ?>';
            const retryUrlBase = '<?php echo e(url('/admin/donasi')); ?>';
            const csrfToken = '<?php echo e(csrf_token()); ?>';
            const modalHapus = new bootstrap.Modal(document.getElementById('modalHapusDonasi'));
            const table = $('#donasi-table').DataTable({
                autoWidth: false,
                searching: false,
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        searchable: false
                    }
                ],
                order: []
            });

            function escapeHtml(value) {
                return $('<div>').text(value ?? '-').html();
            }

            function statusBadge(status) {
                if (Number(status) === <?php echo e(\App\Models\DonasiKilau::DONASI_PENDING); ?>) {
                    return '<span class="badge badge-warning">Pending</span>';
                }

                if (Number(status) === <?php echo e(\App\Models\DonasiKilau::DONASI_AKTIVE); ?>) {
                    return '<span class="badge badge-success">Berdonasi</span>';
                }

                return '<span class="badge badge-secondary">Expired</span>';
            }

            function km12Cell(item) {
                if (Number(item.status_donasi) !== <?php echo e(\App\Models\DonasiKilau::DONASI_AKTIVE); ?>) {
                    return '<span class="badge badge-light">-</span>';
                }

                const status = item.km12_sync_status || null;
                const badge = {
                    synced: ['badge-success', 'Synced'],
                    pending: ['badge-warning', 'Menunggu Sync'],
                    syncing: ['badge-info', 'Syncing'],
                    failed: ['badge-danger', 'Failed'],
                }[status] || ['badge-light', 'Belum Sync'];
                const transaksi = item.km12_transaksi_id
                    ? `<div class="donasi-cell-meta">#${escapeHtml(item.km12_transaksi_id)}</div>`
                    : '';
                const syncTitle = status === 'synced' ? 'Resync KM12' : 'Retry sync KM12';
                const syncConfirm = status === 'synced'
                    ? ' onsubmit="return confirm(\'Sinkronisasi ulang transaksi ini ke KM12?\')"'
                    : '';
                const retry = ['synced', 'failed'].includes(status)
                    ? `<form method="POST" action="${retryUrlBase}/${item.id}/retry-km12-sync" class="mt-1"${syncConfirm}>
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn btn-xs btn-outline-primary py-0 px-1" title="${syncTitle}">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </form>`
                    : '';
                const error = item.km12_sync_error
                    ? `<div class="donasi-cell-meta text-danger" title="${escapeHtml(item.km12_sync_error)}">${escapeHtml(limitText(item.km12_sync_error, 28))}</div>`
                    : '';

                return `<span class="badge ${badge[0]}">${badge[1]}</span>${transaksi}${retry}${error}`;
            }

            function formatRupiah(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0
                }).format(Number(value || 0));
            }

            function formatTanggal(value) {
                if (!value) {
                    return '-';
                }

                return new Date(value).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function formatNoHp(value) {
                if (!value) {
                    return '-';
                }

                let hp = String(value).replace(/\D+/g, '');

                if (hp.startsWith('0')) {
                    hp = `62${hp.substring(1)}`;
                } else if (hp.startsWith('8')) {
                    hp = `62${hp}`;
                }

                return `<a href="https://wa.me/${hp}" target="_blank" class="donasi-contact-link" title="${escapeHtml(value)}">
                    <i class="fab fa-whatsapp"></i><span>${escapeHtml(value)}</span>
                </a>`;
            }

            function jenisDonasi(item) {
                if (Number(item.type_donasi) === <?php echo e(\App\Models\DonasiKilau::TYPE_DONASI_PROGRAM); ?>) {
                    return item.program?.judul || 'Program Tanpa Judul';
                }

                return Number(item.opsional_umum) === <?php echo e(\App\Models\DonasiKilau::OPSIONAL_UMUM_ZAKAT); ?>

                    ? 'Zakat'
                    : 'Infaq';
            }

            function tipeDonasiBadge(item) {
                return Number(item.type_donasi) === <?php echo e(\App\Models\DonasiKilau::TYPE_DONASI_PROGRAM); ?>

                    ? '<span class="badge badge-primary">Program</span>'
                    : '<span class="badge badge-secondary">Umum</span>';
            }

            function limitText(value, maxLength = 56) {
                const text = String(value || '');

                return text.length > maxLength ? `${text.substring(0, maxLength - 3)}...` : text;
            }

            function donaturCell(item) {
                return `<div class="donasi-cell-title" title="${escapeHtml(item.nama)}">${escapeHtml(item.nama)}</div>
                    <div class="donasi-cell-meta" title="${escapeHtml(item.email)}">${escapeHtml(item.email)}</div>
                    ${formatNoHp(item.no_hp)}`;
            }

            function donasiCell(item) {
                const detail = jenisDonasi(item);
                const feedback = item.feedback
                    ? `<div class="donasi-feedback" title="${escapeHtml(item.feedback)}">${escapeHtml(limitText(item.feedback))}</div>`
                    : '';

                return `${tipeDonasiBadge(item)}
                    <div class="donasi-cell-title mt-1" title="${escapeHtml(detail)}">${escapeHtml(detail)}</div>
                    ${feedback}`;
            }

            function updateTable(data) {
                table.clear();

                data.forEach(item => {
                    table.row.add([
                        donaturCell(item),
                        donasiCell(item),
                        `<span class="donasi-nominal">${formatRupiah(item.total_donasi)}</span>`,
                        statusBadge(item.status_donasi),
                        km12Cell(item),
                        `<span class="text-nowrap">${formatTanggal(item.created_at)}</span>`,
                        `<button class="btn btn-sm btn-link text-danger p-0 btnHapusDonasi"
                            data-id="${item.id}" title="Hapus Donasi">
                            <i class="fas fa-trash-alt donasi-action"></i>
                        </button>`
                    ]);
                });

                table.draw();
            }

            $('body').on('click', '.btnHapusDonasi', function() {
                $('#hapusDonasiForm').attr('action', `${deleteUrl}/${$(this).data('id')}`);
                modalHapus.show();
            });

            $('.km12-program-select').on('change', function() {
                const selected = this.options[this.selectedIndex];
                const formId = this.dataset.formId;
                const form = document.getElementById(formId);

                if (!form) return;

                const sourceId = document.querySelector(`[form="${formId}"][name="km12_sumber_dana_id"]`);
                const programName = document.querySelector(`[form="${formId}"][name="km12_program_name"]`);
                const sourceName = document.querySelector(`[form="${formId}"][name="km12_sumber_dana_name"]`);

                if (sourceId) sourceId.value = selected.dataset.sourceId || '';
                if (programName) programName.value = selected.dataset.programName || '';
                if (sourceName) sourceName.value = selected.dataset.sourceName || '';
            });

            const fmtMonth = ym => new Date(`${ym}-01`).toLocaleDateString('id-ID', {
                month: 'short',
                year: 'numeric'
            });

            let donasiChart = null;
            let chartLoaded = false;
            let activeChartGroup = 'monthly';

            function drawDonasi(labels, data) {
                donasiChart?.destroy();
                donasiChart = new Chart(document.getElementById('donasiChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Total Donasi',
                            data,
                            backgroundColor: '#1363c6',
                            borderColor: '#0d4b8c',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            function loadDonasi(group = activeChartGroup) {
                activeChartGroup = group;

                $('#donasiFilter button').removeClass('active')
                    .filter(`[data-group="${group}"]`).addClass('active');

                $.ajax({
                    url: '<?php echo e(route('admin.donasi.chartData')); ?>',
                    data: {
                        group
                    },
                    dataType: 'json',
                    success: rows => {
                        if (!Array.isArray(rows)) {
                            rows = [rows];
                        }

                        let labels = rows.map(row => row.label);

                        if (group === 'monthly') {
                            labels = labels.map(fmtMonth);
                        }

                        drawDonasi(labels, rows.map(row => Number(row.total)));
                        chartLoaded = true;
                    },
                    error: (jq) => {
                        console.error('[donasiData]', jq.status);
                        alert('Tidak bisa memuat data Donasi. Lihat console / laravel.log');
                    }
                });
            }

            $('#donasiFilter button').on('click', function() {
                loadDonasi($(this).data('group'));
            });

            $('#donasiChartSection').on('shown.bs.collapse', function() {
                $('#toggleDonasiChart').html('<i class="fas fa-chart-bar"></i> Sembunyikan Grafik');

                if (!chartLoaded) {
                    loadDonasi('monthly');
                } else {
                    setTimeout(() => donasiChart?.resize(), 50);
                }
            });

            $('#donasiChartSection').on('hidden.bs.collapse', function() {
                $('#toggleDonasiChart').html('<i class="fas fa-chart-bar"></i> Tampilkan Grafik');
            });

            $('#filter-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '<?php echo e(route('admin.donasi.filter')); ?>',
                    data: {
                        keyword: $('#keywordFilter').val(),
                        month: $('#monthFilter').val(),
                        year: $('#yearFilter').val(),
                        status: $('#statusFilter').val()
                    },
                    dataType: 'json',
                    success: res => {
                        updateTable(res.data || []);

                        if ($('#donasiChartSection').hasClass('show')) {
                            drawDonasi(
                                (res.labels || []).map(fmtMonth),
                                (res.totals || []).map(total => Number(total))
                            );
                            chartLoaded = true;
                        }
                    }
                });
            });

            $('#resetFilter').on('click', function() {
                $('#keywordFilter').val('');
                $('#monthFilter').val('');
                $('#yearFilter').val('');
                $('#statusFilter').val('');
                $('#filter-form').trigger('submit');
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views/AdminPage/Donasi/index.blade.php ENDPATH**/ ?>