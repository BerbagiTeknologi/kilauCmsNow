@extends('App.master')

@section('style')
    <style>
        .sejarah-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            text-align: justify;
        }

        .text-section {
            text-align: center;
        }

        .btn-sejarah {
            background-color: #1363c6;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-sejarah:hover {
            background-color: #0e4a9e;
        }

        .visi-misi-list {
            list-style: none;
            /* Hilangkan default bullet point */
            padding-left: 0;
            /* Hilangkan padding kiri default */
        }

        .visi-misi-list li {
            position: relative;
            padding-left: 25px;
            /* Beri ruang untuk ikon atau custom bullet */
            margin-bottom: 10px;
            /* Beri jarak antar misi */
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .about-img {
                width: 100%;
                height: auto;
            }

            .content {
                text-align: center;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Hero Start -->
    <div class="container-fluid pt-5 bg-primary hero-header">
        <div class="container pt-5">
            <div class="row g-5 pt-5">
                <div class="col-12 text-center" style="margin-top: 100px !important;">
                    <h1 class="display-4 text-white mb-4">Tentang Kami</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Tentang Kami</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    {{-- About Section Start --}}
    @if ($tentangMenu && $tentangMenu->status == 'Aktif')
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <div class="tooltip-container">
                            <h1 class="display-5 fw-bold mb-2 hover-text">{{ $tentangMenu->judul }}</h1>
                        </div>
                        <p class="lead mb-4">
                            {{ $tentangMenu->subjudul }}
                        </p>
                    </div>
                </div>

                <div class="row gx-4 gy-4 py-3">
                    @foreach ($tentangs as $tentang)
                        <!-- Bagian Teks -->
                        <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up"
                            data-aos-delay="200">
                            <div class="content">
                                <h2 class="fw-bold">{{ $tentang->judul_tentang_kami }}</h2>
                                <p>{{ $tentang->deskripsi }}</p>
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
    {{-- About Section End --}}

    <!-- Sejarah Yayasan Start -->
    @if ($sejarahMenu && $sejarahMenu->status == 'Aktif')
        <div class="container-fluid py-2">
            <div class="container py-2">
                <div class="row g-5">
                    <div class="col-12 text-section wow fadeIn" data-wow-delay="0.1s">
                        {{-- <div class="btn btn-sm border rounded-pill text-primary px-3 mb-3">Sejarah Yayasan</div> --}}
                        <h1 class="mb-4">{{ $sejarahMenu->judul }}</h1>
                        <p class="sejarah-description mb-4 text-center">
                            {{ $sejarahMenu->subjudul }}
                        </p>
                        <p class="sejarah-description mb-4">
                            {{ $sejarahs->first() ? strip_tags($sejarahs->first()->deskripsi_sejarah) : 'Sejarah Tidak Tersedia' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Sejarah Yayasan End -->

    <!-- Visi dan Misi Start -->
    @if ($visimisiMenu && $visimisiMenu->status == 'Aktif')
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-12 text-section wow fadeIn" data-wow-delay="0.1s">
                        {{-- <div class="btn btn-sm border rounded-pill text-primary px-3 mb-3">Visi Dan Misi</div> --}}
                        <h1 class="mb-4">{{ $visimisiMenu->judul }}</h1>
                        <p class="sejarah-description mb-4 text-center">
                            {{ $visimisiMenu->subjudul }}
                        </p>

                        <h2 class="mt-5 mb-3">Visi</h2>
                        <p class="visi-misi-description mb-4">
                            {{ isset($visimisis) && $visimisis->isNotEmpty() ? strip_tags($visimisis->first()->visi) : 'Visi tidak tersedia' }}
                        </p>

                        <h2 class="mt-5 mb-3">Misi</h2>
                        <ul class="visi-misi-list">
                            @if (isset($visimisis) && $visimisis->isNotEmpty() && !empty($visimisis->first()->misi))
                                @foreach (explode("\n", preg_replace('/\.\.\./', '', strip_tags($visimisis->first()->misi))) as $misi)
                                    @if (!empty(trim($misi)))
                                        {{-- Cek apakah misi tidak kosong --}}
                                        <li class="visi-misi-description">{{ trim($misi) }}</li>
                                    @endif
                                @endforeach
                            @else
                                <li class="visi-misi-description">Misi tidak tersedia</li>
                            @endif
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        AOS.init();
    </script>
@endsection
