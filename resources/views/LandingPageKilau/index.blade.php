@extends('App.master')

@php
    // Cek apakah fungsi getEmbedURL sudah dideklarasikan sebelumnya
    if (!function_exists('getEmbedURL')) {
        // Fungsi untuk mendapatkan embed URL dari URL YouTube
        function getEmbedURL($url)
        {
            // Menghapus parameter query (seperti ?si=...) jika ada
            $url = preg_replace('/\?[^\/]+$/', '', $url);

            // Cek apakah URL dalam format 'youtu.be', 'youtube.com', atau 'youtube.com/shorts'
            $regExp =
                '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:shorts\/([\w\-]{11})|(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/)([\w\-]{11}))|youtu\.be\/([\w\-]{11}))/';
            preg_match($regExp, $url, $match);

            if (!empty($match[1])) {
                // Jika URL adalah Shorts
                return 'https://www.youtube.com/embed/' . $match[1]; // Return embed URL untuk Shorts
            } elseif (!empty($match[2])) {
                // Jika URL adalah video standar YouTube
                return 'https://www.youtube.com/embed/' . $match[2]; // Return embed URL untuk video standar
            } elseif (!empty($match[3])) {
                // Jika URL menggunakan format 'youtu.be'
                return 'https://www.youtube.com/embed/' . $match[3]; // Return embed URL untuk video standar
            }

            return null; // Jika URL tidak valid
        }
    }

    // Cek apakah fungsi extractVideoId sudah dideklarasikan sebelumnya
    if (!function_exists('extractVideoId')) {
        // Fungsi untuk mengekstrak ID video dari URL standar YouTube
        function extractVideoId($url)
        {
            $url = preg_replace('/\?[^\/]+$/', '', $url); // Menghapus query parameter

            // Mengambil ID video dari URL standar YouTube atau YouTube Shorts
            $pattern =
                '/(?:https?:\/\/)?(?:www\.)?youtube\.com\/(?:shorts\/([\w\-]{11})|(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/)([\w\-]{11}))|youtu\.be\/([\w\-]{11})/';
            preg_match($pattern, $url, $matches);

            return $matches[1] ?? ($matches[2] ?? ($matches[3] ?? null));
        }
    }
@endphp


