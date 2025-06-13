@php
    use App\Models\DonasiKilau;
@endphp
 
 
<style>
    #program-card {
        width: 410px;
        min-width: 410px;
        border-radius: 0.80rem !important;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        transition: transform 0.3s ease;
        scroll-snap-align: start;
    }

    #program-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .progress {
        height: 8px;
        border-radius: 10px;
        background-color: #e9ecef;
    }

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

    #program-scroll-container {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 1rem;
        padding-bottom: 1rem;
        -webkit-overflow-scrolling: touch;
    }

    #program-scroll-container::-webkit-scrollbar {
        height: 6px;
    }

    #program-scroll-container::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-grow: 1;
    }

    .action-buttons {
        margin-top: auto;
        display: flex;
        gap: 0.5rem;
    }

    .action-buttons .btn {
        min-height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 1;
    }

    .btn-donasi i {
        font-size: 1.2rem;
    }
    
     /* ---- Kartu feedback ---- */
    .feedback-card {
        position: relative;
    }
    .feedback-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 6px;                     /* lebar strip */
        border-radius: .5rem 0 0 .5rem; /* ikut radius kartu */
        background-color: #28a745;      /* Bootstrap 'success' → hijau */
    }

    .badge-donasi {
        font-size: .7rem;          /* lebih mungil daripada badge default */
        font-weight: 600;
    }

</style>


<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-4">{{ $programMenu->judul }}</h1>
            <p class="lead">{{ $programMenu->subjudul }}</p>
        </div>

        <div id="program-scroll-container">
            @foreach ($programs as $program)
                @php
                    $done = $program->program_yang_berhasil_dijalankan;
                    $target = $program->jumlah_target_tercapai;
                    $percent = $target > 0 ? ($done / $target) * 100 : 0;
                @endphp

                <div class="program-card" id="program-card">
                    <img src="{{ asset('storage/' . $program->thumbnail_image) }}"
                         alt="{{ $program->judul }}"
                         class="program-img">

                    <div class="card-body p-3 text-center d-flex flex-column">
                        <div>
                            <h5 class="card-title">{{ $program->judul }}</h5>
                            <p class="card-text text-muted mb-3">
                                {{ \Illuminate\Support\Str::limit(strip_tags($program->deskripsi), 60, '...') }}
                            </p>

                            <label class="text-center w-100 d-block">Pencapaian Program</label>
                            <div class="progress mt-2">
                                <div class="progress-bar progress-bar-primary"
                                     style="width: {{ $percent }}%;"
                                     role="progressbar"
                                     aria-valuenow="{{ $percent }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                                <div class="progress-bar progress-bar-warning"
                                     style="width: {{ 100 - $percent }}%;">
                                </div>
                            </div>

                            <p class="small mt-2">{{ $done }} orang telah terdampak dari target {{ $target }} penerima manfaat</p>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="action-buttons mt-3">
                            <button class="btn btn-primary open-modal"
                                    data-id="program-modal{{ $program->id }}">
                                Lihat Detail
                            </button>

                            <button type="button"
                                    class="btn btn-outline-primary btn-donasi"
                                    data-program-id="{{ $program->id }}"
                                    data-program-image="{{ asset('storage/' . $program->thumbnail_image) }}">
                                <i class="fas fa-donate"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

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
                    
                    {{-- ====== Daftar Feedback Donatur ====== --}}
                    @if($program->feedbacks->isNotEmpty())
                    <h5 class="mt-4 d-flex align-items-center">
                        Dukungan Donatur
                        <span class="badge bg-primary ms-2">
                            {{ $program->feedbacks_count ?? $program->feedbacks->count() }}
                        </span>
                    </h5>

                    @foreach($program->feedbacks as $fb)
                        <div class="feedback-card d-flex gap-3 mb-3 p-2 ps-3 border rounded-3 shadow-sm bg-light">
                            {{-- Avatar --}}
                            <span class="rounded-circle bg-white d-inline-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;">
                                <i class="fas fa-user text-secondary"></i>
                            </span>

                            {{-- Nama, badge donasi, waktu, komentar --}}
                            <div>
                                <p class="mb-1 fw-semibold d-flex align-items-center flex-wrap">
                                    {{ $fb->nama }}

                                    @if($fb->status_donasi == DonasiKilau::DONASI_AKTIVE)
                                        <span class="badge bg-success badge-donasi ms-2 d-inline-flex align-items-center">
                                            <i class="fas fa-check-circle me-1"></i> Berdonasi
                                        </span>
                                    @endif

                                    <small class="text-muted ms-2">
                                        • {{ $fb->created_at->diffForHumans(null,false,false,2) }}
                                    </small>
                                </p>

                                <p class="mb-0 text-muted">{{ $fb->feedback }}</p>
                            </div>
                        </div>
                    @endforeach
                    @endif

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
                    
                   <button type="button"
                            class="btn btn-secondary py-3 d-flex align-items-center justify-content-center gap-2"
                            onclick="shareProgram('{{ $program->id }}')"
                            style="max-width: 600px; width: 100%;">
                    
                        {{-- Ikon share (FontAwesome 5/6) --}}
                        <i class="fas fa-share-alt" style="font-size:1.2rem;"></i>
                    
                        Bagikan Program
                    </button>
                    
                      <!-- Tombol Bagikan Referral -->
                       <button type="button"
                            class="btn py-3 mt-2 w-100 btn-referral-share d-none"
                            data-program-id="{{ $program->id }}"
                            style="max-width: 600px; width: 100%; background-color: #6f42c1; color: white;">
                            🔗 Salin Link Referral
                        </button>

                </div>

                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Login Reminder Referral -->
