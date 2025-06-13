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
            $regExp = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:shorts\/([\w\-]{11})|(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/)([\w\-]{11}))|youtu\.be\/([\w\-]{11}))/';
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
            $url = preg_replace('/\?[^\/]+$/', '', $url);  // Menghapus query parameter

            // Mengambil ID video dari URL standar YouTube atau YouTube Shorts
            $pattern = '/(?:https?:\/\/)?(?:www\.)?youtube\.com\/(?:shorts\/([\w\-]{11})|(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/)([\w\-]{11}))|youtu\.be\/([\w\-]{11})/';
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

       /* ==================== GLOBAL ==================== */

        /* Mobile Layout */
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
        
        /* Tablet Layout */
        @media (max-width: 1024px) {
            .col-lg-4,
            .col-md-6 {
                width: 100% !important;
            }
        }
        
        /* Modal Default */
        .modal-dialog {
            max-width: 800px;
            max-height: none;
            overflow-y: hidden;
        }
        
        .modal-body {
            overflow-y: auto;
        }
        
        /* ==================== DONASI MODAL ONLY ==================== */
        
        /* Gaya Umum Donasi Modal */
        #donasiModal .modal-content {
            background-color: #1363c6;
            color: white;
            background-image: url('{{ asset('assets/img/bg-hero.png') }}');
            background-size: cover;
            background-position: center;
            border-radius: 10px;
        }
        
        #donasiModal .modal-header {
            background-color: rgba(19, 99, 198, 0.85);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        #donasiModal .modal-footer {
            background-color: rgba(19, 99, 198, 0.85);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Tombol Custom */
        #donasiModal .btn-custome {
            background-color: rgb(9, 9, 188);
            color: white;
            transition: background-color 0.3s ease;
        }
        
        #donasiModal .btn-custome:hover {
            background-color: rgb(9, 9, 97);
            color: white;
        }
        
        /* Program Card Styling */
        #donasiModal .program-card {
            height: auto;
            cursor: pointer;
            border: none;
            overflow: hidden;
            transition: transform 0.2s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        #donasiModal .program-card:hover {
            transform: scale(1.03);
        }
        
        /* Gambar di dalam Card */
        #donasiModal .program-card img {
            width: 100%;
            height: auto;
            object-fit: cover;
            display: block;
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
            /* Shadow for card */
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
            /* Title hidden by default */
            /* transition: opacity 0.1s ease; */
        }

        /* Show title on hover */
        #program-cards .program-card:hover .card-body {
            opacity: 1;
            /* Title becomes visible on hover */
        }

        /* Title styling */
        #program-cards .program-card .card-body h5 {
            margin: 0;
            font-size: 18px;
            /* Larger font size for the title */
            text-shadow: 1px 1px 3px white;
            /* Adding text shadow */
        }

        /* Hide description text */
        #program-cards .program-card .card-body p {
            display: none;
        }

        .selected-btn {
            background-color: white !important;
            color: black !important;
        }
        
        /* ==================== MOBILE VIEW ONLY ==================== */
        
        @media (max-width: 768px) {
            #donasiModal .modal-dialog {
                width: 100%;
                max-width: 100%;
                margin: 0;
                min-height: 50vh;      /* Agar cukup tinggi untuk konten */
                max-height: 100dvh;     /* Tidak melebihi layar */
                display: flex;
                flex-direction: column;
            }
        
            #donasiModal .modal-content {
                flex: 1 1 auto;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                border-radius: 0;
            }
        
            #donasiModal .modal-header,
            #donasiModal .modal-footer {
                flex-shrink: 0;
                padding: 1rem;
            }
        
            #donasiModal .modal-body {
                flex: 1 1 auto;
                overflow-y: auto;
                padding: 1rem;
            }
        
            #donasiModal .modal-body img {
                width: 100%;
                height: auto;
                object-fit: cover;
            }
        
            #donasiModal .btn,
            #donasiModal .form-control {
                font-size: 14px;
            }
        
            #donasiModal .modal-footer .btn {
                width: 100%;
                padding: 12px;
                font-size: 16px;
            }
        
            #donasiModal .row.row-cols-2 {
                row-gap: 10px;
            }
        
        }


       
    </style>
@endsection

