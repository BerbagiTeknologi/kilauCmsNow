@extends('App.master')

@section('style')
 @foreach ($dokuments as $document)
    <meta property="og:title" content="{{ $document->text_document ?? 'Dokumen' }}" />
    <meta property="og:description" content="{{ strip_tags($document->subjudul ?? 'Dokumen yang berisi informasi penting') }}" />
    <meta property="og:image" content="{{ Storage::url($document->image ?? '/assets/img/default-image.jpg') }}" />
    <meta property="og:url" content="{{ url()->current() . '?id=' . $document->id . '&fullscreen=true' }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Nama Website" />
@endforeach


    <style>
        .document-section {
            padding: 60px 0;
        }

        .document-info {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .document-info h3 {
            color: #1363c6;
            margin-bottom: 20px;
        }

        .document-info p {
            font-size: 1rem;
            color: #333;
            margin-bottom: 10px;
        }

        .text-section {
            text-align: center;
        }

        .btn-dokumen {
            background-color: #1363c6;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-dokumen:hover {
            background-color: #0e4a9e;
        }

        /* Pusatkan jika hanya ada 1 dokumen */
        .document-wrapper {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .document-container {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* Agar konten dimulai dari atas */
        }
        
        .flipbook {
            width: 350px;        /* Lebar flipbook */
            height: 450px;       /* Tinggi flipbook yang lebih tinggi untuk memaksimalkan ruang */
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* Agar konten berada di atas */
        }
        
        ._df_book {
            width: 150% !important;
            margin-left: -87px !important;
            height: 150% !important;
        }


    </style>

    <!-- Flipbook StyleSheet -->
    <link href="{{ asset('assets_flipbox/css/dflip.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets_flipbox/css/themify-icons.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <!-- Hero Start -->
    <div class="container-fluid pt-5 bg-primary hero-header">
        <div class="container pt-5">
            <div class="row g-5 pt-5">
                <div class="col-12 text-center" style="margin-top: 100px !important;">
                    <h1 class="display-4 text-white mb-4">Dokumen Kami</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Profil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Dokumen</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

   <!-- Dokumen Section Start -->
   <div class="container-fluid py-5 document-section">
        <div class="container">
            <!-- Informasi Dokumen -->
            <div class="row g-5">
                <div class="col-12 text-section wow fadeIn" data-wow-delay="0.1s">
                    <div class="btn btn-sm border rounded-pill text-primary px-3 mb-3">Dokumen</div>
                    <h1 class="mb-4">{{ $dokumentMenu->judul }}</h1>
                    <p class="mb-4">{{ $dokumentMenu->subjudul }}</p>
                </div>
            </div>

            <!-- Daftar Dokumen -->
            @if (count($dokuments) > 0)
                <div class="document-wrapper">
                    @foreach ($dokuments as $document)
                        <div class="document-container">
                            <h5>{!! $document->text_document !!}</h5>
                            @if ($document->files)
                                @php
                                    $files = json_decode($document->files);
                                @endphp

                                @foreach ($files as $file)
                                    @php
                                        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                    @endphp

                                      @if ($fileExtension == 'pdf' || in_array($fileExtension, ['png', 'jpg', 'jpeg', 'gif']))
                                            <div class="flipbook">
                                                <div class="_df_book" data-id="{{ $document->id }}" height="500" width="500" webgl="true"
                                                    source="{{ Storage::url($file) }}">
                                                </div>
                                            </div>
                                        @else
                                        <a href="{{ Storage::url($file) }}" target="_blank">
                                            Lihat File
                                        </a>
                                    @endif

                                    <br> 
                                @endforeach
                            @else
                                <p>No File</p>
                            @endif
                            
                            
                        </div>
                    @endforeach
                </div>
            @else
                <div class="col-12 text-center">
                    <p>Tidak ada dokumen yang tersedia.</p>
                </div>
            @endif
        </div>
    </div>
    <!-- Dokumen Section End -->
@endsection

@section('scripts')
<!-- jQuery -->
<script src="{{ asset('assets_flipbox/js/libs/jquery.min.js') }}" type="text/javascript"></script>
<!-- Flipbook main Js file -->
<script src="{{ asset('assets_flipbox/js/dflip.min.js') }}" type="text/javascript"></script>

<script>
  $(document).ready(function() {
    // Get the parameters from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const documentId = urlParams.get('id'); // Get document ID
    const fullscreen = urlParams.get('fullscreen'); // Get fullscreen parameter
    
    if (documentId) {
        // Find the correct flipbook element based on the document ID
        const flipbook = $('._df_book[data-id="' + documentId + '"]').dFlip({
            height: 400,
            webgl: true,
            fullscreen: false, // Do not open fullscreen by default
        });

        // If the fullscreen parameter is true, trigger fullscreen after the flipbook is loaded
        if (fullscreen === 'true') {
            setTimeout(function() {
                flipbook.dFlip('toggleFullscreen'); // Enable fullscreen
            }, 500); // Wait for the flipbook to fully load before toggling fullscreen
        }
    }
});



    
    // function shareDocument(url) {
    //     // Menyalin link share ke clipboard
    //     navigator.clipboard.writeText(url).then(function() {
    //         alert("Link share telah disalin!");
    //     }, function() {
    //         alert("Gagal menyalin link!");
    //     });
    // }
</script>
@endsection