@section('style')
    <style>
        #testimonial-container {
            position: relative;
            overflow-x: hidden;
            /* Menyembunyikan scrollbar horizontal */
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }

        #testimonial-container .col-12 {
            scroll-snap-align: start;
        }

        /* Media Query untuk Mobile */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            #berita-container {
                flex-direction: column;
                overflow-x: auto;
            }

            .col-lg-4,
            .col-md-6 {
                width: 100% !important;
                flex: 0 0 100%;
            }

            .card-title a {
                font-size: 16px;
            }

            .card-text {
                font-size: 12px;
            }
        }

        /* Media Query untuk Tablet */
        @media (max-width: 1024px) {

            .col-lg-4,
            .col-md-6 {
                width: 100% !important;
            }
        }

        .modal-dialog {
            max-width: 800px;
            max-height: none;
            overflow-y: hidden;
        }

        .modal-body {
            /* max-height: 75vh; */
            overflow-y: auto;
        }

        /* Tampilan Mobile Dinamis */
        @media (max-width: 768px) {

            /* Menyesuaikan gambar agar full width */
            .modal-body img {
                width: 100%;
                height: auto;
                /* Agar tidak terdistorsi */
                object-fit: cover;
            }

            /* Menyesuaikan ukuran tombol agar full width */
            .modal-footer .btn {
                width: 100%;
                padding: 12px;
                font-size: 16px;
                /* Memperbesar ukuran font agar lebih terlihat */
            }
        }

        #donasiModal .modal-content {
            background-color: #1363c6;
            color: white;
            background-image: url({{ asset('assets/img/bg-hero.png') }});
            background-size: cover;
            background-position: center;
        }

        /* Modal Header Styling */
        #donasiModal .modal-header {
            background-color: #1363c6;
            color: white;
        }

        /* Styling untuk tombol donasi */
        #donasiModal .btn-custome {
            background-color: rgb(9, 9, 188);
            color: white;
        }

        #donasiModal .btn-custome:hover {
            background-color: rgb(9, 9, 97);
            color: white;
        }

        .selected-btn {
            background-color: white !important;
            color: black !important;
        }

        .bg-white {
            background-color: white !important;
        }

        .bg-transparent {
            background-color: transparent !important;
        }

      /* ========= IKLAN MODAL – aturan umum (SEMUA ukuran) ========= */
        #iklanModal .modal-dialog{
            /* ukuran desktop/tablet – Anda boleh ubah angka */
            max-width:750px;
        }
        
        #iklanModal .modal-body{
            padding:0;           /* tetap tanpa padding di semua ukuran */
            overflow:hidden;     /* cegah scroll horizontal */
        }
        
        #iklanModal .modal-body img{
            width:100%;
            object-fit:contain;
        }
        
        /* footer & tombol (berlaku di semua ukuran) */
        #iklanModal .modal-footer{
            padding:.5rem 1rem .75rem;
        }
        #iklanModal .modal-footer .btn{
            font-size:15px;
            padding:10px 12px;
        }
        
        /* ========= penyesuaian khusus MOBILE (≤ 576 px) ========= */
        @media (max-width:576px){
        
          /* beri jarak atas‑bawah via variable Bootstrap */
          #iklanModal{ --bs-modal-margin:1rem; }
        
          #iklanModal .modal-dialog{
              max-width:90vw!important;   /* kartu lebih ramping */
              margin:auto;                /* biarkan Bootstrap “center” */
              width:auto;
          }
        
          #iklanModal .modal-body{
              max-height:65vh;            /* batasi tinggi total */
          }
        
          #iklanModal .modal-body img{
              height:260px!important;     /* override tinggi gambar */
          }
        }
        
        /* ===== versi layar >576 px (tinggi gambar 420 px) ===== */
        @media (min-width:577px){
          #iklanModal .modal-body img{
              height:420px;               /* tinggi desktop */
          }
        }
        
        /* cegah scroll horisontal di mana pun */
        #iklanModal,
        #iklanModal .modal-dialog,
        #iklanModal .modal-content{
            overflow-x:hidden;
        }
        
        /* hilangkan kompensasi scrollbar Bootstrap */
        body.modal-open{ padding-right:0!important; }


       
    </style>
@endsection

