@extends('App.master')

@section('style')
<style>
.card-referral{box-shadow:0 4px 12px rgba(0,0,0,.08);}
.copy-input{
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: .9rem;
}
</style>
@endsection

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

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
                    @if (!empty($employeeReferral['photo_url']))
                        <div class="mb-3">
                            <img src="{{ $employeeReferral['photo_url'] }}" alt="Foto karyawan" class="rounded-circle" style="width:72px;height:72px;object-fit:cover;">
                        </div>
                    @endif
                    <div class="text-muted small">Nama</div>
                    <div class="fw-semibold">{{ $userName ?? '-' }}</div>
                    <div class="text-muted small mt-2">Email</div>
                    <div class="fw-semibold">{{ $userEmail ?? '-' }}</div>
                    <div class="text-muted small mt-2">Jenis Referral</div>
                    <span class="badge {{ $referralType === \App\Models\ReferralCode::TYPE_KILAU_EMPLOYEE ? 'bg-success' : 'bg-primary' }}">
                        {{ $referralTypeLabel }}
                    </span>
                    @if ($employeeReferral)
                        <div class="text-muted small mt-2">Karyawan</div>
                        <div class="fw-semibold">{{ $employeeReferral['name'] ?? '-' }}</div>
                        @if (!empty($employeeReferral['position']))
                            <div class="text-muted small">{{ $employeeReferral['position'] }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-referral h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Link Referral</div>
                    <div class="input-group">
                        <input type="text" class="form-control copy-input" value="{{ $shareLinkUmum }}" readonly>
                        <button type="button" class="btn btn-outline-primary btn-copy" data-copy="{{ $shareLinkUmum }}">
                            Salin
                        </button>
                    </div>
                    <div class="text-muted small mt-2">
                        ID Fundraiser: <span class="copy-input">{{ $affiliateSub }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!$affiliateColumnReady)
        <div class="alert alert-warning">
            Kolom <code>affiliate_sub</code> belum tersedia di database KilauCMS. Jalankan <code>php artisan migrate</code> agar rekap donasi fundraiser bisa tampil.
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-referral h-100">
                <div class="card-body">
                    <div class="text-muted small">Nominal Berhasil</div>
                    <div class="fs-5 fw-semibold">Rp {{ number_format((float) $totalDonasi, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-referral h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Transaksi</div>
                    <div class="fs-5 fw-semibold">{{ $totalTransaksi }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-referral h-100">
                <div class="card-body">
                    <div class="text-muted small">Pending</div>
                    <div class="fs-5 fw-semibold">{{ $totalPending }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-referral h-100">
                <div class="card-body">
                    <div class="text-muted small">Berhasil</div>
                    <div class="fs-5 fw-semibold">{{ $totalAktif }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-referral mb-4">
        <div class="card-body">
            <h5 class="mb-3">Link Fundraiser per Program</h5>
            @if ($programs->isEmpty())
                <div class="text-muted">Belum ada program aktif.</div>
            @else
                @php
                    $baseUrl = url('/');
                    $aff = urlencode((string) $affiliateSub);
                @endphp
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
                            @foreach ($programs as $program)
                                @php
                                    $link = $baseUrl . '?aff=' . $aff . '&modal=' . $program->id;
                                @endphp
                                <tr>
                                    <td>{{ $program->id }}</td>
                                    <td>{{ $program->judul }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm copy-input" value="{{ $link }}" readonly>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-copy" data-copy="{{ $link }}">Salin</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card card-referral">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">Donasi Terbaru</h5>
                @if ($affiliateColumnReady)
                    <span class="text-muted small">
                        @if ($totalTransaksi > 0)
                            Menampilkan {{ $donasiTerbaru->count() }} dari {{ $totalTransaksi }} transaksi
                        @else
                            Belum ada transaksi
                        @endif
                    </span>
                @endif
            </div>
            @if (!$affiliateColumnReady)
                <div class="text-muted">Rekap donasi belum tersedia karena kolom database belum dimigrasi.</div>
            @elseif ($donasiTerbaru->isEmpty())
                <div class="text-center text-muted py-4">
                    <div class="mb-2">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>Belum ada donasi yang masuk dari link fundraiser Anda.</div>
                </div>
            @else
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
                            @foreach ($donasiTerbaru as $donasi)
                                @php
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
                                @endphp
                                <tr>
                                    <td>{{ $donasi->id }}</td>
                                    <td>{{ $jenis }}</td>
                                    <td>{{ $donasi->nama }}</td>
                                    <td>Rp {{ number_format((float) $donasi->total_donasi, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $statusBadgeClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>{{ optional($donasi->created_at)->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
@endsection
