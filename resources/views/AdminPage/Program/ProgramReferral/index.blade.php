@extends('AdminPage.App.master')

@section('style')
<style>
    .badge-total {
        font-size: 13px;
        padding: 5px 10px;
        background-color: #eaf6ff;
        border: 1px solid #b6dfff;
        border-radius: 5px;
        color: #007bff;
        font-weight: 500;
    }

    .badge-warning { background-color: #ffc107; color: #000; }
    .badge-success { background-color: #28a745; color: #fff; }
    .badge-danger  { background-color: #dc3545; color: #fff; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Referral Fundraiser</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="referral-table" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul Program</th>
                                        <th>Nama Referer</th>
                                        <th>Jumlah Klik</th>
                                        <th>Total Uang</th>
                                        <th>Dibuat Pada</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                               <tbody>
                                    @foreach ($referrals as $index => $ref)

                                        @php
                                            $uang = $ref->click_count * 1000;

                                            // Mapping withdrawals + histories + program judul
                                            $withdrawals = $ref->withdrawals->map(function($w) use ($ref) {
                                                return [
                                                    'id' => $w->id,
                                                    'nama_lengkap' => $w->nama_lengkap,
                                                    'email' => $w->email,
                                                    'no_hp' => $w->no_hp,
                                                    'nama_bank' => $w->nama_bank,
                                                    'no_rekening' => $w->no_rekening,
                                                    'status' => $w->status,
                                                    'program_judul' => $ref->program->judul ?? '-',
                                                    'histories' => $w->histories->map(function($h) use ($ref) {
                                                            return [
                                                                'status' => $h->status,
                                                                'note' => $h->note,
                                                                'changed_at' => \Carbon\Carbon::parse($h->changed_at)->format('d M Y H:i'),
                                                                'program_judul' => $ref->program->judul ?? '-',
                                                            ];
                                                        }),
                                                ];
                                            });
                                        @endphp

                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $ref->program->judul ?? '-' }}</td>
                                            <td>{{ $ref->referer_name }}</td>
                                            <td><strong>{{ $ref->click_count }}</strong></td>
                                            <td><span class="badge-total">Rp {{ number_format($uang, 0, ',', '.') }}</span></td>
                                            <td>{{ \Carbon\Carbon::parse($ref->created_at)->format('d M Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group gap-2" role="group">
                                                    <button class="btn btn-primary btn-sm rounded-circle p-2 show-referral"
                                                        data-id="{{ $ref->id }}"
                                                        data-judul="{{ $ref->program->judul }}"
                                                        data-referer="{{ $ref->referer_name }}"
                                                        data-click="{{ $ref->click_count }}"
                                                        data-total="{{ $uang }}"
                                                        data-withdrawals='@json($withdrawals)'
                                                        data-toggle="modal" data-target="#referralDetailModal">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                    @endforeach
                                    </tbody>

                            </table>
                        </div>
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <strong>Catatan:</strong> Setiap klik referral bernilai <strong>Rp1.000</strong>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="referralDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Referral & Pengajuan Pencairan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>Judul Program:</strong> <span id="modalJudulProgram"></span></p>
        <p><strong>Referer:</strong> <span id="modalReferer"></span></p>
        <p><strong>Jumlah Klik:</strong> <span id="modalClick"></span></p>
        <p><strong>Total Uang:</strong> Rp <span id="modalTotal"></span></p>

        <hr>
        <h6>Pengajuan Pencairan</h6>
        <div id="withdrawalInfo"></div>

        <hr>
        <h6>Riwayat Status</h6>
     
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Program Yang Dipilih</th>
              <th>Status</th>
              <th>Catatan</th>
              <th>Waktu</th>
            </tr>
          </thead>
          <tbody id="historyTable"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <form id="approveForm" method="POST" action="">
          @csrf
          @method('PATCH')
          <button type="submit" class="btn btn-success d-none" id="btnApprove">Approve</button>
        </form>
        <form id="rejectForm" method="POST" action="">
          @csrf
          @method('PATCH')
          <button type="submit" class="btn btn-danger d-none" id="btnReject">Reject</button>
        </form>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#referral-table').DataTable({
            pageLength: 10,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Next",
                    previous: "Previous"
                },
                zeroRecords: "Data tidak ditemukan",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(disaring dari _MAX_ total data)"
            }
        });

        $('#referralDetailModal').on('hidden.bs.modal', function () {
            location.reload();
        });

        $('.show-referral').on('click', function () {
            const id = $(this).data('id');
            const judul = $(this).data('judul');
            const referer = $(this).data('referer');
            const click = $(this).data('click');
            const total = $(this).data('total');
            const withdrawals = $(this).data('withdrawals');

            $('#modalJudulProgram').text(judul);
            $('#modalReferer').text(referer);
            $('#modalClick').text(click);
            $('#modalTotal').text(Number(total).toLocaleString());

            let info = '';
            let approveButton = $('#btnApprove');
            let rejectButton = $('#btnReject');
            let approveForm = $('#approveForm');
            let rejectForm = $('#rejectForm');
            let historyTable = $('#historyTable');

            if (withdrawals.length > 0) {
                const pending = withdrawals.find(w => w.status === 'pending');
                const latest = withdrawals[0]; // untuk menampilkan info utama

                // Menampilkan informasi withdrawal terbaru (terlepas dari statusnya)
                const statusClass =
                    latest.status === 'approved' ? 'badge-success' :
                    latest.status === 'rejected' ? 'badge-danger' :
                    'badge-warning';

                info += `<p><strong>Nama:</strong> ${latest.nama_lengkap}</p>`;
                info += `<p><strong>Email:</strong> ${latest.email}</p>`;
                info += `<p><strong>No HP:</strong> ${latest.no_hp}</p>`;
                info += `<p><strong>Bank:</strong> ${latest.nama_bank} - ${latest.no_rekening}</p>`;
                info += `<p><strong>Status:</strong> <span class="badge ${statusClass}">${latest.status.charAt(0).toUpperCase() + latest.status.slice(1)}</span></p>`;

                // Gabungkan semua histories dari seluruh withdrawals
                let allHistories = [];
                withdrawals.forEach(w => {
                    if (w.histories && Array.isArray(w.histories)) {
                        allHistories = allHistories.concat(w.histories);
                    }
                });

                if (allHistories.length > 0) {
                    let rows = '';
                    allHistories.forEach((h, i) => {
                        const c = h.status === 'approved' ? 'badge-success' :
                                  h.status === 'rejected' ? 'badge-danger' : 'badge-warning';
                        rows += `<tr>
                            <td>${i + 1}</td>
                            <td>${h.program_judul ?? '-'}</td>
                            <td><span class="badge ${c}">${h.status}</span></td>
                            <td>${h.note ?? '-'}</td>
                            <td>${h.changed_at}</td>
                        </tr>`;
                    });
                    historyTable.html(rows);
                } else {
                    historyTable.html('<tr><td colspan="5" class="text-muted">Belum ada riwayat.</td></tr>');
                }

                // Tombol hanya muncul jika ada withdrawal dengan status pending
                if (pending) {
                    const approveUrl = `{{ route('programReferrals.approve', ':id') }}`.replace(':id', pending.id);
                    const rejectUrl = `{{ route('programReferrals.reject', ':id') }}`.replace(':id', pending.id);
                    approveForm.attr('action', approveUrl);
                    rejectForm.attr('action', rejectUrl);
                    approveButton.removeClass('d-none');
                    rejectButton.removeClass('d-none');
                } else {
                    approveButton.addClass('d-none');
                    rejectButton.addClass('d-none');
                }

            } else {
                info = '<p class="text-muted">Belum ada pengajuan pencairan.</p>';
                historyTable.html('<tr><td colspan="5" class="text-muted">Belum ada riwayat.</td></tr>');
                approveButton.addClass('d-none');
                rejectButton.addClass('d-none');
            }

            $('#withdrawalInfo').html(info);
            $('#referralDetailModal').modal('show');
        });
    });
</script>
@endsection
