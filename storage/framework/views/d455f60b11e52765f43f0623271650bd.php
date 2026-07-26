<?php $__env->startSection('style'); ?>
<style>
.card-referral{box-shadow:0 4px 12px rgba(0,0,0,.08);}
.copy-input{
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: .9rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <h3 class="mb-2">
        <i class="fas fa-hand-holding-heart text-danger me-2"></i> Dashboard Fundraiser
    </h3>
    <p class="text-muted mb-4">
        Kelola link fundraiser dan pantau donasi yang teratribusi ke akun Anda.
    </p>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-referral h-100">
                <div class="card-body">
                    <?php if(!empty($employeeReferral['photo_url'])): ?>
                        <div class="mb-3">
                            <img src="<?php echo e($employeeReferral['photo_url']); ?>" alt="Foto karyawan" class="rounded-circle" style="width:72px;height:72px;object-fit:cover;">
                        </div>
                    <?php endif; ?>
                    <div class="text-muted small">Nama</div>
                    <div class="fw-semibold"><?php echo e($userName ?? '-'); ?></div>
                    <div class="text-muted small mt-2">Email</div>
                    <div class="fw-semibold"><?php echo e($userEmail ?? '-'); ?></div>
                    <div class="text-muted small mt-2">Jenis Referral</div>
                    <span class="badge <?php echo e($referralType === \App\Models\ReferralCode::TYPE_KILAU_EMPLOYEE ? 'bg-success' : 'bg-primary'); ?>">
                        <?php echo e($referralTypeLabel); ?>

                    </span>
                    <?php if($employeeReferral): ?>
                        <div class="text-muted small mt-2">Karyawan</div>
                        <div class="fw-semibold"><?php echo e($employeeReferral['name'] ?? '-'); ?></div>
                        <?php if(!empty($employeeReferral['position'])): ?>
                            <div class="text-muted small"><?php echo e($employeeReferral['position']); ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-referral h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Link Referral</div>
                    <div class="input-group">
                        <input type="text" class="form-control copy-input" value="<?php echo e($shareLinkUmum); ?>" readonly>
                        <button type="button" class="btn btn-outline-primary btn-copy" data-copy="<?php echo e($shareLinkUmum); ?>">
                            Salin
                        </button>
                    </div>
                    <div class="text-muted small mt-2">
                        ID Fundraiser: <span class="copy-input"><?php echo e($affiliateSub); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(!$affiliateColumnReady): ?>
        <div class="alert alert-warning">
            Kolom <code>affiliate_sub</code> belum tersedia di database KilauCMS. Jalankan <code>php artisan migrate</code> agar rekap donasi fundraiser bisa tampil.
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-referral h-100">
                <div class="card-body">
                    <div class="text-muted small">Nominal Berhasil</div>
                    <div class="fs-5 fw-semibold">Rp <?php echo e(number_format((float) $totalDonasi, 0, ',', '.')); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-referral h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Transaksi</div>
                    <div class="fs-5 fw-semibold"><?php echo e($totalTransaksi); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-referral h-100">
                <div class="card-body">
                    <div class="text-muted small">Pending</div>
                    <div class="fs-5 fw-semibold"><?php echo e($totalPending); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-referral h-100">
                <div class="card-body">
                    <div class="text-muted small">Berhasil</div>
                    <div class="fs-5 fw-semibold"><?php echo e($totalAktif); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-referral mb-4">
        <div class="card-body">
            <h5 class="mb-3">Link Fundraiser per Program</h5>
            <?php if($programs->isEmpty()): ?>
                <div class="text-muted">Belum ada program aktif.</div>
            <?php else: ?>
                <?php
                    $baseUrl = url('/');
                    $aff = urlencode((string) $affiliateSub);
                ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width:40px;">ID</th>
                                <th>Program</th>
                                <th style="width:320px;">Link</th>
                                <th style="width:80px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $link = $baseUrl . '?aff=' . $aff . '&modal=' . $program->id;
                                ?>
                                <tr>
                                    <td><?php echo e($program->id); ?></td>
                                    <td><?php echo e($program->judul); ?></td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm copy-input" value="<?php echo e($link); ?>" readonly>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-copy" data-copy="<?php echo e($link); ?>">Salin</button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card card-referral">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">Donasi Terbaru</h5>
                <?php if($affiliateColumnReady): ?>
                    <span class="text-muted small">
                        <?php if($totalTransaksi > 0): ?>
                            Menampilkan <?php echo e($donasiTerbaru->count()); ?> dari <?php echo e($totalTransaksi); ?> transaksi
                        <?php else: ?>
                            Belum ada transaksi
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>
            <?php if(!$affiliateColumnReady): ?>
                <div class="text-muted">Rekap donasi belum tersedia karena kolom database belum dimigrasi.</div>
            <?php elseif($donasiTerbaru->isEmpty()): ?>
                <div class="text-center text-muted py-4">
                    <div class="mb-2">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>Belum ada donasi yang masuk dari link fundraiser Anda.</div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width:60px;">ID</th>
                                <th>Jenis</th>
                                <th>Nama Donatur</th>
                                <th style="width:160px;">Nominal</th>
                                <th style="width:110px;">Status</th>
                                <th style="width:170px;">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $donasiTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $donasi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isProgram = (int) $donasi->type_donasi === \App\Models\DonasiKilau::TYPE_DONASI_PROGRAM;
                                    $jenis = $isProgram
                                        ? ('Program: ' . ($donasi->program->judul ?? '-'))
                                        : ('Umum: ' . ((int) $donasi->opsional_umum === \App\Models\DonasiKilau::OPSIONAL_UMUM_ZAKAT ? 'Zakat' : 'Infaq'));
                                    $statusLabel = match ((int) $donasi->status_donasi) {
                                        \App\Models\DonasiKilau::DONASI_AKTIVE => 'Berhasil',
                                        \App\Models\DonasiKilau::DONASI_EXPIRED => 'Expired',
                                        default => 'Menunggu',
                                    };
                                    $statusBadgeClass = match ($statusLabel) {
                                        'Berhasil' => 'bg-success',
                                        'Expired' => 'bg-secondary',
                                        default => 'bg-warning text-dark',
                                    };
                                ?>
                                <tr>
                                    <td><?php echo e($donasi->id); ?></td>
                                    <td><?php echo e($jenis); ?></td>
                                    <td><?php echo e($donasi->nama); ?></td>
                                    <td>Rp <?php echo e(number_format((float) $donasi->total_donasi, 0, ',', '.')); ?></td>
                                    <td>
                                        <span class="badge <?php echo e($statusBadgeClass); ?>">
                                            <?php echo e($statusLabel); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e(optional($donasi->created_at)->format('d M Y H:i')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    function fallbackCopy(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'absolute';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            Swal.fire({ icon: 'success', title: 'Link disalin', timer: 1200, showConfirmButton: false });
        } catch (e) {
            Swal.fire({ icon: 'info', title: 'Salin manual', text: text });
        }
        document.body.removeChild(textarea);
    }

    async function copyText(text) {
        if (!text) return;
        if (navigator.clipboard && window.isSecureContext) {
            try {
                await navigator.clipboard.writeText(text);
                Swal.fire({ icon: 'success', title: 'Link disalin', timer: 1200, showConfirmButton: false });
                return;
            } catch (e) {
                // fallback
            }
        }
        fallbackCopy(text);
    }

    document.querySelectorAll('.btn-copy').forEach(btn => {
        btn.addEventListener('click', () => copyText(btn.dataset.copy || ''));
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\LandingPageKilau\Components\point-referal.blade.php ENDPATH**/ ?>