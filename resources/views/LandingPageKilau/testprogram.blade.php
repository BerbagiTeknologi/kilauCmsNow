 
<style>

    .program-card {
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .program-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .program-img {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        height: 180px;
        object-fit: cover;
    }

    .card-body {
        display: flex;
        flex-grow: 1;
        flex-direction: column;
        justify-content: space-between;
    }

    .progress {
        height: 8px;
        border-radius: 10px;
    }

    .progress-bar {
        background-color: #007bff;
    }

    .carousel-inner img {
        border-radius: 8px;
        object-fit: cover;
        width: 200px;
        height: 400px;
    }

    /* Progress bar color variations */
    .progress-bar-success {
        background-color: green;
    }

    .progress-bar-primary {
        background-color: #007bff;
    }

    .progress-bar-warning {
        background-color: #ffc107;
    }

    .progress-bar-danger {
        background-color: #007bff;
    }

</style>

<!-- Programs Section Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <!-- Judul Section -->
        <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 500px">
            <h1 class="mb-4">{{ $programMenu->judul }}</h1>
            <p class="lead">{{ $programMenu->subjudul }}</p>
        </div>

        <!-- Cards Program -->
        <div class="row g-4 py-5 flex-nowrap overflow-auto d-flex" style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;">
            @foreach ($programs as $program)
                <div class="col-lg-3 col-md-6">
                    <div class="card program-card">
                        <img src="{{ asset('storage/' . $program->thumbnail_image) }}" class="card-img-top program-img"
                            alt="{{ $program->judul }}">

                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title">{{ $program->judul }}</h5>
                            <p class="card-text text-muted">
                                {{ \Illuminate\Support\Str::limit(str_replace('&nbsp;', ' ', html_entity_decode(strip_tags($program->deskripsi))), 30, '...') }}
                            </p>


                            <label for="combined-target">Pencapaian Program</label>
                            <div class="progress mt-2">
                                <div class="progress-bar progress-bar-primary" role="progressbar"
                                    style="width: {{ $program->jumlah_target_tercapai > 0 ? ($program->program_yang_berhasil_dijalankan / $program->jumlah_target_tercapai) * 100 : 0 }}%;"
                                    aria-valuenow="{{ $program->program_yang_berhasil_dijalankan }}" aria-valuemin="0"
                                    aria-valuemax="{{ $program->jumlah_target_tercapai }}">
                                </div>

                                <div class="progress-bar progress-bar-warning" role="progressbar"
                                    style="width: {{ $program->jumlah_target_tercapai > 0 ? (($program->jumlah_target_tercapai - $program->program_yang_berhasil_dijalankan) / $program->jumlah_target_tercapai) * 100 : 0 }}%;"
                                    aria-valuenow="{{ $program->jumlah_target_tercapai - $program->program_yang_berhasil_dijalankan }}"
                                    aria-valuemin="0" aria-valuemax="{{ $program->jumlah_target_tercapai }}">
                                </div>
                            </div>

                            <p class="small mt-2">{{ $program->program_yang_berhasil_dijalankan }} orang telah
                                terdampak dari target {{ $program->jumlah_target_tercapai }} penerima manfaat</p>

                            <!-- Row Flexbox untuk Tombol -->
                            <div class="d-flex align-items-center mt-3">
                                <!-- Tombol Lihat Detail -->
                                <button class="btn btn-primary open-modal me-2"
                                    data-id="program-modal{{ $program->id }}"
                                    style="flex: 8; min-height: 45px; display: flex; align-items: center; justify-content: center;">
                                    Lihat Detail
                                </button>

                                <!-- Tombol Donasi -->
                                {{--  <button type="button"
                                    class="btn btn-outline-primary btn-donasi d-flex justify-content-center align-items-center"
                                    data-program-id="{{ $program->id }}" data-program-title="{{ $program->judul }}"
                                    style="flex: 2; min-height: 45px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-donate"></i>
                                </button> --}}

                                <button type="button"
                                    class="btn btn-outline-primary btn-donasi d-flex justify-content-center align-items-center"
                                    data-program-id="{{ $program->id }}"
                                    data-program-image="{{ asset('storage/' . $program->thumbnail_image) }}"
                                    style="flex: 2; min-height: 45px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-donate"></i>
                                </button>

                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Programs Section End -->

<!-- Modal Program -->
@foreach ($programs as $program)
    <div class="modal fade" id="program-modal{{ $program->id }}" tabindex="-1"
        aria-labelledby="modalLabel{{ $program->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel{{ $program->id }}">{{ $program->judul }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                   <div class="modal-body" style=" padding-bottom: 145px;">
                    <!-- Carousel Gambar Dokumentasi -->
                    <div id="carousel{{ $program->id }}" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach ($program->foto_image as $image)
                                <div class="carousel-item @if ($loop->first) active @endif">
                                    <img src="{{ asset('storage/' . $image) }}" class="d-block w-100"
                                        alt="Dokumentasi">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button"
                            data-bs-target="#carousel{{ $program->id }}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button"
                            data-bs-target="#carousel{{ $program->id }}" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>

                <p class="mt-3">{!! html_entity_decode($program->deskripsi) !!}</p>



                    <!-- Menampilkan Foto Mitra di Modal -->
                    <p class="mt-3">Mitra kami yang support di program {{ $program->judul }}</p>
                    <div class="d-flex justify-content-start mt-3 flex-wrap">
                        @foreach ($program->mitras as $mitra)
                            <div class="mx-2 text-center">
                                <img src="{{ Storage::url($mitra->file) }}" alt="{{ $mitra->nama_mitra }}"
                                    class="img-fluid" style="max-width: 100px; height: auto;">
                            </div>
                        @endforeach
                    </div>

                    <!-- Progress Bars -->
                    <!--<label for="recipient-beneficiaries"><em>Penerima Manfaat <strong>{{ $program->judul }}</strong>-->
                    <!--        berjumlah-->
                    <!--        <strong>{{ $program->program_yang_berhasil_dijalankan }}</strong> orang </em></label>-->
                    <!--<div class="progress mt-2">-->
                    <!--    <div class="progress-bar progress-bar-primary" role="progressbar"-->
                    <!--        style="width: {{ $program->jumlah_target_tercapai > 0 ? ($program->program_yang_berhasil_dijalankan / $program->jumlah_target_tercapai) * 100 : 0 }}%;"-->
                    <!--        aria-valuenow="{{ $program->program_yang_berhasil_dijalankan }}" aria-valuemin="0"-->
                    <!--        aria-valuemax="{{ $program->jumlah_target_tercapai }}">-->
                    <!--    </div>-->
                    <!--</div>-->

                    <!--<label for="target"><em>Target yang Harus Dicapai-->
                    <!--        <strong>{{ $program->jumlah_target_tercapai }}</strong>-->
                    <!--        orang</em></label>-->
                    <!--<div class="progress mt-2">-->
                    <!--    <div class="progress-bar progress-bar-warning" role="progressbar"-->
                    <!--        style="width: {{ $program->jumlah_target_tercapai > 0 ? ($program->jumlah_target_tercapai / $program->jumlah_target_tercapai) * 100 : 0 }}%;"-->
                    <!--        aria-valuenow="{{ $program->jumlah_target_tercapai }}" aria-valuemin="0"-->
                    <!--        aria-valuemax="{{ $program->jumlah_target_tercapai }}">-->
                    <!--    </div>-->
                    <!--</div>-->

                    <!-- Pencapaian Program -->
                    <label for="combined-target"><em>Pencapaian Program <strong>{{ $program->judul }},</strong>
                            <strong>{{ $program->program_yang_berhasil_dijalankan }}</strong> orang telah
                            menerima manfaaat dari target <strong>{{ $program->jumlah_target_tercapai }}</strong> yang
                            harus dicapai</em></label>
                    <div class="progress mt-2">
                        <!-- Penerima Manfaat (Hijau) -->
                        <div class="progress-bar progress-bar-primary" role="progressbar"
                            style="width: {{ $program->jumlah_target_tercapai > 0 ? ($program->program_yang_berhasil_dijalankan / $program->jumlah_target_tercapai) * 100 : 0 }}%;"
                            aria-valuenow="{{ $program->program_yang_berhasil_dijalankan }}" aria-valuemin="0"
                            aria-valuemax="{{ $program->jumlah_target_tercapai }}">
                        </div>

                        <!-- Sisa Target yang Ingin Dicapai (Kuning) -->
                        <div class="progress-bar progress-bar-warning" role="progressbar"
                            style="width: {{ $program->jumlah_target_tercapai > 0 ? (($program->jumlah_target_tercapai - $program->program_yang_berhasil_dijalankan) / $program->jumlah_target_tercapai) * 100 : 0 }}%;"
                            aria-valuenow="{{ $program->jumlah_target_tercapai - $program->program_yang_berhasil_dijalankan }}"
                            aria-valuemin="0" aria-valuemax="{{ $program->jumlah_target_tercapai }}">
                        </div>
                    </div>

                 <div id="program-modal-footer-{{ $program->id }}"
                    class="modal-footer fixed-bottom border-0 p-3 d-flex flex-column align-items-center text-center">
                
                    <!-- Tombol Donasi -->
                    <!-- Tombol Donasi -->
                    <button type="button" class="btn btn-primary btn-donasi py-3 d-flex align-items-center justify-content-center gap-2"
                        data-program-id="{{ $program->id }}"
                        data-program-image="{{ asset('storage/' . $program->thumbnail_image) }}"
                        style="max-width: 600px; width: 100%; position: relative;">
                        
                        <!-- Ikon Kertas Uang -->
                        <span style="position: relative; display: inline-block; width: 1.5em; height: 1.3em;">
                            <i class="fas fa-money-bill-wave" style="color: white; font-size: 1.2em;"></i> <!-- Kertas Uang Putih -->
                        </span>
                    
                        Berbagi Sekarang
                    </button>

                
                    <!-- Tombol Bagikan -->
                    <!--<button type="button" class="btn btn-secondary py-3"-->
                    <!--    onclick="shareProgram('{{ $program->judul }}', '{{ $program->id }}')"-->
                    <!--    style="max-width: 600px; width: 100%;">-->
                    <!--    Ã°Å¸â€œÂ¤ Bagikan Program-->
                    <!--</button>-->
                    
                    <button type="button" class="btn btn-secondary py-3"
                        onclick="shareProgram('{{ $program->id }}')"
                        style="max-width: 600px; width: 100%;">
                        ðŸ“£ Bagikan Program
                    </button>

                
                </div>

                </div>
            </div>
        </div>
    </div>
@endforeach

@include('LandingPageKilau.Components.donasi-modal')

<script>
     document.addEventListener('DOMContentLoaded', function () {
        const modalElements = document.querySelectorAll('[id^="program-modal"]');
    
        modalElements.forEach(modal => {
            const modalId = modal.getAttribute('id');
            const modalFooter = document.querySelector(`#program-modal-footer-${modalId.split('program-modal')[1]}`);
    
            modal.addEventListener('shown.bs.modal', function () {
                // Saat modal muncul, pastikan tombol naik ke atas hanya untuk modal program
                if (modalFooter) {
                    modalFooter.style.transform = 'translateY(0)';
                }
            });
    
            modal.addEventListener('hidden.bs.modal', function () {
                // Saat modal ditutup, sembunyikan tombol kembali
                if (modalFooter) {
                    modalFooter.style.transform = 'translateY(100px)';
                }
            });
        });
    });
    
    //   function shareProgram(title, programId) {
    //     let baseUrl = "https://home.kilauindonesia.org/";
    //     let slugTitle = title.toLowerCase().replace(/[^a-z0-9]+/g, '-'); // Buat slug dari judul
    //     let shareUrl = `${baseUrl}?judul=${encodeURIComponent(slugTitle)}&modal=${programId}#program-modal${programId}`;
    
    //     if (navigator.share) {
    //         navigator.share({
    //             title: title,
    //             text: `Cek program ini: ${title}`,
    //             url: shareUrl
    //         }).then(() => console.log('Berhasil dibagikan'))
    //           .catch((error) => console.log('Gagal berbagi:', error));
    //     } else {
    //         let whatsappUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(title + '\n' + shareUrl)}`;
    //         window.open(whatsappUrl, '_blank');
    //     }
    // }
    
    async function shareProgram(programId) {
        let originalUrl = `https://home.kilauindonesia.org/?modal=${programId}`;
        
        try {
            // Kirim permintaan ke TinyURL API
            let response = await fetch(`https://api.tinyurl.com/create`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer YOUR_TINYURL_API_KEY',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    url: originalUrl
                })
            });
    
            let data = await response.json();
            let shortUrl = data.data.tiny_url || originalUrl; // Gunakan shortlink jika tersedia
    
            if (navigator.share) {
                navigator.share({
                    title: "Lihat Program Ini!",
                    text: "Yuk, lihat program ini di Kilau Indonesia!",
                    url: shortUrl
                }).then(() => console.log('Berhasil dibagikan'))
                  .catch((error) => console.log('Gagal berbagi:', error));
            } else {
                let whatsappUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent("Yuk, lihat program ini di Kilau Indonesia! " + shortUrl)}`;
                window.open(whatsappUrl, '_blank');
            }
    
        } catch (error) {
            console.error("Error generating short URL:", error);
        }
    }

    
    // Buka modal secara otomatis jika URL mengandung parameter ?modal=ID
   document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        const modalId = urlParams.get("modal");
        const judulSlug = urlParams.get("judul");
    
        var iklanModalElement = document.getElementById('iklanModal');
        var donasiModalElement = document.getElementById('donasiModal');
    
        // Jika ada modal program dalam URL, jangan tampilkan modal iklan & donasi
        if (modalId || judulSlug) {
            if (iklanModalElement) iklanModalElement.remove();
            if (donasiModalElement) donasiModalElement.remove();
        } else {
            // Jika tidak ada modal program, tampilkan modal iklan
            var iklanModal = new bootstrap.Modal(iklanModalElement);
            iklanModal.show();
        }
    
        // Buka modal program jika ada parameter `modal` atau `judul`
        if (modalId) {
            let modalElement = document.getElementById(`program-modal${modalId}`);
            if (modalElement) {
                let modalInstance = new bootstrap.Modal(modalElement);
                modalInstance.show();
            }
        } else if (judulSlug) {
            document.querySelectorAll('[id^="program-modal"]').forEach(modal => {
                let modalTitle = modal.querySelector(".modal-title").textContent.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                if (modalTitle === judulSlug) {
                    let modalInstance = new bootstrap.Modal(modal);
                    modalInstance.show();
                }
            });
        }
    });



    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.open-modal'); // Tombol "Lihat Detail"
        const donasiButtons = document.querySelectorAll('.btn-donasi'); // Tombol "Donasi Sekarang"
        const donasiModalElement = document.getElementById('ourProgramDonasiModal');
        const donasiModal = new bootstrap.Modal(donasiModalElement);
        const programIdInput = document.getElementById('ourProgramIdInput'); // Input ID Program
        const programImageElement = document.getElementById('ourSelectedProgramImage');

        // Simpan status modal
        let isModalOpen = false;

        // Fungsi Menutup Semua Modal Terbuka
        function closeAllModals() {
            document.querySelectorAll('.modal.show').forEach(modal => {
                const instance = bootstrap.Modal.getInstance(modal);
                if (instance) {
                    instance.hide();
                }
            });

            // Hapus backdrop modal agar tidak bertumpuk
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
        }

        // **Klik Tombol "Lihat Detail" Untuk Membuka Modal Program**
        buttons.forEach(button => {
            button.addEventListener('click', function () {
                closeAllModals(); // Tutup semua modal yang terbuka
                const modalId = this.getAttribute('data-id'); // Ambil ID Modal Program
                const modalTarget = new bootstrap.Modal(document.getElementById(modalId));
                modalTarget.show();
            });
        });

        // **Klik Tombol "Donasi" Untuk Membuka Modal Donasi**
        donasiButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();

                const programId = this.getAttribute('data-program-id');
                const programImage = this.getAttribute('data-program-image');

                // **Tutup modal program sebelum modal donasi dibuka**
                closeAllModals();

                setTimeout(() => { // Beri jeda agar modal program benar-benar tertutup
                    // Set ID program ke dalam modal donasi
                    programIdInput.value = programId;

                    if (programImage && programImage !== "null") {
                        programImageElement.src = programImage;
                        programImageElement.style.display = "block"; // Tampilkan gambar
                    } else {
                        programImageElement.style.display = "none"; // Sembunyikan jika tidak ada gambar
                    }

                    // Buka modal donasi setelah modal program benar-benar tertutup
                    donasiModal.show();
                    isModalOpen = true;

                }, 300); // Delay 300ms agar modal donasi tidak langsung muncul sebelum modal program tertutup
            });
        });

        // **Pastikan modal tertutup dengan benar**
        donasiModalElement.addEventListener('hidden.bs.modal', function () {
            isModalOpen = false;

            // Hapus backdrop modal yang tertinggal
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');

            // Refresh hanya modal program, bukan seluruh halaman
            // location.reload();
        });

        // **Pastikan tidak ada modal yang tertinggal setelah refresh**
        window.addEventListener('beforeunload', function () {
            closeAllModals();
        });

        // **Membantu Scroll Modal Donasi Jika Tidak Bisa Scroll**
        donasiModalElement.querySelector('.modal-body').style.overflowY = 'auto';
    });
</script>


