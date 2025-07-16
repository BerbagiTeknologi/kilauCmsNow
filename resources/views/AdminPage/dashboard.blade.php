@extends('AdminPage.App.master')

@section('style')
    <style>
        .card-stats {
            background-color: #1572E8;
            color: white;
            border-radius: 10px;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 15px;
        }

        .card-stats .icon-big {
            color: white;
            font-size: 50px;
        }

        .card-stats .numbers h4,
        .card-stats .numbers p {
            color: white;
            margin: 0;
        }

        .card-stats .numbers h4 {
            font-size: 30px;
        }

        .card-stats .more-info {
            margin-top: auto;
            text-align: right;
        }

        .card-stats .more-info a {
            color: white;
            font-weight: bold;
            text-decoration: none;
        }

        .card-stats .more-info a:hover {
            text-decoration: underline;
        }

        .col-sm-6.col-md-3 {
            padding-bottom: 10px;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .filter-section select,
        .filter-section button {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                <h3 class="fw-bold mb-3">DASHBOARD CMS KILAU INDONESIA</h3>
            </div>
            <div class="row">

                <!-- Testimoni -->
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center">
                                    <i class="fas fa-comments"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <h4>{{ $totalTestimoni }}</h4>
                                    <p>Testimoni</p>
                                </div>
                            </div>
                        </div>
                        <div class="more-info">
                            <a href="{{ route('testimoni') }}">More Info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Berita -->
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <h4>{{ is_numeric($totalBerita) ? $totalBerita : 'N/A' }}</h4>
                                    <p>Berita</p>
                                </div>
                            </div>
                        </div>
                        <div class="more-info">
                            <a href="{{ route('berita') }}">More Info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Donatur -->
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <h4>{{ $totalMitraDonatur }}</h4>
                                    <p>Mitra Donatur</p>
                                </div>
                            </div>
                        </div>
                        <div class="more-info">
                            <a href="{{ route('mitra') }}">More Info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Kantor Cabang -->
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center">
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <h4>{{ $totalKantorCabang }}</h4>
                                    <p>Kantor Cabang</p>
                                </div>
                            </div>
                        </div>
                        <div class="more-info">
                            <a href="{{ route('kontak') }}">More Info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <!--<div class="row">-->
                    <!-- Grafik Donasi -->
                <!--    <div class="col-md-6 mt-2">-->
                <!--        <h5 class="fw-bold">Grafik Donasi</h5>-->
                <!--        <canvas id="donasiChart" width="400" height="200"></canvas>-->
                <!--    </div>-->

                    <!-- Grafik Kunjungan Landing Page -->
                <!--    <div class="col-md-6 mt-2">-->
                <!--        <h5 class="fw-bold">Grafik Kunjungan Website Kilau</h5>-->
                <!--        <canvas id="landingChart" width="400" height="200"></canvas>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!--<div class="row">-->
                <!--    {{-- Grafik Donasi (tanpa filter) --}}-->
                <!--    <div class="col-md-6 mt-2">-->
                <!--        <h5 class="fw-bold">Grafik Donasi</h5>-->
                <!--        <canvas id="donasiChart" height="200"></canvas>-->
                <!--    </div>-->
                
                <!--    {{-- Grafik Kunjungan --}}-->
                <!--    <div class="col-md-6 mt-2">-->
                <!--        <h5 class="fw-bold">Grafik Kunjungan Website Kilau</h5>-->
                
                <!--        {{-- Tombol filter di bawah judul --}}-->
                <!--        <div id="trafficFilter" class="btn-group btn-group-sm mb-2" role="group">-->
                <!--            <button class="btn btn-outline-primary active" data-group="daily">Harian</button>-->
                <!--            <button class="btn btn-outline-primary"           data-group="monthly">Bulanan</button>-->
                <!--            <button class="btn btn-outline-primary"           data-group="yearly">Tahunan</button>-->
                <!--        </div>-->
                
                <!--        <canvas id="landingChart" height="200"></canvas>-->
                <!--    </div>-->
                <!--</div>-->
                
                <div class="row">

                    {{-- ===== GRAFIK DONASI ===== --}}
                    <div class="col-md-6 mt-2">
                        <h5 class="fw-bold">Grafik Donasi</h5>
                        <div id="donasiFilter" class="btn-group btn-group-sm mb-2" role="group">
                            <button class="btn btn-outline-primary active" data-group="daily">Harian</button>
                            <button class="btn btn-outline-primary"          data-group="monthly">Bulanan</button>
                            <button class="btn btn-outline-primary"          data-group="yearly">Tahunan</button>
                        </div>
                        <canvas id="donasiChart" height="200"></canvas>
                    </div>
                
                    {{-- ===== GRAFIK KUNJUNGAN ===== --}}
                    <div class="col-md-6 mt-2">
                        <h5 class="fw-bold">Grafik Kunjungan Website Kilau</h5>
                        <div id="trafficFilter" class="btn-group btn-group-sm mb-2" role="group">
                            <button class="btn btn-outline-primary active" data-group="daily">Harian</button>
                            <button class="btn btn-outline-primary"          data-group="monthly">Bulanan</button>
                            <button class="btn btn-outline-primary"          data-group="yearly">Tahunan</button>
                        </div>
                        <canvas id="landingChart" height="200"></canvas>
                    </div>
                
                </div>

               <div class="col-12 mt-5">
                    <div class="table-responsive">
                        <h6 class="fw-bold mb-3">Rekap & Log Kunjungan (Semua Tipe)</h6>
                        
                        <form method="GET" class="row g-2 align-items-end mb-3">
                            {{-- pertahankan query lainnya --}}
                            @foreach(request()->except('date') as $k => $v)
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endforeach
                
                            <!--<div class="col-auto">-->
                            <!--    <label for="dateFilter" class="form-label mb-0">Filter tanggal</label>-->
                            <!--    <input type="date"-->
                            <!--           id="dateFilter"-->
                            <!--           name="date"-->
                            <!--           value="{{ request('date') }}"-->
                            <!--           class="form-control"-->
                            <!--           max="{{ now()->toDateString() }}">-->
                            <!--</div>-->
                
                            <!--<div class="col-auto">-->
                            <!--    <button class="btn btn-primary">-->
                            <!--        <i class="fas fa-filter me-1"></i> Tampilkan-->
                            <!--    </button>-->
                
                            <!--    @if(request()->filled('date'))-->
                            <!--        <a href="{{ route('dashboard', request()->except('date')) }}"-->
                            <!--           class="btn btn-outline-secondary ms-1">-->
                            <!--            Reset-->
                            <!--        </a>-->
                            <!--    @endif-->
                            <!--</div>-->
                        </form>
                
                        {{-- info tanggal ter-filter --}}
                        @if(request()->filled('date'))
                            <p class="small text-muted">
                                Log untuk tanggal
                                <strong>{{ \Carbon\Carbon::parse(request('date'))->translatedFormat('d F Y') }}</strong>
                            </p>
                        @endif
                
                        {{-- ===================== BADGE TOTAL PER TIPE ===================== --}}
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <div class="badge bg-primary w-100 px-3 py-3 text-center fs-5">
                                    Landing Page: {{ $totalLandingPage }}
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="badge bg-success w-100 px-3 py-3 text-center fs-5">
                                    Form Donasi Umum: {{ $totalFormDonasi }}
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="badge bg-warning w-100 px-3 py-3 text-center fs-5">
                                    Form Donasi Program: {{ $totalFormDonasiProgram }}
                                </div>
                            </div>
                        </div>

                
                        <table class="table table-bordered table-hover align-middle table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:60px">#</th>
                                    <th>Bulan</th>
                                    <th>Jumlah Kunjungan</th>
                                    <th>Tipe</th>
                                    <th>Session ID</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                    <th>Viewed At</th>
                                </tr>
                            </thead>
                            <tbody id="log-kunjungan-body">
                                {{-- Rekap per bulan --}}
                                @foreach ($bulanKunjungan as $i => $bln)
                                    <tr class="table-primary fw-semibold">
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::createFromDate(null, $bln, 1)->translatedFormat('F') }}</td>
                                        <td>{{ $totalKunjungan[$i] }}</td>
                                        <td colspan="5" class="text-center">— Rekap Bulanan —</td>
                                    </tr>
                                @endforeach
                
                                {{-- Log kunjungan detail --}}
                                @php $no = ($landingLogs->currentPage() - 1) * $landingLogs->perPage() + 1; @endphp
                                @foreach ($landingLogs as $log)
                                    <tr class="log-row">
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $log->viewed_at->translatedFormat('F') }}</td>
                                        <td>1</td>
                                        <td>
                                            @switch($log->type)
                                                @case('landingpage')
                                                    <span class="badge bg-primary">Landing</span>
                                                    @break
                                                @case('form_donasi')
                                                    <span class="badge bg-success">Form Donasi</span>
                                                    @break
                                                @case('form_donasi_program')
                                                    <span class="badge bg-warning text-dark">Donasi Program</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">Lainnya</span>
                                            @endswitch
                                        </td>
                                        <td class="text-break">{{ $log->session_id }}</td>
                                        <td>{{ $log->ip_address }}</td>
                                        <td class="text-break" style="max-width:320px">{{ $log->user_agent }}</td>
                                        <td>{{ $log->viewed_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $landingLogs->links() }}
                        </div>
                    </div>
                </div>


                <h5 class="fw-bold">Filter Data Donasi</h5>
                <form id="filter-form">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="month" id="monthFilter" class="form-control">
                                <option value="">Select Month</option>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}">
                                        {{ \Carbon\Carbon::createFromFormat('m', $month)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="status" id="statusFilter" class="form-control">
                                <option value="">Select Status</option>
                                <option value="1">Pending</option>
                                <option value="2">Berdonasi</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-12" id="donasi-table-container">
                <div class="table-responsive">
                    <table id="donasi-table" class="table table-striped align-middle" style="min-width: 1400px;">
                        <thead>
                            <tr>
                                <th style="min-width: 140px;">Nama Donatur</th>
                                <th style="min-width: 100px;">Jenis Donasi</th>
                                <th style="min-width: 200px;">Donasi</th>
                                <th style="min-width: 140px;">Total Donasi</th>
                                <th style="min-width: 120px;">Email</th>
                                <th style="min-width: 120px;">No. HP</th>
                                <th style="min-width: 150px;">Feedback</th>
                                <th style="min-width: 140px;">Tanggal Donasi</th>
                                <th style="min-width: 120px;">Status Donasi</th>
                                <th style="min-width: 30px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($donasi as $data)
                                <tr>
                                    <td class="text-nowrap">{{ $data->nama }}</td>
                                    <td>{{ $data->type_donasi == 1 ? 'Program' : 'Umum' }}</td>
                                    <td class="text-break">
                                        {{ $data->type_donasi == 1
                                            ? ($data->program
                                                ? $data->program->judul
                                                : 'Program Tanpa Judul')
                                            : ($data->opsional_umum == 1
                                                ? 'Zakat'
                                                : 'Infaq') }}
                                    </td>
                                    <td>{{ number_format($data->total_donasi, 2) }}</td>
                                    <td class="text-break">{{ $data->email ?? '-' }}</td>
                                    <!--<td class="text-break">{{ $data->no_hp ?? '-' }}</td>-->
                                     <td class="text-break">
                                        @if ($data->no_hp)
                                            @php
                                                // hapus semua karakter non-digit
                                                $hp = preg_replace('/\D+/', '', $data->no_hp);
                            
                                                // 08xxx  → 628xxx
                                                if (str_starts_with($hp, '0')) {
                                                    $hp = '62' . substr($hp, 1);
                                                }
                                                // 8xxx (tanpa 0) → 628xxx
                                                elseif (str_starts_with($hp, '8')) {
                                                    $hp = '62' . $hp;
                                                }
                                            @endphp
                                            <a href="https://wa.me/{{ $hp }}" target="_blank"
                                               class="text-success text-decoration-none">
                                                {{ $data->no_hp }}
                                                <i class="fab fa-whatsapp ms-1"></i>
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-break">{{ $data->feedback ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $data->formatted_date }}</td>
                                    <td>
                                        @if ($data->status_donasi == 1)
                                            <button class="btn btn-warning w-100">Pending</button>
                                        @else
                                            <button class="btn btn-success w-100">Berdonasi</button>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-link text-danger p-0 btnHapusDonasi"
                                            data-id="{{ $data->id }}" style="font-size: 1.2rem;"
                                            title="Hapus Donasi">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Konfirmasi Hapus -->
            <div class="modal fade" id="modalHapusDonasi" tabindex="-1" aria-labelledby="modalHapusLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                        <!-- Header -->
                        <div class="modal-header bg-gradient bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="modalHapusLabel">
                                <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Donasi
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body py-4 text-center">
                            <p class="fs-5 mb-0 text-danger">
                                Anda yakin ingin menghapus data donasi ini?<br>
                                <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer d-flex justify-content-between px-4 pb-4">
                            <form id="hapusDonasiForm" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
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
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
       
    <!--const bulanKunjungan = @json($bulanKunjungan); -->
    <!--const totalKunjungan = @json($totalKunjungan); -->
   <!--<script>-->
      
    
   <!--     const ctxLanding = document.getElementById('landingChart').getContext('2d');-->
   <!--     const landingChart = new Chart(ctxLanding, {-->
   <!--         type: 'line',-->
   <!--         data: {-->
   <!--             labels: bulanKunjungan.map(b => new Date(2023, b - 1).toLocaleString('en-US', {-->
   <!--                 month: 'long'-->
   <!--             })),-->
   <!--             datasets: [{-->
   <!--                 label: 'Kilau Website Visits',-->
   <!--                 data: totalKunjungan,-->
   <!--                 borderColor: '#1363c6',-->
   <!--                 backgroundColor: 'rgba(19, 99, 198, 0.2)',-->
   <!--                 tension: 0.4-->
   <!--             }]-->
   <!--         },-->
   <!--         options: {-->
   <!--             responsive: true,-->
   <!--             scales: {-->
   <!--                 y: {-->
   <!--                     beginAtZero: true-->
   <!--                 }-->
   <!--             }-->
   <!--         }-->
   <!--     });-->
   <!-- </script>-->
   
  <script>
    function setActiveBtn(group) {
        document.querySelectorAll('#trafficFilter button').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.group === group);
        });
    }
    
    let landingChart;
    
    function renderLanding(labels = [], data = []) {
        const ctx = document.getElementById('landingChart').getContext('2d');
    
        if (landingChart) {
            landingChart.data.labels           = labels;
            landingChart.data.datasets[0].data = data;
            landingChart.update();
            return;
        }
    
        landingChart = new Chart(ctx, {
            type : 'line',
            data : {
                labels   : labels,
                datasets : [{
                    label           : 'Kunjungan Website',
                    data            : data,
                    borderColor     : '#1363c6',
                    backgroundColor : 'rgba(19,99,198,0.2)',
                    tension         : 0.4
                }]
            },
            options : {
                responsive : true,
                scales     : { y : { beginAtZero : true } }
            }
        });
    }
    
    function loadTraffic(group = 'daily') {
        setActiveBtn(group);
    
        fetch(`{{ route('dashboard.trafficData') }}?group=${group}`)
            .then(r => r.json())
            .then(rows => {
                const labels = rows.map(r => r.label);
                const totals = rows.map(r => r.total);
    
                if (group === 'monthly') {
                    labels.forEach((v, i) => {
                        const d = new Date(v + '-01');
                        labels[i] = d.toLocaleDateString('id-ID', { month : 'short', year : 'numeric' });
                    });
                }
    
                renderLanding(labels, totals);
            })
            .catch(console.error);
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        loadTraffic('daily');  
    
        document.querySelectorAll('#trafficFilter button')
                .forEach(btn => btn.addEventListener('click',
                    () => loadTraffic(btn.dataset.group)));
    });
    </script>