@section('content')

    <!-- Modal Iklan (muncul saat halaman dimuat) -->
    <div class="modal fade" id="iklanModal" tabindex="-1" aria-labelledby="iklanModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" style="max-width:750px; max-height: 500px;">
            <div class="modal-content border rounded-3 shadow">

                {{-- ==================== BODY ==================== --}}
             <div class="modal-body pt-4 pb-2 p-0 position-relative">

                    <div id="carouselIklan" class="carousel slide" data-bs-ride="carousel">

                        <div class="carousel-inner">
                            @foreach ($donasiiklan as $idx => $item)
                                @php
                                    $isPng = strtolower(pathinfo($item->file, PATHINFO_EXTENSION)) === 'png';
                                    $link  = trim($item->link ?? '');
                                    $label = trim($item->name_button_iklan ?? '');
                                    $icon  = trim($item->icon_iklan ?? '');
                                @endphp
                            
                                <div class="carousel-item {{ $idx ? '' : 'active' }}"
                                     data-link="{{ $link }}"
                                     data-label="{{ $label }}"
                                     data-icon="{{ $icon }}">
                                    <img src="{{ Storage::url($item->file) }}"
                                         class="d-block mx-auto img-fluid {{ $isPng ? '' : 'bg-white' }}"
                                         style="max-width:85%;max-height:380px;object-fit:contain;
                                                padding:{{ $isPng ? 0 : '10px' }};border-radius:.5rem;">
                                </div>
                            @endforeach


                          </div>
                          

                       <button class="carousel-control-prev" type="button" data-bs-target="#carouselIklan" data-bs-slide="prev"
                                style="background: none; border: none;">
                            <i class="fas fa-chevron-left" style="color: #1363c6; font-size: 1.8rem;"></i>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselIklan" data-bs-slide="next"
                                style="background: none; border: none;">
                            <i class="fas fa-chevron-right" style="color: #1363c6; font-size: 1.8rem;"></i>
                        </button>


                    </div>
                </div>

                {{-- ==================== FOOTER ==================== --}}
                <div class="modal-footer border-0 px-4 pb-4">
                   @php
                        // ambil slide pertama (bisa null kalau koleksi kosong)
                        $first = $donasiiklan->first();
                    
                        // ===== Label tombol =====
                        //  - kalau name_button_iklan null, "", atau "   " → default
                        $label = blank($first?->name_button_iklan)
                                 ? 'Berbagi Sekarang'
                                 : $first->name_button_iklan;
                    
                        // ===== Ikon tombol =====
                        //  - kalau icon_iklan null / "" / " " → default
                        $ikon  = blank($first?->icon_iklan)
                                 ? 'fa-money-bill-wave'
                                 : $first->icon_iklan;
                    @endphp
                    
                    <button id="openDonasiModal"
                        class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 rounded-pill shadow-sm">
                        <i id="btn-icon" class="fas {{ $ikon }}"></i>
                        <span id="btn-label">{{ $label }}</span>
                    </button>



                </div>

            </div>
        </div>
    </div>

     <!-- Modal Donasi Start -->
    <div class="modal fade" id="donasiModal" tabindex="-1" aria-labelledby="donasiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-light" id="donasiModalLabel">Donasi Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Hi, apakah kamu ingin menjadi bagian dari kami untuk menebarkan manfaat?</p>
                    <p><strong>⭐ Ayo Berbagi sekarang ⭐</strong></p>

                    <form id="donasiForm" method="POST">
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
                                @foreach ([1, 25000, 50000, 75000, 100000] as $amount)
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

    <!-- Hero Start -->
    <div class="container-fluid py-5 bg-primary hero-header" style="">
        <div class="container py-5">
            <!-- Carousel -->
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
                <!-- Indicators -->
                <div class="carousel-indicators" style="bottom: -90px !important;">
                    @foreach ($homeKilau as $index => $item)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $index == 0 ? 'active' : '' }}"
                            aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                            aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>

                <!-- Slides -->
                <div class="carousel-inner hero-inner">
                    @foreach ($homeKilau as $index => $item)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <div class="row g-5 pt-5">
                                <!-- Kolom Teks -->
                                <div class="col-lg-6 col-12 align-self-center text-center text-lg-start mb-lg-5">
                                    <h1 class="display-4 text-white ">
                                        {{ $item->judul_home }}
                                    </h1>
                                </div>

                                <!-- Kolom Gambar atau Animasi Lottie -->
                                <div class="col-lg-6 col-12 order-1 order-lg-3 ps-3 d-flex justify-content-center">
                                    @if ($item->file_home)
                                        <!-- Tampilkan Gambar Jika Ada -->
                                        <img src="{{ Storage::url($item->file_home) }}" class="img-fluid"
                                            alt="{{ $item->judul_home }}" style="width: 50% !important; height:auto;">
                                    @else
                                        <!-- Tampilkan Animasi Lottie Jika Tidak Ada Gambar -->
                                        <dotlottie-player
                                            src="https://lottie.host/e0a24f8d-7cba-4c46-8167-c7cb86af6595/17K5vHPYIi.lottie"
                                            background="transparent" speed="1"
                                            style="max-width: 100%; height: auto;" loop autoplay>
                                        </dotlottie-player>
                                        {{-- <dotlottie-player
                                            src="https://lottie.host/68b0ea5a-ba38-494e-8dda-d9ecdeffc2a2/eycheMpLtU.lottie"
                                            background="transparent" speed="1" style="max-width: 100%; height: auto;"
                                            loop autoplay>
                                        </dotlottie-player> --}}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    {{-- About Section Start --}}
    @if ($tentangMenu && $tentangMenu->status == 'Aktif')
        <div class="container-fluid py-5">
            <div class="container py-5">
                <!-- Judul Section -->
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <!-- Teks "Tentang Kami" dengan Efek Hover -->
                        <div class="tooltip-container">
                            <h1 class="display-5 fw-bold mb-2 hover-text">{{ $tentangMenu->judul }}</h1>
                        </div>
                        <p class="lead mb-4">
                            {{ $tentangMenu->subjudul }}
                        </p>
                    </div>
                </div>

                <div class="row gx-0 py-3">
                    @foreach ($tentangs as $tentang)
                        <!-- Bagian Teks -->
                        <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up"
                            data-aos-delay="200">
                            <div class="content">
                                <h2>{{ $tentang->judul_tentang_kami }}</h2>
                                <p>{{ $tentang->deskripsi }}</p>

                                <!-- Lihat Selengkapnya -->
                                <a href="{{ route('tentangkami.landingpage') }}" class="btn btn-primary mt-2">
                                    Lihat Selengkapnya
                                </a>
                            </div>
                        </div>

                        <!-- Bagian Gambar -->
                        <div class="col-lg-6 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
                            <img src="{{ $tentang->file ? Storage::url($tentang->file) : asset('assets/img/default.jpg') }}"
                                class="img-fluid about-img" alt="Tentang Kami">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($timelineMenu && $timelineMenu->status == 'Aktif')
        @include('LandingPageKilau.timline_interaktif')
    @endif

    @if ($beritaMenu && $beritaMenu->status == 'Aktif')
        <!-- Berita Start -->
        <div id="berita-donatur" class="container-fluid py-5">
            <div class="container py-5">
                <!-- Judul Section -->
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h1 class="display-5 fw-bold mb-2"> {{ $beritaMenu->judul }}</h1>
                        <p class="lead mb-4">
                            {{ $beritaMenu->subjudul }}
                        </p>
                    </div>
                </div>

                <!-- Konten Berita -->
                <div class="row g-4 flex-nowrap overflow-x-auto pb-3 scroll-hidden" id="berita-container">
                    <!-- Data Berita Akan Dimuat Dinamis dengan AJAX -->
                </div><!-- End Row -->

                <!-- Pagination -->
                <div class="row justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul id="pagination" class="pagination">
                            <!-- Tombol pagination akan dimuat oleh JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div><!-- End Container -->
        </div><!-- End Berita -->
    @endif

    @if ($campaignMenu && $campaignMenu->status == 'Aktif')
        @include('LandingPageKilau.campaign_kilau', [
            'campaignMenu' => $campaignMenu,
            'campaigns'    => $campaigns,
            'page'         => $page,
            'perPage'      => $perPage,
            'lastPage'     => $lastPage
        ])
    @endif

    @foreach ($iklanKilau as $item)
        @if ($item->iklanKilauLists->isNotEmpty())
            <!-- Memastikan ada data IklanKilauList yang aktif -->
            <div class="container-fluid bg-primary feature pt-5">
                <div class="container pt-5">
                    <div class="row g-5 align-items-center">
                        <!-- Kolom Kiri: Deskripsi -->
                        <div class="col-lg-6 align-self-center mb-md-5 pb-md-5 wow fadeIn" data-wow-delay="0.3s">
                            <div class="btn btn-sm border rounded-pill text-white px-3 mb-3">
                                Mengapa Memilih Kami
                            </div>
                            <h1 class="text-white mb-4">{{ $item->judul }}</h1>
                            <p class="text-light mb-4">{{ $item->deskripsi }}</p>

                            <!-- Menampilkan semua IklanKilauList tanpa kondisi status aktif -->
                            @foreach ($item->iklanKilauLists as $iklanList)
                                <div class="d-flex align-items-center text-white mb-3">
                                    <div class="btn-sm-square bg-white text-primary rounded-circle me-3">
                                        <i class="fa fa-check"></i>
                                    </div>
                                    <span>{{ $iklanList->name }}</span> <!-- Menampilkan name dari IklanKilauList -->
                                </div>
                            @endforeach

                            <!-- Menampilkan IklanKilauList yang aktif -->
                            @foreach ($item->iklanKilauLists as $iklanList)
                                @if ($iklanList->status_iklan_kilau_list == '1')
                                    <!-- Cek jika statusnya aktif -->
                                    <div class="d-flex align-items-center text-white mb-3">
                                        <div class="btn-sm-square bg-white text-primary rounded-circle me-3">
                                            <i class="fa fa-check"></i>
                                        </div>
                                        <span>{{ $iklanList->name }}</span>
                                        <!-- Menampilkan name dari IklanKilauList yang aktif -->
                                    </div>
                                @endif
                            @endforeach

                            <div class="row g-4 pt-3">
                                <div class="col-sm-6">
                                    <div class="d-flex rounded p-3" style="background: rgba(256, 256, 256, 0.1)">
                                        <i class="fa fa-building fa-3x text-white"></i>
                                        <div class="ms-3">
                                            <h2 class="text-white mb-0">{{ $item->jumlah_yayasan }}</h2>
                                            <p class="text-white mb-0">Kantor Cabang</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex rounded p-3" style="background: rgba(256, 256, 256, 0.1)">
                                        <i class="fa fa-hand-holding-heart fa-3x text-white"></i>
                                        <div class="ms-3">
                                            <h2 class="text-white mb-0" id="jumlahDonatur">Loading...</h2>
                                            <p class="text-white mb-0">Donatur Kami</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Animasi Lottie atau Gambar -->
                        <div class="col-lg-6 d-flex justify-content-center align-items-center wow fadeIn"
                            data-wow-delay="0.5s">
                            @if ($item->file)
                                <!-- Jika ada gambar, tampilkan gambar -->
                                <img src="{{ Storage::url($item->file) }}" alt="Home Kilau Image" class="img-fluid"
                                    style="max-width: 350px; margin-left: 50px !important; height: auto;">
                            @else
                                <!-- Jika tidak ada gambar, tampilkan animasi Lottie -->
                                <dotlottie-player
                                    src="https://lottie.host/aad7ce21-732e-4713-b698-1318a30a3e18/xsi4Rvux4O.lottie"
                                    background="transparent" speed="1"
                                    style="max-width: 300px; width: 100%; height: auto;" loop autoplay>
                                </dotlottie-player>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if ($programMenu && $programMenu->status == 'Aktif')
        @include('LandingPageKilau.ourprogram')
    @endif

    @if ($testimoniMenu && $testimoniMenu->status == 'Aktif')
       @include('LandingPageKilau.testimoni')
    @endif

    {{--  @if ($testimoniMenu && $testimoniMenu->status == 'Aktif')
        <!-- Testimonial Start -->
        <div class="container-fluid bg-light py-5">
            <div class="container py-5">
                <div class="mx-auto text-center wow fadeIn" style="max-width: 500px">
                    <h1 class="mb-4">{{ $testimoniMenu->judul }}</h1>
                    <p class="lead">{{ $testimoniMenu->subjudul }}</p>
                </div>

                <!-- Konten Testimoni -->
                <div class="row overflow-auto d-flex flex-nowrap py-2 position-relative" id="testimonial-container">

                    @if ($testimonis && $testimonis->count())
                        @foreach ($testimonis as $testimoni)
                            <div class="col-12 col-sm-6 col-lg-4 mb-4">
                                <div class="card h-100 d-flex flex-column">
                                    <!-- If there's a video, place it on top -->
                                    @if ($testimoni->video_link)
                                        @php
                                            $embedURL = getEmbedURL($testimoni->video_link);
                                        @endphp
                                        @if ($embedURL)
                                            <div class="card-img-top">
                                                <iframe class="embed-responsive-item w-100" height="300"
                                                    src="{{ $embedURL }}" title="YouTube video player"
                                                    frameborder="0"
                                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                                    allowfullscreen>
                                                </iframe>
                                            </div>
                                        @else
                                            <p class="text-center">Video tidak tersedia.</p>
                                        @endif
                                    @endif

                                    <!-- Card Body (with flex-grow) -->
                                    <div class="card-body d-flex flex-column">
                                        <!-- Testimonial Info (Nama, Pekerjaan, Komentar) -->
                                        <div class="d-flex align-items-center mb-3">
                                            <img class="img-fluid rounded-circle"
                                                src="{{ $testimoni->file ? Storage::url($testimoni->file) : asset('assets/img/profile.jpg') }}"
                                                style="width: 60px; height: 60px; object-fit: cover;" />
                                            <div class="ms-3" style="flex-grow: 1; overflow: hidden;">
                                                <h5 class="mb-1"
                                                    style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                                                    {{ $testimoni->nama }}</h5>
                                                <span>{{ $testimoni->pekerjaan }}</span>
                                            </div>
                                        </div>

                                        <!-- If there's a comment, display it, else let the video occupy the space -->
                                        <div class="flex-grow-1">
                                            @if ($testimoni->komentar)
                                                <p class="card-text fs-6 mb-3">{{ strip_tags($testimoni->komentar) }}</p>
                                            @else
                                                <p class="card-text fs-6">No comments available.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Video Testimonial (if available) below text -->
                                    @if (!$testimoni->video_link)
                                        <div class="card-footer p-0">
                                            <p class="text-center">No video available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">Belum ada testimoni yang aktif.</p>
                    @endif
                </div><!-- End Testimonial -->
            </div><!-- End Container -->
        </div><!-- End Testimonial -->
    @endif --}}

    <!-- Modal untuk Komentar -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Beri Komentar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="commentForm" action="{{ route('testimonihome.create') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="commentName" class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" id="commentName"
                                placeholder="Masukkan nama Anda" required>
                        </div>
                        <div class="mb-3">
                            <label for="commentKerja" class="form-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" id="commentKerja"
                                placeholder="Masukkan pekerjaan Anda (Opsional)">
                        </div>
                        <div class="mb-3">
                            <label for="commentMessage" class="form-label">Komentar</label>
                            <textarea name="komentar" class="form-control" id="commentMessage" rows="3" placeholder="Tulis komentar Anda"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="commentFile" class="form-label">Upload Gambar (Opsional)</label>
                            <input type="file" name="file" class="form-control" id="commentFile"
                                accept="image/*">
                            <small class="text-muted">Hanya gambar dengan format jpeg, png, jpg, gif, svg yang
                                diterima.</small>
                            <div class="mt-2">
                                <img id="imagePreview" src="#" alt="Pratinjau Gambar"
                                    style="max-width: 50%; display: none;" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Kirim Komentar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- @if ($faqMenu && $faqMenu->status == 'Aktif')
        <!-- FAQs Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <!-- Judul Section -->
                <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 500px">
                    <h1 class="mb-4">{{ $faqMenu->judul }}</h1>
                    <p class="lead">{{ $faqMenu->subjudul }}</p>
                </div>

                <!-- Konten FAQ -->
                <div class="row">
                    <!-- Kolom Pertama -->
                    <div class="col-lg-6 py-4">
                        <div class="accordion" id="accordionFAQ1">
                            @foreach ($faqs->slice(0, ceil($faqs->count() / 2)) as $key => $faq)
                                <div class="accordion-item wow fadeIn" data-wow-delay="0.{{ $key + 1 }}s">
                                    <h2 class="accordion-header" id="heading{{ $key }}">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $key }}"
                                            aria-expanded="false" aria-controls="collapse{{ $key }}"
                                            onfocus="this.style.color='white';" onblur="this.style.color='';">
                                            {{ $faq->question }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $key }}" class="accordion-collapse collapse"
                                        aria-labelledby="heading{{ $key }}" data-bs-parent="#accordionFAQ1">
                                        <div class="accordion-body">
                                            {{ $faq->answer }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kolom Kedua -->
                    <div class="col-lg-6 py-4">
                        <div class="accordion" id="accordionFAQ2">
                            @foreach ($faqs->slice(ceil($faqs->count() / 2)) as $key => $faq)
                                <div class="accordion-item wow fadeIn" data-wow-delay="0.{{ $key + 5 }}s">
                                    <h2 class="accordion-header" id="heading{{ $key + ceil($faqs->count() / 2) }}">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $key + ceil($faqs->count() / 2) }}"
                                            aria-expanded="false" onfocus="this.style.color='white';"
                                            onblur="this.style.color='';"
                                            aria-controls="collapse{{ $key + ceil($faqs->count() / 2) }}">
                                            {{ $faq->question }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $key + ceil($faqs->count() / 2) }}"
                                        class="accordion-collapse collapse"
                                        aria-labelledby="heading{{ $key + ceil($faqs->count() / 2) }}"
                                        data-bs-parent="#accordionFAQ2">
                                        <div class="accordion-body">
                                            {{ $faq->answer }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div><!-- End Row -->
            </div><!-- End Container -->
        </div>
        <!-- FAQs End -->
    @endif --}}


    @if ($mitraMenu && $mitraMenu->status == 'Aktif')
        <!-- Mitra Donatur Start -->
        <div id="mitra-donatur" class="container-fluid py-5">
            <div class="container py-5">
                <!-- Judul Section -->
                <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 500px">
                    <h1 class="mb-4">{{ $mitraMenu->judul }}</h1>
                    <p class="lead">{{ $mitraMenu->subjudul }}</p>
                </div>

                <!-- Semua Baris Mitra Donatur -->
                <div class="row justify-content-center wow fadeIn py-5" data-wow-delay="0.3s"
                    style="gap: 20px; flex-wrap: wrap;">
                    @if ($mitras && $mitras->count())
                        @foreach ($mitras as $mitra)
                            <div class="col-lg-1 col-md-2 col-3 mb-4 text-center">
                                <img src="{{ $mitra->file ? Storage::url($mitra->file) : asset('images/default.png') }} "
                                    class="img-fluid w-100" style="max-width: 100px;" alt="{{ $mitra->nama_mitra }}">
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">Belum ada mitra donatur yang aktif.</p>
                    @endif
                </div>
            </div>
        </div>
        <!-- Mitra Donatur End -->
    @endif

