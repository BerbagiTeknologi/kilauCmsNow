<style>
    .modal-dialog {
        max-width: 800px;
        max-height: none;
        overflow-y: hidden;
    }

    .modal-body {
        /* max-height: 75vh; */
        overflow-y: auto;
    }

    #ourProgramDonasiModal .modal-content {
        background-color: #1363c6;
        color: white;
        background-image: url({{ asset('assets/img/bg-hero.png') }});
        background-size: cover;
        background-position: center;
    }

    /* Modal Header Styling */
    #ourProgramDonasiModal .modal-header {
        background-color: #1363c6;
        color: white;
    }

    /* Styling untuk tombol donasi */
    #ourProgramDonasiModal .btn-custome {
        background-color: rgb(9, 9, 188);
        color: white;
    }

    #ourProgramDonasiModal .btn-custome:hover {
        background-color: rgb(9, 9, 97);
        color: white;
    }

    /* Styling untuk card program */
    #program-cards .program-card {
        position: relative;
        cursor: pointer;
        height: 180px;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Image styling */
    /*  #program-cards .program-card img {
        object-fit: cover;
        height: 100%;
        width: 100%;
        transition: all 0.3s ease;
        opacity: 0.7;
    } */

    #program-cards .program-card:hover img {
        opacity: 1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
    }

    /* Card title styling */
    #program-cards .program-card .card-body {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    /* Show title on hover */
    #program-cards .program-card:hover .card-body {
        opacity: 1;
    }

    /* Title styling */
    #program-cards .program-card .card-body h5 {
        margin: 0;
        font-size: 18px;
        text-shadow: 1px 1px 3px white;
    }

    /* Hide description text */
    #program-cards .program-card .card-body p {
        display: none;
    }

    .selected-btn {
        background-color: white !important;
        color: black !important;
    }
</style>


