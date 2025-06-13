@extends('App.master')

@section('style')
<style>
/* sentuhan kecil saja â€“ selebihnya pakai kelas Bootstrap */
.card-referral{box-shadow:0 4px 12px rgba(0,0,0,.08);}
</style>
@endsection

@section('content')
<div class="container py-4">

    {{-- <h3 class="mb-3">ðŸ’° Penghasilan Referral Anda</h3> --}}
     <h3 class="mb-3">
        <i class="fas fa-dollar-sign text-success me-2"></i>
        Penghasilan Referral Anda
    </h3>

    <p class="text-muted">
        Setiap klik link referral bernilai <strong>Rp1.000</strong>.  
        Klik <em>Ajukan Pencairan</em> untuk menarik saldo ke rekening Anda.
    </p>

    @foreach ($referrals as $item)
        @php
            $sub = $item->click_count * 1000;
            $withdraw = $withdrawals[$item->id] ?? null;
        @endphp

        <div class="card card-referral mb-4">
            <div class="card-body">
                <h5 class="card-title mb-1">
                    {{ $item->program->judul ?? 'Program tidak ditemukan' }}
                </h5>

                <ul class="list-unstyled mb-3">
                    <li>Jumlah Klik : <strong>{{ $item->click_count }}</strong></li>
                    <li>Total Uang Terkumpul : <strong>Rp {{ number_format($sub, 0, ',', '.') }}</strong></li>
                </ul>

                @if ($withdraw)
                    <div class="alert alert-warning py-2 mb-2">
                        <i class="fas fa-clock me-1"></i>
                        Pencairan <strong>sedang diproses</strong> sejak {{ \Carbon\Carbon::parse($withdraw->requested_at)->diffForHumans() }}.
                    </div>
                    <button class="btn btn-secondary btn-sm" disabled>
                        <i class="fas fa-spinner fa-spin me-1"></i> Menunggu Verifikasi
                    </button>
                @else
                    <button class="btn btn-primary btn-sm btn-withdraw"
                            data-id="{{ $item->id }}"
                            data-program="{{ $item->program->judul }}">
                        <i class="fas fa-hand-holding-usd me-1"></i> Ajukan Pencairan
                    </button>
                @endif
            </div>
        </div>
    @endforeach


   @if ($totalUang)
        <div class="alert alert-success">
            <strong>
                Total Uang Referral Terkumpul untuk {{ $userName ?? 'Anda' }}:
            </strong>
            Rp {{ number_format($totalUang, 0, ',', '.') }}
        </div>
    @endif

</div>

<!-- Modal -->
<div class="modal fade" id="withdrawalModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title text-white">Pengajuan Pencairan</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="{{ route('referral.withdraw') }}">
        @csrf
        <input type="hidden" name="program_referral_id" id="referralId">

        <div class="modal-body">
            <p class="fw-semibold" id="programName"></p>

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="nama_lengkap"
                       value="{{ $userName }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email"
                       value="{{ $userEmail }}" readonly>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">No. HP</label>
                    <input type="text" class="form-control" name="no_hp" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Bank</label>
                    <input type="text" class="form-control" name="nama_bank" required>
                </div>
                <div class="col-12">
                    <label class="form-label">No. Rekening</label>
                    <input type="text" class="form-control" name="no_rekening" required>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Ajukan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal   = new bootstrap.Modal('#withdrawalModal');
    const idInput = document.getElementById('referralId');
    const nameEl  = document.getElementById('programName');

    document.querySelectorAll('.btn-withdraw').forEach(btn => {
        btn.addEventListener('click', () => {
            idInput.value = btn.dataset.id;
            nameEl.textContent = btn.dataset.program;
            modal.show();
        });
    });
});
</script>
@endsection
