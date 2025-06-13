@extends('App.master')

@section('style')
    <style>
        /* Styling Galeri */
        .gallery-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            cursor: pointer;
            width: 300px;
            height: 200px;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease-in-out;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-caption {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .gallery-item:hover .gallery-caption {
            opacity: 1;
        }
    </style>
@endsection

@section('content')
    <!-- Hero Start -->
    <div class="container-fluid pt-5 bg-primary hero-header">
        <div class="container pt-5">
            <div class="row g-5 pt-5">
                <div class="col-12 text-center" style="margin-top: 100px !important;">
                    <h1 class="display-4 text-white mb-4">Galeri Kami</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Profil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Galeri Kami</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- Galeri Start -->
    <div class="container py-5">
        <div class="row text-center">
            <h2 class="mb-1">Galeri Kegiatan</h2>
            <p>Lihat berbagai kegiatan yang telah kami lakukan.</p>
        </div>

        <!-- Filter Cabang -->
        <div class="container py-1 text-center">
            <label for="filterCabang" class="form-label"><strong>Pilih Cabang:</strong></label>
            <select id="filterCabang" class="form-select w-auto d-inline-block">
                <option value="all">Semua Cabang</option>
                @foreach ($galeri->unique('nama_kantor_cabang') as $cabang)
                    <option value="{{ $cabang->nama_kantor_cabang }}">{{ $cabang->nama_kantor_cabang }}</option>
                @endforeach
            </select>
        </div>

        <!-- Gallery Items -->
        <div class="gallery-container py-4" id="gallery">
            @include('LandingPageKilau.galeri-items', ['galeri' => $galeri])
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center" id="pagination">
                {{ $galeri->links() }}
            </ul>
        </nav>
    </div>
    <!-- Galeri End -->

    <!-- Modal Start -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Bootstrap Carousel -->
                    <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" id="modalGalleryImages"></div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                    <p class="mt-3 text-center" id="modalDescription"></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal End -->

@endsection

@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Filter berdasarkan kantor cabang
            $("#filterCabang").on("change", function() {
                var cabangTerpilih = $(this).val();
                $(".gallery-item").each(function() {
                    if (cabangTerpilih === "all" || $(this).data("cabang") === cabangTerpilih) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Event klik untuk menampilkan modal dengan semua gambar kegiatan terkait
            function initGalleryItems() {
                $(".gallery-item").click(function() {
                    var title = $(this).data("title");
                    var images = $(this).data("images");
                    var description = $(this).data("description");

                    $("#modalTitle").text(title);
                    $("#modalDescription").text(description);

                    // Load images into carousel
                    var imageHtml = "";
                    images.forEach((img, index) => {
                        imageHtml += `
                            <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                <img src="${img}" class="d-block w-100" alt="${title}">
                            </div>`;
                    });

                    $("#modalGalleryImages").html(imageHtml);
                    $("#galleryModal").modal("show");
                });
            }

            initGalleryItems();
        });
    </script>
@endsection
