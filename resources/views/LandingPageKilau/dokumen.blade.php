@extends('App.master')

@section('style')
       @php $shareDoc = $dokuments->first(); @endphp

    <meta property="og:title"
          content="{{ strip_tags($shareDoc->text_document) ?: 'Dokumen' }}">

    <meta property="og:description"
          content="{{ isset($dokumentMenu->subjudul) ? strip_tags($dokumentMenu->subjudul) : 'Dokumen' }}">

    <meta property="og:image"
          content="{{ $shareDoc->thumbnail
                        ? Storage::url($shareDoc->thumbnail)
                        : asset('assets/img/default-image.jpg') }}">

    <meta property="og:url"
          content="{{ url()->current() }}?id={{ $shareDoc->id }}&fullscreen=true">

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
            justify-content: flex-start;
            /* Agar konten dimulai dari atas */
        }

        .flipbook {
            width: 350px;
            /* Lebar flipbook */
            height: 450px;
            /* Tinggi flipbook yang lebih tinggi untuk memaksimalkan ruang */
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            /* Agar konten berada di atas */
        }

        ._df_book {
            width: 150% !important;
            margin-left: -87px !important;
            height: 150% !important;
        }

        .doc-track {
            display: flex;
            /*  <-- bikin baris */
            flex-wrap: nowrap;
            /* baris tunggal, tak pindah ke bawah */
            gap: 30px;
            overflow-x: auto;
            /* bisa di-scroll */
            padding-block: 8px;
            scroll-behavior: smooth;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .doc-track::-webkit-scrollbar {
            display: none;
        }

        .doc-card {
            flex: 0 0 350px;
            /* lebar fix 350, tak menyusut */
            scroll-snap-align: start;
            text-align: center;
        }

        /* ------------------- panah ------------------- */
        .doc-arrow {
            position: absolute;
            top: 110%;
            /* tepat di bawah trek */
            width: 40px;
            height: 40px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: none;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .25);
            z-index: 10;
            transform: translate(-130%, -50%);
            /* ▼ sama untuk keduanya */
        }

        .doc-arrow-left {
            left: 10;
        }

        /* center-X pada titik (0,100%) */
        .doc-arrow-right {
            right: 0;
        }


        .doc-track.single {
            justify-content: center;
            /* kartu tepat di tengah */
            overflow-x: hidden;
            /* tak perlu scroll */
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets_flipbox/css/dflip.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_flipbox/css/themify-icons.min.css') }}">
@endsection

@section('content')
    <!-- ========== HERO ========== -->
    <div class="container-fluid pt-5 bg-primary hero-header">
        <div class="container pt-5">
            <div class="row g-5 pt-5">
                <div class="col-12 text-center" style="margin-top:100px!important;">
                    <h1 class="display-4 text-white mb-4">Dokumen Kami</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Dokumen</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

   @php $single = $dokuments->count() === 1; @endphp
{{-- =======================  SECTION  ======================= --}}
<div class="container-fluid py-5 document-section">
    <div class="container">
        <div class="row text-center mb-4">
            <h2 class="mb-1">{{ $dokumentMenu->judul }}</h2>
            <p>{{ $dokumentMenu->subjudul }}</p>
        </div>

        @if ($dokuments->count())
            <div class="doc-wrapper">
                <button class="doc-arrow doc-arrow-left {{ $single ? 'd-none' : '' }}" id="docLeft">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <div class="doc-track {{ $single ? 'single' : '' }}" id="docTrack">
                    @foreach ($dokuments as $document)
                        <div class="doc-card position-relative">

                            {{-- -------- KONTEN -------- --}}
                            <h6 class="mb-2">{!! $document->text_document !!}</h6>

                            @php $files = $document->files ? json_decode($document->files) : []; @endphp
                            @forelse($files as $file)
                                @php $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); @endphp

                                @if (in_array($ext, ['pdf', 'png', 'jpg', 'jpeg', 'gif']))
                                    <div class="flipbook mb-3">
                                        <div class="_df_book" data-id="{{ $document->id }}"
                                             source="{{ Storage::url($file) }}" webgl="true"></div>
                                    </div>
                                @else
                                    <a href="{{ Storage::url($file) }}" target="_blank">Lihat File</a>
                                @endif
                            @empty
                                <p>No File</p>
                            @endforelse

                            {{-- -------- TOMBOL SHARE -------- --}}
                            <button class="btn btn-sm btn-secondary text-white btn-share mt-2"
                                     data-link="{{ route('dokumen.share', $document->slug) }}">
                                <i class="fas fa-share-alt"></i> Share
                            </button>

                        </div>
                    @endforeach
                </div>

                <button class="doc-arrow doc-arrow-right {{ $single ? 'd-none' : '' }}" id="docRight">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            {{-- pagination hanya untuk listing --}}
            @if (is_null($singleId))
                <nav>
                    <ul class="pagination justify-content-center">{{ $dokuments->links() }}</ul>
                </nav>
            @endif
        @else
            <p class="text-center">Tidak ada dokumen yang tersedia.</p>
        @endif
    </div>