@endsection

@section('scripts')
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="Mid-client-Cuh29wUmwqZAF-6t"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

    <script>
        
    </script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const modalId = urlParams.get("modal");
            const judulSlug = urlParams.get("judul");

            var iklanModalElement = document.getElementById('iklanModal');
            var donasiModalElement = document.getElementById('donasiModal');
            var iklanModal = iklanModalElement ? new bootstrap.Modal(iklanModalElement) : null;
            var donasiModal = donasiModalElement ? new bootstrap.Modal(donasiModalElement) : null;
            var isModalOpen = false;

            // **Cek apakah halaman dibuka dengan parameter modal program**
            if (!modalId && !judulSlug && iklanModal) {
                // **Jika tidak ada modal program, tampilkan modal iklan**
                iklanModal.show();
            }

            // **Klik "Berbagi Sekarang" untuk membuka modal donasi**
            $('#openDonasiModal').on('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                closeAllModals();

                setTimeout(() => {
                    if (donasiModal) {
                        donasiModal.show();
                        isModalOpen = true;
                    }
                }, 300);
            });

            // **Fungsi untuk menutup semua modal terbuka dengan benar**
            function closeAllModals() {
                $('.modal.show').modal('hide'); // Tutup semua modal yang sedang terbuka
                setTimeout(() => {
                    $('.modal-backdrop').remove(); // Hapus backdrop modal setelah animasi selesai
                    $('body').removeClass('modal-open').css({
                        overflow: '',
                        paddingRight: ''
                    }); // Pastikan body bisa di-scroll
                }, 300);
            }

            // **Pastikan modal donasi tertutup dengan bersih**
            $('#donasiModal, #iklanModal').on('hidden.bs.modal', function() {
                isModalOpen = false;
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });

            $('#iklanModal').on('hidden.bs.modal', function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });


            // **Pastikan tidak ada modal yang tertinggal setelah refresh**
            window.addEventListener('beforeunload', function() {
                closeAllModals();
            });
        });

        $(document).ready(function() {
            $('#donasiModal').on('shown.bs.modal', function () {
                $.post('{{ route('track.donasi.modal') }}', {
                    _token: $('meta[name="csrf-token"]').attr('content')
                });
            });
        });

        $(document).ready(function() {
            // Menampilkan modal donasi saat halaman dimuat
            // var donasiModal = new bootstrap.Modal(document.getElementById('donasiModal'));
            // donasiModal.show()

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
                                        // order_id: 'donasi-' + donasiId
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

        $(document).ready(function() {
            // Preview gambar sebelum upload
            $('#commentFile').on('change', function() {
                const file = this.files[0]; // Ambil file yang dipilih
                if (file) {
                    const reader = new FileReader(); // Buat FileReader untuk membaca file
                    reader.onload = function(e) {
                        // Tampilkan pratinjau gambar
                        $('#imagePreview').attr('src', e.target.result).css('display', 'block');
                    };
                    reader.readAsDataURL(file); // Baca file sebagai data URL
                } else {
                    // Reset pratinjau gambar jika file dihapus
                    $('#imagePreview').attr('src', '#').css('display', 'none');
                }
            });
        });

        // JavaScript untuk Efek Hover (Opsional)
        document.querySelector('.tooltip-container').addEventListener('mouseenter', function() {
            const tooltip = this.querySelector('.tooltip');
            tooltip.style.opacity = '1';
            tooltip.style.visibility = 'visible';
        });

        document.querySelector('.tooltip-container').addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.tooltip');
            tooltip.style.opacity = '0';
            tooltip.style.visibility = 'hidden';
        });

        $(document).ready(function() {
            let currentPage = 1;
            let perPage = 6; // Bisa disesuaikan jumlah berita per halaman

            function loadBerita(page = 1) {
                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita?page=${page}&per_page=${perPage}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            let beritaHtml = "";

                            response.data.forEach(function(berita, index) {
                                // Filter: Skip berita jika status tidak aktif
                                if (berita.status_berita === "Tidak Aktif") {
                                    return;
                                }
                                let fotoUtama = berita.foto ?
                                    `https://berbagipendidikan.org${berita.foto}` :
                                    "assets/img/no-image.jpg";
                                let foto2 = berita.foto2 ?
                                    `https://berbagipendidikan.org${berita.foto2}` : fotoUtama;
                                let foto3 = berita.foto3 ?
                                    `https://berbagipendidikan.org${berita.foto3}` : fotoUtama;
                                let beritaDetailUrl =
                                    `/berita/${berita.judul.replace(/\s+/g, '-')}`; // Ganti spasi dengan tanda hubung

                                beritaHtml += `
                                    <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch wow fadeIn" data-wow-delay="${0.1 * (index + 1)}s">
                                        <article class="card w-100 h-100" style="border-radius: 0.80rem !important; overflow: hidden; display: flex; flex-direction: column;">

                                        <div class="post-img card-img-top" style="height: 250px; overflow: hidden;">
                                            <div id="carouselBerita${berita.id}" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                <div class="carousel-item active">
                                                <img src="${fotoUtama}" alt="Foto Berita" class="d-block w-100" 
                                                    style="object-fit: cover; height: 250px; 
                                                        border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem; 
                                                        border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                                                </div>
                                                <div class="carousel-item">
                                                <img src="${foto2}" alt="Foto Berita" class="d-block w-100" 
                                                    style="object-fit: cover; height: 250px; 
                                                        border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem; 
                                                        border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                                                </div>
                                                <div class="carousel-item">
                                                <img src="${foto3}" alt="Foto Berita" class="d-block w-100" 
                                                    style="object-fit: cover; height: 250px; 
                                                        border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem; 
                                                        border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                                                </div>
                                            </div>
                                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselBerita${berita.id}" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#carouselBerita${berita.id}" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                                            <h2 class="title card-title">
                                            <a href="${beritaDetailUrl}" class="text-decoration-none">${berita.judul}</a>
                                            </h2>
                                            <div class="d-flex align-items-center mb-3">
                                            <div class="post-meta">
                                                <p class="post-author mb-0">Kilau Indonesia</p>
                                                <p class="post-date mb-0">
                                                <time datetime="${berita.tanggal}">${formatTanggal(berita.tanggal)}</time>
                                                </p>
                                            </div>
                                            </div>
                                        </div>
                                        </article>
                                    </div>
                                    `;

                            });

                            $("#berita-container").html(beritaHtml);
                            generatePagination(response.pagination);
                        } else {
                            $("#berita-container").html(
                                '<p class="text-center">Belum ada berita yang tersedia.</p>');
                        }
                    },
                    error: function() {
                        $("#berita-container").html(
                            '<p class="text-center text-danger">Gagal memuat berita.</p>');
                    }
                });
            }

            function generatePagination(pagination) {
                let paginationHtml = '';
                let maxVisiblePages = 5;

                if (pagination.current_page > 1) {
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="1">First</a></li>`;
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page - 1}">«</a></li>`;
                }

                let startPage = Math.max(1, pagination.current_page - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(pagination.last_page, startPage + maxVisiblePages - 1);

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
                }

                if (pagination.current_page < pagination.last_page) {
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page + 1}">»</a></li>`;
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.last_page}">Last</a></li>`;
                }

                $('#pagination').html(paginationHtml);
            }

            function formatTanggal(dateString) {
                let options = {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                };
                return new Date(dateString).toLocaleDateString("id-ID", options);
            }

            function loadJumlahDonatur() {
                $.ajax({
                    url: "https://kilauindonesia.org/api/donitung?kon=1",
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.message === "success") {
                            $("#jumlahDonatur").text(response.data);
                        } else {
                            $("#jumlahDonatur").text("Gagal Memuat");
                        }
                    },
                    error: function() {
                        $("#jumlahDonatur").text("Error");
                    }
                });
            }

            $(document).on('click', '#pagination a', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                loadBerita(page);
            });

            loadBerita();
            loadJumlahDonatur();
        });
    </script>
@endsection