<div class="modal fade" id="ourProgramDonasiModal" tabindex="-1" aria-labelledby="ourProgramDonasiModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light" id="ourProgramDonasiModalLabel">Donasi Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Hi, apakah kamu ingin menjadi bagian dari kami untuk menebarkan manfaat?</p>
                <p><strong>⭐ Ayo Berbagi sekarang ⭐</strong></p>

                <form id="ourProgramDonasiForm" method="POST" action="{{ route('donasi.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="ourProgramNama" class="form-label">Nama Donatur  <span class="text-danger">*</span> </label>
                        <input type="text" class="form-control" id="ourProgramNama" name="nama"
                            placeholder="Masukkan Nama Donatur" required>
                    </div>

                    <div class="mb-3">
                        <label for="ourProgramNomorHp" class="form-label">Nomor HP </label>
                        <input type="tel" class="form-control" id="ourProgramNomorHp" name="no_hp"
                               placeholder="08xxxxxxxxxx">
                    </div>
                    
                    <div class="mb-3">
                        <label for="ourProgramEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="ourProgramEmail" name="email"
                               placeholder="nama@email.com">
                    </div>
                    
                    <div class="mb-3">
                        <label for="ourProgramFeedback" class="form-label">Doa atau Dukungan</label>
                        <textarea class="form-control" id="ourProgramFeedback" name="feedback"
                                  rows="3" placeholder="Tulis pesan atau doa…"></textarea>
                    </div>

                    <!-- Input Hidden Program ID -->
                    <input type="hidden" id="typeDonasiInput" name="type_donasi" value="1">
                    <input type="hidden" id="ourProgramIdInput" name="id_program" value="">

                    <!-- Informasi Program -->
                    {{-- <div class="mb-3">
                        <label class="form-label text-white">Program yang Dipilih</label>
                        <div class="card program-card text-center">
                            <div class="card-body">
                                <h5 id="ourSelectedProgramTitle"></h5>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Informasi Program -->
                   {{--  <div class="mb-3">
                        <label class="form-label text-white">Program yang Dipilih</label>
                        <div class="card program-card text-center">
                            <img id="ourSelectedProgramImage" class="card-img-top" src=""
                                alt="Program Thumbnail"
                                style="max-height: 300px; object-fit: cover; display: none; margin: auto;">
                        </div>
                    </div> --}}

            

                    <div class="mb-3">
                        <label class="form-label text-white">Program yang Dipilih</label>
                        <div class="card program-card text-center" style="border: none; overflow: hidden; border-radius: 0.8rem; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); background-color: #fff; width: 100%;">
                            <img id="ourSelectedProgramImage" class="card-img-top" src=""
                                alt="Program Thumbnail"
                                style="display: none; width: 100%; height: 100%; object-fit: cover; object-position: center;">
                        </div>
                    </div>

                    <!-- Pilihan Jumlah Donasi -->
                    <div class="mb-3">
                        <label class="form-label">Pilih Jumlah Donasi  <span class="text-danger">*</span></label>
                        <div class="row">
                            @foreach ([10000, 25000, 50000, 75000, 100000] as $amount)
                                <div class="col-4 mt-2">
                                    <button type="button" class="btn btn-outline-light our-donasi-btn"
                                        data-amount="{{ $amount }}" style="width: 100%;">Rp
                                        {{ number_format($amount, 0, ',', '.') }}</button>
                                </div>
                            @endforeach
                            <div class="col-4 mt-2">
                                <button type="button" class="btn btn-outline-light" id="ourProgramDonasiCustom"
                                    style="width: 100%;">Isi Sendiri</button>
                            </div>
                        </div>
                    </div>

                    <!-- Input Custom Donasi -->
                    <div class="mb-3" id="ourProgramCustomDonasi" style="display: none;">
                        <label class="form-label">Masukkan Nominal Donasi</label>
                        <input type="number" class="form-control" id="ourProgramCustomAmount" name="total"
                            placeholder="Masukkan jumlah donasi">
                    </div>

                    <!-- Total Donasi -->
                    <div class="mb-3">
                        <label class="form-label">Total Donasi</label>
                        <input type="text" class="form-control" id="ourProgramTotal" readonly>
                        <!-- Hanya tampilan -->
                    </div>

                    <button type="submit" class="btn btn-custome w-100">Berbagi Sekarang</button>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

    /* ------- konstanta -------- */
    const TRACK_URL = '{{ route('track.donasi.modalprogram') }}';
    const csrf     = document.querySelector('meta[name="csrf-token"]').content;

    /* ------- ambil elemen modal -------- */
    const modalEl = document.getElementById('ourProgramDonasiModal');

    if (modalEl) {
        /* event Bootstrap “shown.bs.modal” */
        modalEl.addEventListener('shown.bs.modal', () => {

            /* kirim POST menggunakan fetch */
            fetch(TRACK_URL, {
                method : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf          // token wajib untuk Laravel
                },
                body   : JSON.stringify({})       // tak perlu data tambahan
            })
            .catch(err => console.error('Log donasi program gagal:', err));
        });
    }
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

        // Modal Donasi
        const donasiModal = new bootstrap.Modal(document.getElementById('ourProgramDonasiModal'));
        const programIdInput = document.getElementById('ourProgramIdInput');
        const programTitleElement = document.getElementById('ourSelectedProgramTitle');
        const totalDisplay = document.getElementById('ourProgramTotal'); // Tampilan format Rupiah
        const customAmountInput = document.getElementById('ourProgramCustomAmount'); 
        const typeDonasiInput = document.getElementById('typeDonasiInput');

        // **Tombol Donasi**
        document.querySelectorAll('.btn-donasi').forEach(button => {
            button.addEventListener('click', function() {
                const programId = this.getAttribute('data-program-id');
                const programTitle = this.getAttribute('data-program-title');
                const programImage = this.getAttribute('data-program-image');

                programIdInput.value = programId;
                programTitleElement.innerText = programTitle;
                typeDonasiInput.value = "1"; // Set default ke donasi program

                if (programImage) {
                    programImageElement.src = programImage;
                    programImageElement.style.display = "block"; // Tampilkan gambar
                } else {
                    programImageElement.style.display =
                        "none"; // Sembunyikan gambar jika tidak ada
                }

                // Reset nilai saat modal dibuka
                totalDisplay.value = '';
                customAmountInput.value = '';
                donasiModal.show();
            });
        });

        // **Tombol Pilihan Jumlah Donasi**
        document.querySelectorAll('.our-donasi-btn').forEach(button => {
            button.addEventListener('click', function() {
                let amount = this.getAttribute('data-amount');

                totalDisplay.value = formatRupiah(amount); // Format tampilan
                customAmountInput.value = amount; // Simpan nilai angka untuk dikirim ke backend

                document.getElementById('ourProgramCustomDonasi').style.display =
                    'none'; // Sembunyikan input custom
            });
        });

        // **Tombol Donasi Custom**
        document.getElementById('ourProgramDonasiCustom').addEventListener('click', function() {
            document.getElementById('ourProgramCustomDonasi').style.display = 'block';
            customAmountInput.value = ''; // Reset nilai sebelumnya
            totalDisplay.value = ''; // Reset tampilan
            customAmountInput.focus();
        });

        // **Input Custom Amount**
        customAmountInput.addEventListener('input', function() {
            let amount = this.value;
            totalDisplay.value = formatRupiah(amount); // Tampilkan dalam format Rupiah
        });

        // **Submit Form Donasi**
        document.getElementById('ourProgramDonasiForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let amount = customAmountInput.value;
            if (!amount || amount <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nominal Donasi Tidak Valid',
                    text: 'Silakan masukkan jumlah donasi yang valid.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const formData = new FormData(this);
            formData.set('total', amount); // Pastikan hanya 1 nilai total dikirim
            console.log("Data dikirim: ", Object.fromEntries(formData)); // Debugging

            fetch(this.getAttribute('action'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Respon dari Backend: ", data); // Debugging
                    if (data.donasi_id) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Donasi Berhasil!',
                            text: 'Sedang memproses pembayaran...',
                            confirmButtonText: 'OK'
                        });

                        let donasiId = data.donasi_id;

                        // **Mendapatkan Token Midtrans untuk Pembayaran**
                        fetch('https://kilauindonesia.org/api/get_token_midtrans_sb', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    total: amount
                                }) // Kirim total ke Midtrans
                            })
                            .then(response => response.json())
                            .then(midtransData => {
                                console.log("Respon Midtrans: ", midtransData); // Debugging

                                if (midtransData.token) {
                                    snap.pay(midtransData.token, {
                                        onSuccess: function(result) {
                                            Swal.fire(
                                                'Terima kasih! 🤲',
                                                'Terima kasih sudah berbagi donasi. Semoga berkah dan bermanfaat! 😊',
                                                'success'
                                            );
                                            donasiModal.hide();

                                            // **Update Status Donasi Setelah Pembayaran Berhasil**
                                            fetch(`/donasi/${donasiId}/update-status`, {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document
                                                            .querySelector(
                                                                'input[name="_token"]'
                                                            ).value
                                                    },
                                                    body: JSON.stringify({
                                                        donasi_id: donasiId,
                                                        status: 2 // Status Aktif
                                                    })
                                                })
                                                .then(() => {
                                                    console.log(
                                                        'Status donasi berhasil diupdate'
                                                    );
                                                    window.location
                                                        .reload(); // **REFRESH OTOMATIS**
                                                })
                                                .catch(() => console.log(
                                                    'Gagal update status donasi'
                                                ));
                                        },
                                        onError: function() {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Pembayaran Gagal',
                                                text: 'Terjadi kesalahan saat pembayaran.',
                                                confirmButtonText: 'Coba Lagi',
                                                confirmButtonColor: '#d33',
                                            });
                                        },
                                        onPending: function() {
                                            Swal.fire({
                                                icon: 'info',
                                                title: 'Pembayaran Sedang Diproses',
                                                text: 'Harap tunggu hingga pembayaran selesai.',
                                                confirmButtonText: 'Tutup',
                                            });
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal Mendapatkan Token Midtrans',
                                        text: 'Terjadi kesalahan saat mendapatkan token untuk pembayaran.',
                                        confirmButtonText: 'Coba Lagi',
                                        confirmButtonColor: '#d33',
                                    });
                                }
                            })
                            .catch(() => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: 'Gagal mendapatkan token Midtrans.',
                                    confirmButtonText: 'Coba Lagi',
                                    confirmButtonColor: '#d33',
                                });
                            });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menyimpan donasi.',
                            confirmButtonText: 'Coba Lagi'
                        });
                    }
                })
                .catch(error => {
                    console.error("Error Fetch: ", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan dalam mengirimkan data.',
                        confirmButtonText: 'Coba Lagi'
                    });
                });
        });
    });
</script>
