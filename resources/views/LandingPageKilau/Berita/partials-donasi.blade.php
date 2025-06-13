<style>
    @media (max-width:1024px){
        .col-lg-4,
        .col-md-6{width:100%!important}
    }
    
    
    /* Tampilan Mobile dinamis */
    @media (max-width:768px){
        .modal-body img{
            width:100%;
            height:auto;
            object-fit:cover;
        }
        .modal-footer .btn{
            width:100%;
            padding:12px;
            font-size:16px;
        }
    }
    
    /* ───────── WARNA & UTILITAS ───────── */
    #donasiModal .modal-content{
        background:#1363c6 url({{ asset('assets/img/bg-hero.png') }}) center/cover no-repeat;
        color:#fff;
    }
    #donasiModal .modal-header{
        background:#1363c6;
        color:#fff;
    }
    #donasiModal .btn-custome{
        background:rgb(9,9,188);
        color:#fff;
    }
    #donasiModal .btn-custome:hover{
        background:rgb(9,9,97);
        color:#fff;
    }

    /* Atur modal agar bisa di-scroll */
    #donasiModal {
        overflow-y: auto !important;
        position: fixed;
    }

    /* Atur dialog agar posisinya tetap */
    #donasiModal .modal-dialog {
        max-width: 800px;
        margin: 1.75rem auto;
        position: relative; /* Ubah dari fixed ke relative */
        /*height: auto;*/
        height: 3000px !important;
    }

    /* Atur content agar bisa menampung semua konten */
    #donasiModal .modal-content {
        height: auto;
        overflow: visible;
    }

    /* Atur modal body agar bisa menampung semua konten */
    #donasiModal .modal-body {
        overflow: visible;
        padding: 20px;
    }
    
    /* Pastikan form tidak memiliki scrollbar sendiri */
    #donasiForm {
        overflow: visible !important;
    }
    
    /* Pastikan backdrop tetap pada tempatnya */
    .modal-backdrop {
        position: fixed;
    }
    
    /* Pastikan body tetap bisa di-scroll */
    body.modal-open {
        overflow: auto !important;
        padding-right: 0 !important;
    }
</style>

<!-- Modal Donasi Start -->
<div class="modal fade" id="donasiModal" tabindex="-1" aria-labelledby="donasiModalLabel" aria-hidden="true" data-bs-scroll="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light" id="donasiModalLabel">Donasi Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="donasiForm" method="POST">
                <p>Hi, apakah kamu ingin menjadi bagian dari kami untuk menebarkan manfaat?</p>
                <p><strong>⭐ Ayo Berbagi sekarang ⭐</strong></p>

                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label">
                            Nama Donatur <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nama" name="nama"
                            placeholder="Masukkan Nama Donatur" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor HP </label>
                        <input type="tel" class="form-control" id="no_hp" name="no_hp"
                               placeholder="08xxxxxxxxxx">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="nama@email.com">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Doa atau Dukungan</label>
                        <textarea class="form-control" id="feedback" name="feedback"
                                  rows="3" placeholder="Tulis pesan atau doa…"></textarea>
                    </div>

                    <!-- Jenis Donasi -->
                    <div class="mb-3">
                        <label class="form-label">Jenis Donasi <span class="text-danger">*</span></label><br>
                        <!-- Donasi Program Button -->
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-light w-100" id="donasiProgramBtn">Donasi
                                    Program</button>
                            </div>
                            <!-- Donasi Umum Button -->
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-light w-100" id="donasiUmumBtn">Donasi
                                    Umum</button>
                            </div>
                        </div>
                    </div>
                    <!-- Jenis Donasi End -->

                    <div class="mb-3" id="program-cards" style="display: none;">
                        <label for="program-cards" class="form-label text-white">Program Kami</label>
                        <div class="row row-cols-2 row-cols-md-4 g-3"> <!-- Bootstrap Grid -->
                            @foreach ($programs as $program)
                                <div class="col">
                                    <div class="card program-card h-100" data-program="{{ $program->judul }}" data-program-id="{{ $program->id }}">
                                        <img src="{{ asset('storage/' . $program->thumbnail_image) }}" class="card-img-top program-img img-fluid" alt="{{ $program->judul }}">
                                        <div class="card-body p-2 text-center">
                                            <!--<small class="text-white">{{ Str::limit($program->judul, 20) }}</small>-->
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" id="programIdInput" name="id_program" value="">

                    <!-- Informasi Deskripsi dan Statistik Program -->
                    <div class="mb-3" id="program-info" style="display: none;">
                        <h6 class="text-white" id="program-title"></h6> <!-- Added title element -->
                        <p id="program-description"></p>
                        <p id="program-statistics"></p>
                    </div>

                    <!-- Opsional Umum -->
                    <div class="mb-3" id="opsionalUmum" style="display: none;">
                        <label class="form-label">Pilih Opsional Umum</label><br>
                        <!-- Row to contain the buttons -->
                        <div class="row">
                            <!-- Button for Zakat -->
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-light w-100" name="opsional_umum"
                                    value="1" data-value="1">Zakat</button>
                            </div>
                            <!-- Button for Infaq -->
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-light w-100" name="opsional_umum"
                                    value="2" data-value="2">Infaq</button>
                            </div>
                        </div>
                    </div>

                    <!-- Input Hidden untuk opsional_umum -->
                    <input type="hidden" id="opsionalValueInput" name="opsional_umum" value="">

                    <!-- Pilihan Jumlah Donasi -->
                    <div class="mb-3">
                        <label for="donasi" class="form-label">Pilih Jumlah Donasi <span class="text-danger">*</span></label>
                        <div class="row">
                            @foreach ([10000, 25000, 50000, 75000, 100000] as $amount)
                                <div class="col-4 mt-2">
                                    <button type="button" class="btn btn-outline-light donasi-btn"
                                        data-amount="{{ $amount }}" style="width: 100%;">Rp
                                        {{ number_format($amount, 0, ',', '.') }}</button>
                                </div>
                            @endforeach
                            <div class="col-4 mt-2">
                                <button type="button" class="btn btn-outline-light" id="donasiCustom"
                                    style="width: 100%;">Isi Sendiri</button>
                            </div>
                        </div>
                    </div>

                    <!-- Input Custom Donasi -->
                    <div class="mb-3" id="customDonasi" style="display: none;">
                        <label for="customAmount" class="form-label">Masukkan Nominal Donasi</label>
                        <input type="number" class="form-control" id="customAmount" name="customAmount"
                            placeholder="Masukkan jumlah donasi">
                    </div>

                    <!-- Total Donasi -->
                    <div class="mb-3">
                        <label for="total" class="form-label">Total Donasi</label>
                        <input type="text" class="form-control" id="total" name="total" readonly>
                    </div>

                    <button type="submit" class="btn btn-custome w-100">Berbagi Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Donasi End -->