<div class="modal fade" id="authReferralModal" tabindex="-1" aria-labelledby="authReferralLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="authReferralLabel">Authentikasi Diperlukan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <p>Untuk membagikan link referral dan mendapatkan poin, Anda perlu login terlebih dahulu.</p>
        <div class="d-flex justify-content-center gap-3 mt-3">
            <a href="{{ url('/login') }}" class="btn btn-primary">Login</a>
            <a href="{{ url('/register') }}" class="btn btn-outline-secondary">Registrasi</a>
        </div>
      </div>
    </div>
  </div>
</div>


@include('LandingPageKilau.Components.donasi-modal')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const username = localStorage.getItem('user_name');
        const referralCode = localStorage.getItem('user_referral_code');

        document.querySelectorAll('.btn-referral-share').forEach(function (btn) {
            const programId = btn.dataset.programId;

            btn.addEventListener('click', function () {
                if (!username) {
                    // Belum login
                    const authModal = new bootstrap.Modal(document.getElementById('authReferralModal'));
                    authModal.show();
                } else if (username && !referralCode) {
                    // Login tapi belum punya referral code
                   Swal.fire({
                        icon: 'warning',
                        title: 'Referral Code Belum Aktif',
                        html: `Akun Anda belum memiliki referral code.<br>Silakan hubungi <strong>Admin Kilau</strong> langsung melalui:<br><a href="https://wa.me/628112484484" target="_blank" class="fw-bold text-decoration-underline">0811-2484-484</a>`,
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#6f42c1'
                    });

                } else {
                    // Punya referral code
                    const referralUrl = `{{ url('/program') }}/${programId}/referral/${encodeURIComponent(referralCode)}`;
                
                    // ✅ Tampilkan alert dengan textarea agar link tidak terpotong
                    Swal.fire({
                        icon: 'success',
                        title: 'Link Referral Anda Siap Dibagikan!',
                        html: `
                            <textarea class="form-control text-start mb-3" rows="2" readonly>${referralUrl}</textarea>
                            <div class="text-muted small mb-2">Link ini sudah disalin ke clipboard. Silakan bagikan ke teman atau keluarga untuk mengumpulkan poin.</div>
                            <a href="https://wa.me/?text=${encodeURIComponent(referralUrl)}" target="_blank" class="btn btn-success btn-sm">📤 Bagikan via WhatsApp</a>
                        `,
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#6f42c1',
                        width: 600
                    });
                
                    // ✅ Tetap salin ke clipboard di background
                    navigator.clipboard.writeText(referralUrl).catch(() => {
                        Swal.fire('Oops', 'Gagal menyalin link', 'error');
                    });
                }

            });

            // Tampilkan semua tombol
            btn.classList.remove('d-none');
        });

        @if (session('scrollToProgram'))
            setTimeout(() => {
                const el = document.getElementById('program-modal{{ session('scrollToProgram') }}');
                if (el) new bootstrap.Modal(el).show();
            }, 500);
        @endif
    });
</script>



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