<script>
$(function () {

  /* =================================================================
     1)  Modal hapus & DataTable — tetap seperti semula
  ================================================================= */
  const modalHapus = new bootstrap.Modal('#modalHapusDonasi');

  $('body').on('click','.btnHapusDonasi',function(){
      $('#hapusDonasiForm').attr('action',
          `{{ url('/admin/dashboard/donasi') }}/${$(this).data('id')}`);
      modalHapus.show();
  });

  $('#donasi-table').DataTable({ order:[[5,'desc']] });

  /* =================================================================
     2)  Grafik DONASI dinamis
  ================================================================= */
  const fmtMonth = ym => new Date(ym+'-01')
        .toLocaleDateString('id-ID',{month:'short',year:'numeric'});

  let donasiChart = null;           // referensi chart

  function drawDonasi(labels,data){
      donasiChart?.destroy();       // hapus chart lama (jika ada)
      donasiChart = new Chart(
          document.getElementById('donasiChart').getContext('2d'),
          { type:'bar',
            data:{ labels,
                   datasets:[{ label:'Total Donasi',
                               data,
                               backgroundColor:'#1363c6',
                               borderColor:'#0d4b8c',
                               borderWidth:1 }]},
            options:{ responsive:true, scales:{ y:{ beginAtZero:true } } }});
  }

  function loadDonasi(group='daily'){
      // set tombol aktif
      $('#donasiFilter button').removeClass('active')
          .filter(`[data-group="${group}"]`).addClass('active');

      $.ajax({
          url : '{{ route('dashboard.donasiData') }}',
          data: { group },
          dataType: 'json',
          success: rows =>{
              if (!Array.isArray(rows)) rows = [rows];     // objek tunggal → array
              let labels = rows.map(r=>r.label);

              if (group === 'monthly') labels = labels.map(fmtMonth);
              // yearly → label spt "2025", daily → label "2025-06-17"

              drawDonasi(labels, rows.map(r=>Number(r.total)));
          },
          error: (jq,x,e)=>{
              console.error('[donasiData]', jq.status, e, jq.responseText);
              alert('Tidak bisa memuat data Donasi. Lihat console / laravel.log');
          }
      });
  }

  // ====== panggil default: HARlAN ======
  loadDonasi('daily');

  // ====== handler tombol filter rentang ======
  $('#donasiFilter button').on('click', function () {
      loadDonasi($(this).data('group'));
  });

  /* =================================================================
     3)  Form filter bulan & status — perbarui tabel + grafik
  ================================================================= */
  $('#filter-form').on('submit', function(e){
      e.preventDefault();
      $.ajax({
          url : '{{ route('dashboard.filterDonasi') }}',
          data: { month:$('#monthFilter').val(), status:$('#statusFilter').val() },
          dataType:'json',
          success: res=>{
              updateTable(res.data);
              drawDonasi(res.bulan, Object.values(res.totalDonasi));
          }
      });
  });

  /* =================================================================
     4)  updateTable & attachModalDeleteLogic  (tetap)
  ================================================================= */
  function updateTable(data){
      const tbody = $('#donasi-table tbody').empty();
      data.sort((a,b)=>new Date(b.created_at)-new Date(a.created_at));
      data.forEach(item=>{
          const d = new Date(item.created_at)
                .toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});
          const jenis  = item.type_donasi==1 ? 'Program' : 'Umum';
          const detail = item.type_donasi==1
               ? (item.program ? item.program.judul : 'Program Tanpa Judul')
               : (item.opsional_umum==1 ? 'Zakat' : 'Infaq');
          const status = item.status_donasi==1
               ? '<button class="btn btn-warning w-100">Pending</button>'
               : '<button class="btn btn-success w-100">Berdonasi</button>';
          const aksi   = `<button class="btn btn-sm btn-link text-danger p-0 btnHapusDonasi"
                            data-id="${item.id}" title="Hapus Donasi">
                            <i class="fas fa-trash-alt"></i></button>`;
          tbody.append(`<tr>
              <td class="text-nowrap">${item.nama??'-'}</td>
              <td>${jenis}</td>
              <td class="text-break">${detail}</td>
              <td>${new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR'}).format(item.total_donasi)}</td>
              <td class="text-break">${item.email??'-'}</td>
              <td class="text-break">${item.no_hp??'-'}</td>
              <td class="text-break">${item.feedback??'-'}</td>
              <td class="text-nowrap">${d}</td>
              <td class="text-nowrap">${status}</td>
              <td>${aksi}</td></tr>`);
      });
      attachModalDeleteLogic();
  }

  function attachModalDeleteLogic(){
      $('body').off('click','.btnHapusDonasi').on('click','.btnHapusDonasi',function(){
          $('#hapusDonasiForm').attr('action',
              `/admin/dashboard/donasi/${$(this).data('id')}`);
          modalHapus.show();
      });
  }
  attachModalDeleteLogic();

});
</script>
@endsection
