<?php $__env->startSection('style'); ?>
    <style>
        .profile-wrapper {
            max-width: 760px;
            margin: auto
        }

        .avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f1f3f5
        }

        .label {
            font-size: .85rem;
            color: #6c757d
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .copy-input {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: .9rem;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="container py-5">
        <div class="profile-wrapper card shadow-sm p-4">
            <?php
                $isEmployeeReferral = ($user['referral_type'] ?? null) === \App\Models\ReferralCode::TYPE_KILAU_EMPLOYEE;
            ?>
            <div class="text-center mb-4">
                <img id="avatar" src="<?php echo e($user['foto'] ?: asset('assets_admin/img/noimage.jpg')); ?>" class="avatar mb-2"
                    alt="Avatar">
                <h5 id="user-name" class="mb-0 fw-semibold"><?php echo e($user['nama'] ?? 'Pengguna'); ?></h5>
                <div id="user-level" class="label"><?php echo e($user['level'] ?? 'donatur'); ?></div>
                <div class="mt-2">
                    <span class="badge <?php echo e($isEmployeeReferral ? 'bg-success' : 'bg-primary'); ?>">
                        <?php echo e($user['referral_type_label'] ?? 'User CMS'); ?>

                    </span>
                </div>
            </div>

            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item d-flex justify-content-between gap-3">
                    <span class="label">Email</span>
                    <span id="user-email" class="text-break text-end"><?php echo e($user['email'] ?? '-'); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between gap-3">
                    <span class="label">No. HP</span>
                    <span id="user-phone" class="text-break text-end"><?php echo e($user['phone'] ?? '-'); ?></span>
                </li>
                <?php if($isEmployeeReferral): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="label">Status</span>
                        <span><span class="badge bg-success">Karyawan Kilau</span></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="label">Jabatan</span><span><?php echo e($user['jabatan'] ?? '-'); ?></span>
                    </li>
                <?php endif; ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="label">Kode Referral</span><span
                        id="user-referral" class="copy-input"><?php echo e($user['referral_code'] ?? '-'); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="label">Status Akun</span>
                    <span id="user-status"><span class="badge bg-success">Aktif</span></span>
                </li>
            </ul>

            <?php if(!empty($user['referral_link'])): ?>
                <div class="mb-3">
                    <div class="label mb-1">Link Referral</div>
                    <div class="input-group">
                        <input type="text" class="form-control copy-input" value="<?php echo e($user['referral_link']); ?>" readonly>
                        <button type="button" class="btn btn-outline-primary btn-copy" data-copy="<?php echo e($user['referral_link']); ?>">
                            Salin
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-grid gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#editProfileModal">
                    <i class="fas fa-pen me-1"></i> Edit Profil
                </button>
                <a href="<?php echo e(route('pointreferall')); ?>" class="btn btn-primary">
                    Dashboard Fundraiser
                </a>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('profile.update')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0 ps-3">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="profileName" class="form-label">Nama</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="profileName" name="name" value="<?php echo e(old('name', $user['nama'])); ?>" maxlength="255"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="profileEmail" class="form-label">Email</label>
                            <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="profileEmail" name="email" value="<?php echo e(old('email', $user['email'])); ?>" maxlength="255"
                                required>
                        </div>
                        <div>
                            <label for="profilePhone" class="form-label">No. HP</label>
                            <input type="tel" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="profilePhone" name="phone" value="<?php echo e(old('phone', $user['phone'])); ?>" maxlength="30">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="container pb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Riwayat Donasi Saya</h5>
                        <small class="text-muted">Ringkasan aktivitas donasi Anda</small>
                    </div>
                    
                </div>
            </div>

            <?php
                // Hitung ringkasan
                $totalTransaksi = 0;
                $totalNominal = 0;
                $jumlahAktif = 0;
                $jumlahPending = 0;
                $jumlahExpired = 0;

                $items = method_exists($histories, 'getCollection') ? $histories->getCollection() : $histories;

                foreach ($items as $h) {
                    $d = $h->donasikilau;
                    $nom = $h->total_donasi ?? ($d->total_donasi ?? 0);
                    $totalTransaksi++;
                    $totalNominal += (float) $nom;

                    $statusVal = $d->status_donasi ?? $h->status_donasi;
                    if ((int) $statusVal === \App\Models\DonasiKilau::DONASI_AKTIVE) {
                        $jumlahAktif++;
                    } elseif ((int) $statusVal === \App\Models\DonasiKilau::DONASI_EXPIRED) {
                        $jumlahExpired++;
                    } else {
                        $jumlahPending++;
                    }
                }

                // helper nomor baris dengan dukungan pagination
                $rowStart =
                    method_exists($histories, 'currentPage') && method_exists($histories, 'perPage')
                        ? ($histories->currentPage() - 1) * $histories->perPage()
                        : 0;
            ?>

            
            <div class="px-3 pt-3">
                <div class="row g-3">
                    <div class="col-6 col-md">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="small text-muted">Total Transaksi</div>
                            <div class="fs-5 fw-semibold"><?php echo e($totalTransaksi); ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="small text-muted">Total Nominal</div>
                            <div class="fs-5 fw-semibold">Rp <?php echo e(number_format($totalNominal, 0, ',', '.')); ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="small text-muted">Aktif</div>
                            <div class="fs-5 fw-semibold text-success"><?php echo e($jumlahAktif); ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="small text-muted">Pending</div>
                            <div class="fs-5 fw-semibold text-warning"><?php echo e($jumlahPending); ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="small text-muted">Expired</div>
                            <div class="fs-5 fw-semibold text-secondary"><?php echo e($jumlahExpired); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <caption class="px-3 pt-3">Daftar riwayat donasi terbaru</caption>
                        <thead class="table-light">
                            <tr>
                                <th style="width:72px;">No</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Program / Opsional</th>
                                <th class="text-end">Nominal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $d = $h->donasikilau;

                                    // nominal: prefer dari history; fallback ke relasi donasi
                                    $nominal = $h->total_donasi ?? ($d->total_donasi ?? 0);

                                    // jenis & keterangan
                                    $jenis = '-';
                                    $keterangan = '-';
                                    if ($d) {
                                        if ((int) $d->type_donasi === \App\Models\DonasiKilau::TYPE_DONASI_PROGRAM) {
                                            $jenis = 'Program';
                                            $keterangan = $d->program?->judul ?? 'Tanpa Program';
                                        } elseif ((int) $d->type_donasi === \App\Models\DonasiKilau::TYPE_DONASI_UMUM) {
                                            $jenis = 'Umum';
                                            $keterangan = $opsionalUmumMap[$d->opsional_umum ?? 0] ?? 'Umum';
                                        }
                                    }

                                    // status
                                    $statusVal = $d->status_donasi ?? $h->status_donasi;
                                    if ((int) $statusVal === \App\Models\DonasiKilau::DONASI_AKTIVE) {
                                        $statusBadge = '<span class="badge bg-success">Donasi</span>';
                                    } elseif ((int) $statusVal === \App\Models\DonasiKilau::DONASI_EXPIRED) {
                                        $statusBadge = '<span class="badge bg-secondary">Expired</span>';
                                    } else {
                                        $statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
                                    }
                                ?>
                                <tr title="Transaksi #<?php echo e($h->id); ?>">
                                    <td><?php echo e($rowStart + $idx + 1); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($h->created_at)->format('d M Y H:i')); ?></td>
                                    <td><?php echo e($jenis); ?></td>
                                    <td><?php echo e($keterangan); ?></td>
                                    <td class="text-end">Rp <?php echo e(number_format($nominal, 0, ',', '.')); ?></td>
                                    <td><?php echo $statusBadge; ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat donasi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(method_exists($histories, 'links')): ?>
                    <div class="p-3">
                        <?php echo e($histories->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            <?php if($errors->any()): ?>
                bootstrap.Modal.getOrCreateInstance(document.getElementById('editProfileModal')).show();
            <?php endif; ?>

            function fallbackCopy(text) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.setAttribute('readonly', '');
                textarea.style.position = 'absolute';
                textarea.style.left = '-9999px';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
            }

            document.querySelectorAll('.btn-copy').forEach((button) => {
                button.addEventListener('click', async () => {
                    const text = button.dataset.copy || '';

                    if (!text) return;

                    try {
                        if (navigator.clipboard && window.isSecureContext) {
                            await navigator.clipboard.writeText(text);
                        } else {
                            fallbackCopy(text);
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Link disalin',
                            timer: 1200,
                            showConfirmButton: false,
                        });
                    } catch (e) {
                        Swal.fire({ icon: 'info', title: 'Salin manual', text });
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\Auth\profile.blade.php ENDPATH**/ ?>