@push('scripts')
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="Mid-client-Cuh29wUmwqZAF-6t"></script>
<script>

    $(document).ready(function() {
        // Menampilkan modal donasi saat halaman dimuat
        // var donasiModal = new bootstrap.Modal(document.getElementById('donasiModal'));
        // donasiModal.show();

        var programId = null; // Variable untuk id program
        var opsionalValue = null; // Variable untuk opsional (jika ada)

        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

        // Mengklik tombol donasi untuk memilih jumlah
        $('.donasi-btn').on('click', function() {
            var amount = $(this).data('amount');
            $('#total').val(formatRupiah(amount));
            $('#donasiForm').data('amount', amount);
        });

        // Menampilkan form custom donasi saat tombol "Isi Sendiri" diklik
        $('#donasiCustom').on('click', function() {
            $('#customDonasi').show();
        });

        // Memperbarui total donasi jika input customAmount diubah
        $('#customAmount').on('input', function() {
            var amount = $(this).val();
            $('#total').val(formatRupiah(amount));
            $('#donasiForm').data('amount', amount); // Menyimpan jumlah custom
        });

        // Fungsi untuk menangani perubahan jenis donasi
        $('#donasiProgramBtn').on('click', function() {
            $('#program-cards').show();
            $('#opsionalUmum').hide();
        });

        $('#donasiUmumBtn').on('click', function() {
            $('#program-cards').hide();
            $('#opsionalUmum').show();
        });

        /* Perubahan Active Button */
        $('#donasiProgramBtn, #donasiUmumBtn').on('click', function() {
            $('#donasiProgramBtn, #donasiUmumBtn').removeClass('selected-btn');
            $(this).addClass('selected-btn');
        });

        $('[name="opsional_umum"]').on('click', function() {
            $('[name="opsional_umum"]').removeClass('selected-btn');
            $(this).addClass('selected-btn');
            var opsionalValue = $(this).data('value');
            $('#opsionalValueInput').val(opsionalValue);
        });

        // Menangani klik pada card program dan mengambil informasi program
        $('#program-cards .program-card').on('click', function() {
            // Ambil nilai program judul dan id
            var programId = $(this).data('program-id'); // ID program
            var programJudul = $(this).data('program'); // Judul program

            // Set ID program ke dalam input hidden untuk dikirim ke server
            $('#programIdInput').val(programId);

            // Menampilkan informasi program terkait
            fetchProgramInfo(programJudul); // Mengirim judul program untuk mencari detail
        });

        // Fungsi untuk mengambil informasi program dari server
        function fetchProgramInfo(programJudul) {
            $.ajax({
                url: '/get-program-info', // Ganti dengan URL yang sesuai untuk mengambil info program
                method: 'GET',
                data: {
                    program: programJudul // Mengirimkan judul program
                },
                success: function(response) {
                    if (response.success_percentage) {
                        $('#program-title').text('Deskripsi Program ' + programJudul);
                        // $('#program-description').text(response.description);
                        $('#program-description').text($('<div>').html(response.description).text());
                        $('#program-statistics').text(response.success_percentage +
                            ' orang telah terdampak dari target ' + response.target +
                            ' penerima manfaat');
                        $('#program-info').show();
                    } else {
                        $('#program-title').text('Program tidak ditemukan');
                        $('#program-description').text('');
                        $('#program-statistics').text('');
                        $('#program-info').hide();
                    }
                },
                error: function() {
                    alert('Error fetching program data.');
                }
            });
        }

        // Menangani pengiriman form donasi
        $('#donasiForm').on('submit', function (e) {
            e.preventDefault(); // Mencegah form agar tidak submit secara normal

            var amount = $('#donasiForm').data('amount');
            var programId = $('#programIdInput').val();
            var opsionalValue = $('#opsionalValueInput').val();
            var nama = $('#nama').val();

            if (!amount || amount <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nominal tidak valid',
                    text: 'Silakan pilih atau masukkan jumlah donasi.',
                });
                return;
            }

            $.ajax({
                url: '{{ route('donasi.store') }}',
                type: 'POST',
                data: {
                    nama: nama,
                    type_donasi: programId ? 1 : 2,
                    total: amount,
                    id_program: programId,
                    opsional_umum: opsionalValue,

                    no_hp   : $('#no_hp').val(),
                    email   : $('#email').val(),
                    feedback: $('#feedback').val(),
                    
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    var donasiId = response.donasi_id;

                    Swal.fire({
                        icon: 'success',
                        title: 'Donasi Berhasil Disimpan',
                        text: 'Silakan lanjutkan pembayaran.',
                        confirmButtonText: 'Lanjutkan',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Kirim ke API Midtrans eksternal
                            $.ajax({
                                url: 'https://kilauindonesia.org/api/get_token_midtrans_sb',
                                type: 'POST',
                                data: {
                                    order_id: donasiId, 
                                    // order_id: 'donasi-' + donasiId, 
                                    total: amount
                                },
                                success: function (res) {
                                    if (res.token) {
                                        snap.pay(res.token, {
                                            onSuccess: function (result) {
                                                Swal.fire(
                                                    'Terima kasih! 🤲',
                                                    'Donasi Anda berhasil. Semoga berkah! 😊',
                                                    'success'
                                                );

                                                // Pastikan donasiModal sudah terdefinisi
                                                var donasiModal = bootstrap.Modal.getInstance(document.getElementById('donasiModal'));
                                                if (!donasiModal) {
                                                    donasiModal = new bootstrap.Modal(document.getElementById('donasiModal'));
                                                }
                                                donasiModal.hide();
                                                
                                                // lanjut update status
                                                fetch('/donasi/' + donasiId + '/update-status', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    },
                                                    body: JSON.stringify({
                                                        status: 2
                                                    })
                                                })
                                                .then(res => res.json())
                                                .then(data => {
                                                    console.log('Status updated:', data);
                                                })
                                                .catch(err => console.error('Gagal update status', err));
                                            },
                                            onPending: function () {
                                                Swal.fire({
                                                    icon: 'info',
                                                    title: 'Pembayaran Sedang Diproses',
                                                    text: 'Mohon tunggu hingga pembayaran selesai.',
                                                });
                                            },
                                            onError: function () {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Pembayaran Gagal',
                                                    text: 'Terjadi kesalahan saat proses pembayaran.',
                                                });
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal Mendapatkan Token',
                                            text: 'Midtrans gagal mengirimkan token pembayaran.'
                                        });
                                    }
                                },
                                error: function () {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal Terhubung ke Midtrans',
                                        text: 'Pastikan koneksi Anda stabil.'
                                    });
                                }
                            });
                        }
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan Donasi',
                        text: 'Silakan coba lagi.'
                    });
                }
            });
        });
    });
</script>
@endpush