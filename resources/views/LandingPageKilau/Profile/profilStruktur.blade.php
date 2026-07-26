@extends('App.master')

@section('style')
    <style>
        /* Tambahkan CSS khusus jika diperlukan */
        .struktur-img {
            width: 100%; /* Mengatur gambar agar memenuhi lebar kontainer */
            height: auto;
            border-radius: 10px;
            margin-top: 20px;
            object-fit: contain; /* Menyesuaikan gambar agar tidak terpotong dan tetap proporsional */
            object-position: center;
        }

        .struktur-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }

        .text-section {
            text-align: center;
            /* Pusatkan teks */
        }

        .image-section {
            text-align: center;
            /* Pusatkan gambar */
        }

        .image-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden; /* Untuk menghindari gambar keluar dari batas kontainer */
            height: auto; /* Mengatur tinggi otomatis sesuai gambar */
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
                    <h1 class="display-4 text-white mb-4">Struktur Yayasan</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Profil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Struktur Organisasi</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- Struktur Organisasi Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row g-5">
                <!-- Teks Penjelasan -->
                <div class="col-12 text-section wow fadeIn" data-wow-delay="0.1s">
                    <div class="btn btn-sm border rounded-pill text-primary px-3 mb-3">Struktur Yayasan</div>
                    <h1 class="mb-4">{{ $strukturMenu->judul }}</h1>
                    <p class="struktur-description mb-4">
                        {{ $strukturMenu->subjudul }}
                    </p>
                    <p class="struktur-description mb-4">
                        {{ $strukturs->first() ? $strukturs->first()->name_judul : 'Struktur tidak tersedia' }}
                    </p>
                </div>

                <!-- Gambar Struktur Organisasi -->
                <div class="col-12 image-section wow fadeIn" data-wow-delay="0.3s">
                    <div class="image-wrapper">
                        <!-- Periksa apakah ada data struktur dan file gambar tersedia -->
                        @if ($strukturs->isNotEmpty() && $strukturs->first()->file)
                            <img class="struktur-img img-fluid" src="{{ Storage::url($strukturs->first()->file) }}" alt="Struktur Organisasi">
                        @else
                            <p>Gambar Tidak Tersedia</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Struktur Organisasi End -->
@endsection

@section('scripts')
    <script>
        // Tambahkan script khusus jika diperlukan
    </script>
@endsection