</div>

{{-- ===== Modal Info (petunjuk cepat) ===== --}}
<div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-body d-flex flex-column align-items-center text-center py-5">

                {{-- ikon pembesar --}}
                <div class="display-4 text-primary mb-3">
                    <i class="fas fa-expand-arrows-alt"></i>
                </div>

                {{-- pesan --}}
                <p class="mb-4 px-3">
                    Klik ikon <b>fullscreen</b> di pojok <b>flipbook</b> untuk membaca dengan ukuran penuh.
                </p>

                {{-- tombol tutup --}}
                <button class="btn btn-primary px-4" data-bs-dismiss="modal">Mengerti</button>
            </div>

        </div>
    </div>
</div>

@endsection


@section('scripts')
<script src="{{ asset('assets_flipbox/js/libs/jquery.min.js') }}"></script>
<script src="{{ asset('assets_flipbox/js/dflip.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ==== panah scroll (tidak berubah) ==== */
    const track = document.getElementById('docTrack');
    const CARD  = 380;
    document.getElementById('docLeft') ?.addEventListener('click', () => track.scrollLeft -= CARD);
    document.getElementById('docRight')?.addEventListener('click', () => track.scrollLeft += CARD);

    /* ==== share (tidak berubah) ==== */
    document.querySelectorAll('.btn-share').forEach(btn=>{
        btn.addEventListener('click', e=>{
            e.stopPropagation();
            const url = btn.dataset.link;
            if (navigator.share) navigator.share({title:'Dokumen',url}).catch(()=>{});
            else {
                navigator.clipboard?.writeText(url);
                window.open(url,'_blank');
                alert('Link disalin.');
            }
        });
    });

    /* ==== tentukan docId ==== */
    const qs        = new URLSearchParams(location.search);
    let   docId     = qs.get('id');
    const sharePage = !docId;                          // /share/slug
    if (!docId) docId = document.querySelector('._df_book')?.dataset.id;
    if (!docId) return;

    const flipEl = document.querySelector(`._df_book[data-id="${docId}"]`);
    if (!flipEl) return;

    /* ==== inisialisasi DearFlip ==== */
    let flipInstance = null;
    if (window.jQuery?.fn?.dFlip) {
        window.jQuery(flipEl)
            .on('df-ready', (_, inst) => { flipInstance = inst; })   // simpan instance ketika siap
            .dFlip({
                height      : 400,
                webgl       : true,
                fullscreen  : true   // WAJIB true agar toggle berfungsi
            });
    }

    /* ==== tampilkan modal ==== */
    const modalEl = document.getElementById('infoModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop:'static' });
        modal.show();

        /* klik “Mengerti” → fullscreen */
        modalEl.querySelector('[data-bs-dismiss="modal"]')?.addEventListener('click', () => {
            flipInstance?.toggleFullscreen();
        });
    }

});
</script>
@endsection