@section('content')

    <!-- Modal Iklan (Muncul Saat Halaman Dimuat) -->
    <div class="modal fade" id="iklanModal" tabindex="-1" aria-labelledby="iklanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-body p-0 position-relative"> 
                    <!-- Carousel Bootstrap -->
                    <div id="carouselIklan" class="carousel slide" data-bs-ride="carousel">
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>z --}}
                        <div class="carousel-inner">
                            @foreach ($donasiiklan as $index => $item)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                   <img src="{{ Storage::url($item->file) }}" class="d-block w-100 img-fluid" alt="Iklan Donasi">
                                </div>
                            @endforeach
                        </div>
                        <!-- Tombol Navigasi Carousel -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselIklan"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselIklan"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>

               <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" 
                        id="openDonasiModal" style="position: relative;">
                        
                        @php
                            $firstIklan = $donasiiklan->first();
                            $ikon = $firstIklan->icon_iklan ?? 'fa-money-bill-wave';
                            $label = $firstIklan->name_button_iklan ?? 'Berbagi Sekarang';
                        @endphp
                        <span style="position: relative; display: inline-block; width: 1.5em; height: 1.3em;">
                            <i class="fas {{ $ikon }}" style="color: white; font-size: 1.2em;"></i>
                        </span>
                        {{ $label }}
                        
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
                    <h5 class="modal-title text-light" id="donasiModalLabel">Donasi Kilau Indonesia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Hi, apakah kamu ingin menjadi bagian dari kami untuk menebarkan manfaat?</p>
                    <!--<p><strong>тнР Ayo donasi sekarang тнР</strong></p>-->
                    
                     <p><strong>тнР Ayo Berbagi sekarang тнР</strong></p>
                    <form id="donasiForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Donatur</label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                placeholder="Masukkan Nama Donatur" required>
                        </div>

                        <!-- Jenis Donasi -->
                        <div class="mb-3">
                            <label class="form-label">Jenis Donasi</label><br>
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

                        <!-- Pilihan Program Donasi (Card) -->
                        <!--<div class="mb-3" id="program-cards" style="display: none;">-->
                        <!--    <label for="program-cards" class="form-label text-white">Program Kami</label>-->
                        <!--    <div class="row">-->
                        <!--        @foreach ($programs as $program)-->
                        <!--            <div class="col-3 mb-2">-->
                        <!--                <div class="card program-card" data-program="{{ $program->judul }}"-->
                        <!--                    data-program-id="{{ $program->id }}">-->
                        <!--                    <img src="{{ asset('storage/' . $program->foto_image[0]) }}"-->
                        <!--                        class="card-img-top" alt="Image">-->
                        <!--                    <div class="card-body">-->
                        <!--                        <h5 class="card-title" title="{{ $program->judul }}">-->
                        <!--                            {{ Str::limit($program->judul, 20) }}-->
                        <!--                        </h5>-->
                        <!--                    </div>-->
                        <!--                </div>-->
                        <!--            </div>-->
                        <!--        @endforeach-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<input type="hidden" id="programIdInput" name="id_program" value="">-->
                        
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
                            <label for="donasi" class="form-label">Pilih Jumlah Donasi</label>
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

    <!-- Hero Start -->
    <div class="container-fluid py-5 bg-primary hero-header" style="">
        <div class="container py-5">
            <!-- Carousel -->
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
                <!-- Indicators -->
                <div class="carousel-indicators" style="bottom: -90px !important;">
                    @foreach ($homeKilau as $index => $item)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}"
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
                                            background="transparent" speed="1" style="max-width: 100%; height: auto;"
                                            loop autoplay>
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

                <div class="row gx-0 gy-4 py-3">
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
    @endif


    <!-- @if ($testimoniMenu && $testimoniMenu->status == 'Aktif')-->
        <!-- Testimonial Start -->
    <!--    <div class="container-fluid bg-light py-5">-->
    <!--        <div class="container py-5">-->
    <!--            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 500px">-->
    <!--                <h1 class="mb-4">{{ $testimoniMenu->judul }}</h1>-->
    <!--                <p class="lead">{{ $testimoniMenu->subjudul }}</p>-->
    <!--            </div>-->

    <!--            <div class="row g-5 py-5">-->
                    <!-- Carousel Video Testimonial -->
    <!--                <div class="col-lg-12 wow fadeIn" data-wow-delay="0.5s">-->
    <!--                    <div class="owl-carousel testimonial-carousel border-start border-primary">-->
    <!--                        @if ($testimonis && $testimonis->count())-->
    <!--                            @foreach ($testimonis as $testimoni)-->
    <!--                                <div class="testimonial-item ps-5">-->
                                        <!-- Testimonial Info (Nama, Pekerjaan, Komentar) - Diletakkan di Atas -->
    <!--                                    <div class="text-start mb-4">-->
    <!--                                        <div class="d-flex justify-content-start align-items-center mb-3">-->
    <!--                                            <img class="img-fluid rounded-circle"-->
    <!--                                                src="{{ $testimoni->file ? Storage::url($testimoni->file) : asset('images/default.png') }}"-->
    <!--                                                style="width: 60px; height: 60px;" />-->
    <!--                                            <div class="ms-3">-->
    <!--                                                <h5 class="mb-1" style="text-align: left;">{{ $testimoni->nama }}-->
    <!--                                                </h5>-->
    <!--                                                <span style="text-align: left;">{{ $testimoni->pekerjaan }}</span>-->
    <!--                                            </div>-->
    <!--                                        </div>-->

    <!--                                        <div>-->
    <!--                                            <p class="fs-4">-->
    <!--                                                {{ strip_tags($testimoni->komentar) }}-->
    <!--                                            </p>-->
    <!--                                        </div>-->
    <!--                                    </div>-->


                                        <!-- Video Testimonial - Diletakkan di Bawah -->
    <!--                                    <div class="mb-4">-->
    <!--                                        {{-- <iframe class="embed-responsive-item" width="100%" height="300"-->
    <!--                                                src="https://www.youtube.com/embed/{{ $testimoni->video_url }}"-->
    <!--                                                title="YouTube video player" frameborder="0"-->
    <!--                                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"-->
    <!--                                                allowfullscreen>-->
    <!--                                        </iframe> --}}-->
    <!--                                        <iframe class="embed-responsive-item" width="100%" height="500"-->
    <!--                                            src="https://www.youtube.com/embed/xABmKcg817Y"-->
    <!--                                            title="YouTube video player" frameborder="0"-->
    <!--                                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"-->
    <!--                                            allowfullscreen>-->
    <!--                                        </iframe>-->

    <!--                                    </div>-->

                                        <!-- Tombol untuk Membuka Modal Komentar -->
    <!--                                    {{-- <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"-->
    <!--                                            data-bs-target="#commentModal">-->
    <!--                                        Beri Komentar-->
    <!--                                    </button> --}}-->
    <!--                                </div>-->
    <!--                            @endforeach-->
    <!--                        @else-->
    <!--                            <p class="text-center">Belum ada testimoni yang aktif.</p>-->
    <!--                        @endif-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
        <!-- Testimonial End -->
    <!--@endif-->

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
    

    



    <!--@if ($mitraMenu && $mitraMenu->status == 'Aktif')-->
        <!-- Mitra Donatur Start -->
    <!--    <div id="mitra-donatur" class="container-fluid bg-light py-5">-->
    <!--        <div class="container py-5">-->
                <!-- Judul Section -->
    <!--            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 500px">-->
    <!--                <h1 class="mb-4">{{ $mitraMenu->judul }}</h1>-->
    <!--                <p class="lead">{{ $mitraMenu->subjudul }}</p>-->
    <!--            </div>-->

                <!-- Baris Pertama -->
    <!--            <div class="row justify-content-center wow fadeIn py-5" data-wow-delay="0.3s" style="gap: 30px;">-->
    <!--                @if ($mitras && $mitras->count())-->
    <!--                    @foreach ($mitras->slice(0, 5) as $mitra)-->
    <!--                        <div class="col-lg-2 col-md-2 col-2 mb-4 text-center">-->
    <!--                            <img src="{{ $mitra->file ? Storage::url($mitra->file) : asset('images/default.png') }}"-->
    <!--                                class="img-fluid w-100" style="max-width: 200px;" alt="{{ $mitra->nama_mitra }}">-->
    <!--                        </div>-->
    <!--                    @endforeach-->
    <!--                @else-->
    <!--                    <p class="text-center">Belum ada mitra donatur yang aktif.</p>-->
    <!--                @endif-->
    <!--            </div>-->

                <!-- Baris Kedua -->
    <!--            <div class="row justify-content-center wow fadeIn" data-wow-delay="0.5s" style="gap: 30px;">-->
    <!--                @if ($mitras && $mitras->count() > 4)-->
    <!--                    @foreach ($mitras->slice(5, 5) as $mitra)-->
                            <!-- Ganti slice untuk 5 data berikutnya -->
    <!--                        <div class="col-lg-2 col-md-2 col-2 mb-4 text-center">-->
    <!--                            <img src="{{ $mitra->file ? Storage::url($mitra->file) : asset('images/default.png') }}"-->
    <!--                                class="img-fluid w-100" style="max-width: 200px;" alt="{{ $mitra->nama_mitra }}">-->
    <!--                        </div>-->
    <!--                    @endforeach-->
    <!--                @endif-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
        <!-- Mitra Donatur End -->
    <!--@endif-->
    
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
            <div class="row justify-content-center wow fadeIn py-5" data-wow-delay="0.3s" style="gap: 40px; flex-wrap: wrap;">
                @if ($mitras && $mitras->count())
                    @foreach ($mitras as $mitra)
                        <div class="col-lg-1 col-md-2 col-3 mb-4 text-center">
                            <img src="{{ $mitra->file ? Storage::url($mitra->file) : asset('images/default.png') }} "
                                class="img-fluid w-110" style="max-width: 110px;" alt="{{ $mitra->nama_mitra }}">
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
    $(document).ready(function () {
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
        $('#openDonasiModal').on('click', function (event) {
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
                $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' }); // Pastikan body bisa di-scroll
            }, 300);
        }
    
        // **Pastikan modal donasi tertutup dengan bersih**
        $('#donasiModal, #iklanModal').on('hidden.bs.modal', function () {
             isModalOpen = false;
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
        });
        
           $('#iklanModal').on('hidden.bs.modal', function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });

    
        // **Pastikan tidak ada modal yang tertinggal setelah refresh**
        window.addEventListener('beforeunload', function () {
            closeAllModals();
        });
    });


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
                              let cleanDescription = response.description.replace(/<[^>]*>/g, ''); 
                            $('#program-description').text(cleanDescription);
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
            $('#donasiForm').on('submit', function(e) {
                e.preventDefault(); // Mencegah form agar tidak submit secara normal

                var amount = $('#donasiForm').data('amount');
                var programId = $('#programIdInput').val();
                var typeDonasi = $('input[name="type_donasi"]:checked').val(); // Menentukan jenis donasi

                if (typeDonasi == 1 && !
                    programId) { // Jika jenis donasi adalah Program, dan id_program kosong
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Program Donasi Terlebih Dahulu',
                        text: 'Silakan pilih program donasi sebelum melanjutkan.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                    });
                    return;
                }

                // Ambil nilai opsional_umum jika ada checkbox yang dicentang
                var opsionalValue = $('#opsionalValueInput').val();

                $.ajax({
                    url: '{{ route('donasi.store') }}',
                    type: 'POST',
                    data: {
                        nama: $('#nama').val(),
                        type_donasi: programId ? 1 : 2,
                        total: amount,
                        id_program: programId,
                        opsional_umum: opsionalValue,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        var donasiId = response.donasi_id;
                        Swal.fire({
                            icon: 'success',
                            title: 'Donasi Berhasil Disimpan',
                            text: 'Silakan lanjutkan pembayaran.',
                            confirmButtonText: 'Lanjutkan',
                            confirmButtonColor: '#3085d6',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Kirim permintaan untuk mendapatkan token Midtrans
                                $.ajax({
                                    url: 'https://kilauindonesia.org/api/get_token_midtrans_sb',
                                    type: 'POST',
                                    data: {
                                        total: amount
                                    },
                                    success: function(response) {
                                        if (response.token) {
                                            snap.pay(response.token, {
                                                onSuccess: function(
                                                    result) {
                                                    Swal.fire(
                                                        'Terima kasih! ЁЯд▓',
                                                        'Terima kasih sudah berbagi donasi. Semoga berkah dan bermanfaat! ЁЯШК',
                                                        'success'
                                                    );
                                                    donasiModal
                                                        .hide();

                                                    $.ajax({
                                                        url: '/donasi/' +
                                                            donasiId +
                                                            '/update-status',
                                                        type: 'POST',
                                                        data: {
                                                            donasi_id: donasiId, // Kirim donasi_id sebagai parameter
                                                            status: 2, // Status aktif
                                                            _token: '{{ csrf_token() }}' // CSRF token
                                                        },
                                                        success: function() {
                                                            console
                                                                .log(
                                                                    'Status donasi berhasil diupdate'
                                                                );
                                                        },
                                                        error: function() {
                                                            console
                                                                .log(
                                                                    'Gagal update status donasi'
                                                                );
                                                        }
                                                    });

                                                },
                                                onError: function(
                                                    result) {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Pembayaran Gagal',
                                                        text: 'Terjadi kesalahan saat pembayaran.',
                                                        confirmButtonText: 'Coba Lagi',
                                                        confirmButtonColor: '#d33',
                                                    });
                                                },
                                                onPending: function(
                                                    result) {
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
                                    },
                                    error: function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Terjadi Kesalahan',
                                            text: 'Gagal mendapatkan token Midtrans.',
                                            confirmButtonText: 'Coba Lagi',
                                            confirmButtonColor: '#d33',
                                        });
                                    }
                                });
                            }
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan Data Donasi',
                            text: 'Terjadi kesalahan saat menyimpan data donasi.',
                            confirmButtonText: 'Coba Lagi',
                            confirmButtonColor: '#d33',
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

            function formatTanggal(dateString) {
                let options = {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                };
                return new Date(dateString).toLocaleDateString("id-ID", options);
            }

            function generatePagination(pagination) {
                let paginationHtml = '';
                let maxVisiblePages = 5;

                if (pagination.current_page > 1) {
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="1">First</a></li>`;
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page - 1}">┬л</a></li>`;
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
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page + 1}">┬╗</a></li>`;
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.last_page}">Last</a></li>`;
                }

                $('#pagination').html(paginationHtml);
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
