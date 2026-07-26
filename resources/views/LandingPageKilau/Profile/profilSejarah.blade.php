@extends('App.master')

@section('style')
    <style>
        /* Tambahkan CSS khusus jika diperlukan */
        .sejarah-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            text-align: justify; /* Teks rata kiri-kanan */
        }

        .text-section {
            text-align: center; /* Pusatkan teks judul */
        }

        .btn-sejarah {
            background-color: #1363c6; /* Warna tombol */
            color: #fff; /* Warna teks tombol */
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-sejarah:hover {
            background-color: #0e4a9e; /* Warna tombol saat hover */
        }
    </style>
@endsection

@section('content')
    <!-- Hero Start -->
    <div class="container-fluid pt-5 bg-primary hero-header">
        <div class="container pt-5">
            <div class="row g-5 pt-5">
                <!-- Kolom untuk Teks dan Breadcrumb -->
                <div class="col-12 text-center" style="margin-top: 100px !important;">
                    <h1 class="display-4 text-white mb-4">Sejarah Yayasan</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Profil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Sejarah Yayasan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- Sejarah Yayasan Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row g-5">
                <!-- Teks Penjelasan Sejarah -->
                <div class="col-12 text-section wow fadeIn" data-wow-delay="0.1s">
                    <div class="btn btn-sm border rounded-pill text-primary px-3 mb-3">Sejarah Yayasan</div>
                    <h1 class="mb-4">{{ $sejarahMenu->judul }}</h1>
                    <p class="sejarah-description mb-4 text-center">
                        {{ $sejarahMenu->subjudul }}
                    </p>
                     <p class="sejarah-description mb-4">
                        {{ $sejarahs->first() ? strip_tags(str_replace('&nbsp;', ' ', $sejarahs->first()->deskripsi_sejarah)) : 'Sejarah Tidak Tersedia' }}
                    </p>
                 
                </div>
            </div>
        </div>
    </div>
    <!-- Sejarah Yayasan End -->
@endsection

@section('scripts')
    <script>
        // Tambahkan script khusus jika diperlukan
    </script>
@endsection