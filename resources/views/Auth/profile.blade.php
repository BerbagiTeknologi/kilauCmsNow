{{-- resources/views/Auth/profile.blade.php --}}
@extends('App.master')

@section('style')
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

        .donation-pagination .pagination {
            flex-wrap: wrap;
            margin-bottom: 0;
        }
    </style>
@endsection

@section('content')
    {{-- Card Profile --}}
    <div class="container py-5">
        <div class="profile-wrapper card shadow-sm p-4">
            @php
                $isEmployeeReferral = ($user['referral_type'] ?? null) === \App\Models\ReferralCode::TYPE_KILAU_EMPLOYEE;
            @endphp
            <div class="text-center mb-4">
                <img id="avatar" src="{{ $user['foto'] ?: asset('assets_admin/img/noimage.jpg') }}" class="avatar mb-2"
                    alt="Avatar">
                <h5 id="user-name" class="mb-0 fw-semibold">{{ $user['nama'] ?? 'Pengguna' }}</h5>
                <div id="user-level" class="label">{{ $user['level'] ?? 'donatur' }}</div>
                <div class="mt-2">
                    <span class="badge {{ $isEmployeeReferral ? 'bg-success' : 'bg-primary' }}">
                        {{ $user['referral_type_label'] ?? 'User CMS' }}
                    </span>
                </div>
            </div>

            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item d-flex justify-content-between gap-3">
                    <span class="label">Email</span>
                    <span id="user-email" class="text-break text-end">{{ $user['email'] ?? '-' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between gap-3">
                    <span class="label">No. HP</span>
                    <span id="user-phone" class="text-break text-end">{{ $user['phone'] ?? '-' }}</span>
                </li>
                @if ($isEmployeeReferral)
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="label">Status</span>
                        <span><span class="badge bg-success">Karyawan Kilau</span></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="label">Jabatan</span><span>{{ $user['jabatan'] ?? '-' }}</span>
                    </li>
                @endif
                <li class="list-group-item d-flex justify-content-between">
                    <span class="label">Kode Referral</span><span
                        id="user-referral" class="copy-input">{{ $user['referral_code'] ?? '-' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="label">Status Akun</span>
                    <span id="user-status"><span class="badge bg-success">Aktif</span></span>
                </li>
            </ul>

            @if (!empty($user['referral_link']))
                <div class="mb-3">
                    <div class="label mb-1">Link Referral</div>
                    <div class="input-group">
                        <input type="text" class="form-control copy-input" value="{{ $user['referral_link'] }}" readonly>
                        <button type="button" class="btn btn-outline-primary btn-copy" data-copy="{{ $user['referral_link'] }}">
                            Salin
                        </button>
                    </div>
                </div>
            @endif

            <div class="d-grid gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#editProfileModal">
                    <i class="fas fa-pen me-1"></i> Edit Profil
                </button>
                <a href="{{ route('pointreferall') }}" class="btn btn-primary">
                    Dashboard Fundraiser
                </a>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="profileName" class="form-label">Nama</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="profileName" name="name" value="{{ old('name', $user['nama']) }}" maxlength="255"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="profileEmail" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="profileEmail" name="email" value="{{ old('email', $user['email']) }}" maxlength="255"
                                required>
                        </div>
                        <div>
                            <label for="profilePhone" class="form-label">No. HP</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                id="profilePhone" name="phone" value="{{ old('phone', $user['phone']) }}" maxlength="30">
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

    {{-- Riwayat Donasi --}}
    <div class="container pb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Riwayat Donasi Saya</h5>
                        <small class="text-muted">Ringkasan aktivitas donasi Anda</small>
                    </div>
                    {{-- opsional: tempatkan tombol/filters di sini --}}
                </div>
            </div>

            @php
                // Nomor baris tetap berurutan antarhalaman.
                $rowStart = ($histories->currentPage() - 1) * $histories->perPage();
            @endphp

            {{-- Info ringkas --}}
            <div class="px-3 pt-3">
                <div class="row g-3">
                    <div class="col-6 col-md">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="small text-muted">Total Transaksi</div>
                            <div class="fs-5 fw-semibold">{{ $historySummary['total_transaksi'] }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="small text-muted">Total Nominal</div>
                            <div class="fs-5 fw-semibold">Rp {{ number_format($historySummary['total_nominal'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="small text-muted">Aktif</div>
                            <div class="fs-5 fw-semibold text-success">{{ $historySummary['jumlah_aktif'] }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="small text-muted">Pending</div>
                            <div class="fs-5 fw-semibold text-warning">{{ $historySummary['jumlah_pending'] }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="small text-muted">Expired</div>
                            <div class="fs-5 fw-semibold text-secondary">{{ $historySummary['jumlah_expired'] }}</div>
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
                            @forelse($histories as $idx => $h)
                                @php
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
                                @endphp
                                <tr title="Transaksi #{{ $h->id }}">
                                    <td>{{ $rowStart + $idx + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($h->created_at)->format('d M Y H:i') }}</td>
                                    <td>{{ $jenis }}</td>
                                    <td>{{ $keterangan }}</td>
                                    <td class="text-end">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                                    <td>{!! $statusBadge !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat donasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($histories->total() > 0)
                    <div class="donation-pagination p-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $histories->firstItem() }}-{{ $histories->lastItem() }}
                            dari {{ $histories->total() }} transaksi
                        </small>

                        @if ($histories->hasPages())
                            {{ $histories->onEachSide(1)->links('pagination::bootstrap-5') }}
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if ($errors->any())
                bootstrap.Modal.getOrCreateInstance(document.getElementById('editProfileModal')).show();
            @endif

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
@endsection
