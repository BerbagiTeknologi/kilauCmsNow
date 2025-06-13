@extends('App.master')

@section('style')
    <style>
        /* Blog Section */
        .blog-section {
            padding: 60px 0;
        }

        .blog-details {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .blog-details .post-img img {
            border-radius: 10px;
            width: 100%;
            height: auto;
        }

        .blog-details .title {
            color: #1363c6;
            margin-top: 20px;
            margin-bottom: 15px;
        }

        .blog-details .meta-top {
            margin-bottom: 20px;
        }

        .blog-details .meta-top ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .blog-details .meta-top ul li {
            display: inline-block;
            margin-right: 15px;
            color: #666;
            font-size: 0.9rem;
        }

        .blog-details .meta-top ul li i {
            margin-right: 5px;
            color: #1363c6;
        }

        .blog-details .content p {
            font-size: 1rem;
            line-height: 1.8;
            color: #333;
            text-align: justify;
        }

        /* Styling untuk gambar dalam konten */
        .content img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        /* Gambar yang memiliki class rata kiri */
        .content .ql-align-left img {
            display: block;
            margin-left: 0;
            margin-right: auto;
        }

        /* Gambar yang memiliki class rata tengah */
        .content .ql-align-center img {
            display: block;
            margin: 15px auto;
            text-align: center;
        }

        /* Gambar yang memiliki class rata kanan */
        .content .ql-align-right img {
            display: block;
            margin-left: auto;
            margin-right: 0;
        }

        /* Recent News */
        .recent-berita {
            margin-top: 20px;
        }

        .recent-berita .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .recent-berita .card img {
            border-radius: 10px 10px 0 0;
        }

        .recent-berita .card-body {
            padding: 15px;
        }

        .recent-berita .card-title {
            font-size: 1rem;
            color: #1363c6;
            margin-bottom: 10px;
        }

        .recent-berita .card-text {
            font-size: 0.9rem;
            color: #666;
        }

        .load-more-btn {
            border-radius: 20px;
            background-color: #1363c6;
            border: none;
            padding: 10px 20px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .load-more-btn:hover {
            background-color: #0e4a9e;
        }

        .meta-top {
            padding: 8px 0;
            font-size: 0.9rem;
            display: flex;
            justify-content: space-between;
            /* Pisahkan elemen kiri dan kanan */
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            /* Berikan sedikit jarak antar elemen */
        }

        .meta-top i {
            cursor: pointer;
            font-size: 1rem;
        }

        /* Atur jarak ikon dan teks agar tidak terlalu jauh */
        .meta-top .d-flex {
            gap: 5px;
            /* Mengurangi jarak antara ikon dan teks */
        }

        /* Atur margin lebih kecil untuk ikon */
        .meta-top .d-flex i {
            margin-right: 4px;
            /* Ubah dari 'me-1' ke nilai lebih kecil */
        }

        /* Like, Komentar, Share tetap di kanan */
        .meta-right {
            margin-left: auto;
            display: flex;
            gap: 12px;
            /* Berikan jarak lebih proporsional antar ikon */
            align-items: center;
        }

        .kategori-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .kategori-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0e4a9e;
            color: white;
            font-weight: bold;
            width: 150px;
            /* Tetapkan lebar yang sama */
            height: 50px;
            /* Tetapkan tinggi yang sama */
            border-radius: 25px;
            /* Tetap rounded */
            text-decoration: none;
            transition: all 0.3s ease-in-out;
            text-align: center;
            white-space: nowrap;
            /* Mencegah teks pecah */
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .kategori-badge:hover {
            background-color: #0c3d7a;
            transform: translateY(-3px);
            color: white;
        }

        .scroll-wrapper {
            overflow: hidden;
            width: 100%;
            padding: 10px 0;
            position: relative;
        }

        .kategori-container {
            display: inline-flex;
            flex-wrap: nowrap;
            /* Penting: jangan biarkan wrap! */
            white-space: nowrap;
            gap: 15px;
            animation: scrollYoyo 2s ease-in-out infinite alternate;
        }

        @keyframes scrollYoyo {
            0% {
                transform: translateX(10%);
            }

            100% {
                transform: translateX(-10%);
            }
        }

        .kategori-badge {
            flex: 0 0 auto;
            /* Prevent them from shrinking or growing */
            width: 140px;
            height: 50px;
            background-color: #0e4a9e;
            color: white;
            font-weight: bold;
            border-radius: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.3s ease-in-out;
        }
    </style>
@endsection

@section('content')
    <!-- Hero Start -->
    <div class="container-fluid pt-5 bg-primary hero-header">
        <div class="container pt-5">
            <div class="row g-5 pt-5">
                <div class="col-12 text-center" style="margin-top: 100px !important;">
                    <h1 class="display-4 text-white mb-4" id="judul-berita">Berita Kami</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white" href="/">Beranda</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Berita</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- Blog Section Start -->
    <div class="container-fluid py-5 blog-section">
        <div class="container py-5">
            <div class="row g-5">

                {{-- <div class="container text-center mt-4">
                    <h3 class="text-primary fw-bold mb-4">KATEGORI BERITA</h3>
                    <div id="kategori-container" class="kategori-container">
                    </div>
                </div> --}}

                <div class="container text-center mt-2">
                    <h3 class=" fw-bold mb-4" style="color: #0e4a9e;">KATEGORI BERITA</h3>
                    <div class="scroll-wrapper">
                        <div id="kategori-container" class="kategori-container">
                            <!-- Kategori akan dimuat lewat AJAX -->
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <section id="blog-details" class="blog-details section">
                        <article class="article">
                            <!-- Carousel Gambar Berita -->
                            @if (isset($berita['foto']) || isset($berita['foto2']) || isset($berita['foto3']))
                                <div class="post-img">
                                    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                                        <!-- Indikator -->
                                        <div class="carousel-indicators">
                                            @if (isset($berita['foto']))
                                                <button type="button" data-bs-target="#carouselExampleIndicators"
                                                    data-bs-slide-to="0" class="active"></button>
                                            @endif
                                            @if (isset($berita['foto2']))
                                                <button type="button" data-bs-target="#carouselExampleIndicators"
                                                    data-bs-slide-to="1"></button>
                                            @endif
                                            @if (isset($berita['foto3']))
                                                <button type="button" data-bs-target="#carouselExampleIndicators"
                                                    data-bs-slide-to="2"></button>
                                            @endif
                                        </div>

                                        <!-- Gambar Carousel -->
                                        <div class="carousel-inner">
                                            @php $active = 'active'; @endphp
                                            @if (isset($berita['foto']))
                                                <div class="carousel-item {{ $active }}">
                                                    <img src="https://berbagipendidikan.org{{ $berita['foto'] }}"
                                                        class="d-block w-100">
                                                </div>
                                                @php $active = ''; @endphp
                                            @endif
                                            @if (isset($berita['foto2']))
                                                <div class="carousel-item {{ $active }}">
                                                    <img src="https://berbagipendidikan.org{{ $berita['foto2'] }}"
                                                        class="d-block w-100">
                                                </div>
                                                @php $active = ''; @endphp
                                            @endif
                                            @if (isset($berita['foto3']))
                                                <div class="carousel-item {{ $active }}">
                                                    <img src="https://berbagipendidikan.org{{ $berita['foto3'] }}"
                                                        class="d-block w-100">
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Tombol Navigasi -->
                                        <button class="carousel-control-prev" type="button"
                                            data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button"
                                            data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- End Carousel -->

                            <h2 class="title">{{ $berita['judul'] ?? 'Null' }}</h2>

                            <div class="d-flex align-items-center" style="margin-bottom: -5px;">
                                <h6 class="text-primary fw-bold mb-0">BeritaKilau</h6>
                                <i class="fas fa-check-circle text-primary ms-1"></i>
                            </div>

                            <div class="meta-top">
                                <!-- Kiri: Tanggal & Waktu -->
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                    <time>{{ \Carbon\Carbon::parse($berita['tanggal'] ?? 'now')->translatedFormat('d F Y H:i') }}
                                        WIB</time>
                                </div>

                                <!-- Tengah: Jumlah Views -->
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-eye text-secondary"></i>
                                    <span class="fw-bold">{{ number_format($berita['views_berita'] ?? 0) }}</span>
                                    <span class="text-muted">x dilihat</span>
                                </div>

                                <!-- Kanan: Like, Komentar, Share -->
                                <div class="meta-right">
                                    <!-- Like (Love) -->
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-thumbs-up like-button text-secondary"
                                            data-id="{{ $berita['id'] }}"></i>
                                        <span id="like-count">{{ $berita['likes_berita'] ?? 0 }}</span>
                                    </div>

                                    <!-- Komentar -->
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-comment text-secondary"></i>
                                        <span id="comment-count">0</span>
                                    </div>

                                    <!-- Share -->
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle p-1"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Bagikan
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item share-wa" href="#">
                                                    <i class="fab fa-whatsapp text-success"></i> WhatsApp
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item share-fb" href="#">
                                                    <i class="fab fa-facebook text-primary"></i> Facebook
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item copy-link" href="#">
                                                    <i class="fas fa-link"></i> Salin Link
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            @if (isset($berita['kategori']) && isset($berita['kategori']['name_kategori']))
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-folder-open text-primary me-2"></i>
                                    <a href="/berita?kategori={{ $berita['kategori']['id'] }}"
                                        class="badge bg-primary text-white px-3 py-2 text-decoration-none">
                                        {{ $berita['kategori']['name_kategori'] }}
                                    </a>
                                </div>
                            @endif

                            <div class="content py-3" id="konten-detail">
                                {!! $berita['konten'] ?? 'Null' !!}
                            </div>
                        </article>
                    </section>
                </div>

                <div class="col-lg-4">
                    <div class="recent-berita">
                        <h4>Berita Terkini</h4>
                        <div id="recent-berita-container">
                            <!-- Berita terkini akan dimuat lewat AJAX -->
                        </div>
                        <button id="load-more" class="load-more-btn w-100">Muat Lebih Banyak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {
            function loadKategori() {
                $.ajax({
                    url: "https://berbagipendidikan.org/api/kategori-berita",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.success && response.data && response.data.data.length > 0) {
                            let kategoriHtml = "";

                            // Loop tiap kategori
                            response.data.data.forEach(function (kategori) {

                                // Fetch berita terbaru berdasarkan kategori
                                $.ajax({
                                    url: `https://berbagipendidikan.org/api/berita-terbaru-by-kategori/${kategori.id}`,
                                    type: "GET",
                                    dataType: "json",
                                    success: function (beritaResponse) {
                                        if (
                                            beritaResponse.success &&
                                            beritaResponse.data &&
                                            beritaResponse.data.judul
                                        ) {
                                            // Convert judul ke slug
                                            const judulSlug = beritaResponse.data.judul
                                                .toLowerCase()
                                                .replace(/\s+/g, '-');

                                            kategoriHtml += `
                                                <a href="/berita/${judulSlug}" class="kategori-badge">
                                                    ${kategori.name_kategori}
                                                </a>`;
                                        } else {
                                            // Jika tidak ada berita, tetap tampilkan badge tapi nonaktif
                                            kategoriHtml += `
                                                <span class="kategori-badge bg-secondary">
                                                    ${kategori.name_kategori}
                                                </span>`;
                                        }
                                        $("#kategori-container").html(kategoriHtml);
                                    },
                                    error: function () {
                                        kategoriHtml += `
                                            <span class="kategori-badge bg-secondary">
                                                ${kategori.name_kategori}
                                            </span>`;
                                        $("#kategori-container").html(kategoriHtml);
                                    }
                                });

                            });
                        } else {
                            $("#kategori-container").html(
                                "<p class='text-muted'>Tidak ada kategori tersedia.</p>"
                            );
                        }
                    },
                    error: function () {
                        $("#kategori-container").html(
                            "<p class='text-danger'>Gagal memuat kategori berita.</p>"
                        );
                    }
                });
            }

            loadKategori();
        });

        $(document).ready(function() {
            let beritaId = "{{ $berita['id'] }}";

            // Load jumlah like dari API
            function loadBeritaStats() {
                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita/${beritaId}`,
                    type: "GET",
                    success: function(response) {
                        if (response.success) {
                            $("#like-count").text(response.data.likes_berita);
                            // Jika sudah like sebelumnya, beri warna merah
                            if (sessionStorage.getItem(`liked_${beritaId}`) === "true") {
                                $(".like-button").addClass("fas").removeClass("far");
                            }
                        }
                    },
                    error: function() {
                        console.error("Gagal mengambil data berita.");
                    }
                });

                $.ajax({
                    url: `https://berbagipendidikan.org/api/komentar-count/${beritaId}`,
                    type: "GET",
                    success: function(response) {
                        if (response.status && response.data) {
                            $("#comment-count").text(response.data.jumlah_komentar);
                        }
                    },
                    error: function() {
                        console.error("Gagal mengambil jumlah komentar.");
                    }
                });
            }

            // Like button (AJAX)
            $(".like-button").on("click", function() {
                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita/${beritaId}/like`,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        if (response.success) {
                            $("#like-count").text(response.likes);

                            // Toggle class untuk mengganti ikon
                            let likeIcon = $(".like-button");
                            if (likeIcon.hasClass("fas")) {
                                likeIcon.removeClass("fas text-danger").addClass(
                                    "far text-secondary");
                                sessionStorage.removeItem(`liked_${beritaId}`);
                            } else {
                                likeIcon.removeClass("far text-secondary").addClass(
                                    "fas text-danger");
                                sessionStorage.setItem(`liked_${beritaId}`, "true");
                            }
                        }
                    },
                    error: function() {
                        console.error("Gagal menyukai berita.");
                    }
                });
            });

            // Share ke WhatsApp & Facebook
            $(".share-wa").on("click", function(e) {
                e.preventDefault();
                let url = encodeURIComponent(window.location.href);
                window.open(`https://wa.me/?text=${url}`, "_blank");
            });

            $(".share-fb").on("click", function(e) {
                e.preventDefault();
                let url = encodeURIComponent(window.location.href);
                window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, "_blank");
            });

            // Copy link ke clipboard
            $(".copy-link").on("click", function(e) {
                e.preventDefault();
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert("Link telah disalin!");
                });
            });

            // Load statistik berita awal
            loadBeritaStats();
        });

        $(document).ready(function() {
            // Function to convert hashtags to clickable links
            function convertHashtagsToLinks(text) {
                return text.replace(/#(\w+)/g, function(match, tag) {
                    // Convert hashtag to clickable link that redirects to Google search
                    let encodedTag = encodeURIComponent(tag);
                    return `<a href="https://www.google.com/search?q=${encodedTag}" class="text-primary" target="_blank">${match}</a>`;
                });
            }

            // Apply the conversion to the content after it's rendered
            var kontenElement = $('#konten-detail');
            if (kontenElement.length) {
                kontenElement.html(convertHashtagsToLinks(kontenElement.html()));
            }
        });

        $(document).ready(function() {
            let currentPage = 1; // Track the current page for berita
            const perPage = 5; // Number of articles per page

            // Function to load the latest news (Berita Terkini)
            function loadBeritaTerkini() {
                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita?page=${currentPage}&per_page=${perPage}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            let beritaHtml = "";
                            response.data.forEach(function(berita) {
                                if (berita.status_berita === "Tidak Aktif") {
                                    return;
                                }

                                let beritaDetailUrl =
                                    `/berita/${berita.judul.replace(/\s+/g, '-')}`;

                                beritaHtml += `
                                <div class="card berita-item">
                                    <img src="https://berbagipendidikan.org${berita.foto}" class="card-img-top">
                                    <div class="card-body">
                                        <h5 class="card-title"><a href="${beritaDetailUrl}" class="text-decoration-none">${berita.judul}</a></h5>
                                    </div>
                                </div>
                            `;
                            });
                            $("#recent-berita-container").html(beritaHtml);
                        }
                    }
                });
            }

            loadBeritaTerkini();

            $("#load-more").on("click", function() {
                currentPage++; // Increment the page number
                loadBeritaTerkini(); // Load the next set of news
            });
        });
    </script>
@endsection
