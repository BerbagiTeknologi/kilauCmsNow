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
            display: block;
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        /* Style carousel (sudah ada pada code sebelumnya) */
        .post-img img {
            border-radius: 10px;
            width: 100%;
            height: auto;
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
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            padding: 10px 0;
        }

        .kategori-badge {
            flex: 0 0 auto;
            /* Prevent them from shrinking or growing */
            width: 140px;
            height: 50px;
            background-color: #1363c6;
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

        .kategori-badge.bg-secondary {
            background-color: gray;
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
                                        <span class="like-count">{{ $berita['likes_berita'] ?? 0 }}</span>
                                    </div>

                                    <!-- Komentar -->
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-comment text-secondary"></i>
                                        <span class="comment-count">0</span>
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
                                    <a href="#" class="badge bg-primary text-white px-3 py-2 text-decoration-none">
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

                @include('LandingPageKilau.Berita.komentar')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
          $(document).ready(function() {
            // Global Pagination Variables
            let currentPageLatest = 1;
            const perPageLatest = 5;
            let currentCategory = null;  // Tidak menggunakan localStorage permanen
            let currentPageCategory = 1;
            const perPageCategory = 5;

            // ----- Load Kategori -----
            function loadKategori() {
                $.get("https://berbagipendidikan.org/api/kategori-berita", function(response) {
                    if (response.success && response.data?.data?.length > 0) {
                        const kategoriPromises = response.data.data.map(kategori => {
                            return $.get(`https://berbagipendidikan.org/api/berita-terbaru-by-kategori/${kategori.id}`)
                                .then(beritaResponse => {
                                    if (beritaResponse.success && beritaResponse.data && beritaResponse.data.judul) {
                                        return `<a href="#" class="kategori-badge" data-id="${kategori.id}" data-name="${kategori.name_kategori}">${kategori.name_kategori}</a>`;
                                    } else {
                                        return `<span class="kategori-badge bg-secondary">${kategori.name_kategori}</span>`;
                                    }
                                }).catch(() => {
                                    return `<span class="kategori-badge bg-secondary">${kategori.name_kategori}</span>`;
                                });
                        });
                        Promise.all(kategoriPromises).then(results => {
                            const aktif = results.filter(html => html.includes('data-id'));
                            const nonaktif = results.filter(html => html.includes('bg-secondary'));
                            $("#kategori-container").html(aktif.join('') + nonaktif.join(''));
                        });
                    } else {
                        $("#kategori-container").html("<p class='text-muted'>Tidak ada kategori tersedia.</p>");
                    }
                }).fail(() => {
                    $("#kategori-container").html("<p class='text-danger'>Gagal memuat kategori berita.</p>");
                });
            }
            loadKategori();

            // ----- Fungsi untuk Load Berita Utama Berdasarkan Kategori (header utama) -----
            function loadMainBeritaByKategori(categoryId, categoryName) {
                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita-terbaru-by-kategori/${categoryId}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success && response.data) {
                            let berita = response.data;
                            
                            // Buat Carousel jika ada gambar
                            let carouselHtml = "";
                            if (berita.foto || berita.foto2 || berita.foto3) {
                                let indicators = "";
                                let slides = "";
                                let active = "active";
                                if (berita.foto) {
                                    indicators += `<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="${active}"></button>`;
                                    slides += `<div class="carousel-item ${active}">
                                        <img src="https://berbagipendidikan.org${berita.foto}" class="d-block w-100">
                                    </div>`;
                                    active = "";
                                }
                                if (berita.foto2) {
                                    indicators += `<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" class="${active}"></button>`;
                                    slides += `<div class="carousel-item ${active}">
                                        <img src="https://berbagipendidikan.org${berita.foto2}" class="d-block w-100">
                                    </div>`;
                                    active = "";
                                }
                                if (berita.foto3) {
                                    indicators += `<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" class="${active}"></button>`;
                                    slides += `<div class="carousel-item ${active}">
                                        <img src="https://berbagipendidikan.org${berita.foto3}" class="d-block w-100">
                                    </div>`;
                                }
                                carouselHtml = `<div class="post-img">
                                    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-indicators">${indicators}</div>
                                        <div class="carousel-inner">${slides}</div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                </div>`;
                            }
                            
                            let metaHtml = `<div class="meta-top">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                    <time>${berita.tanggal}</time>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-eye text-secondary"></i>
                                    <span class="fw-bold">${berita.views_berita}</span>
                                    <span class="text-muted">x dilihat</span>
                                </div>
                                <div class="meta-right">
                                    <div class="d-flex align-items-center">
                                        <i class="like-button far fa-thumbs-up text-secondary" data-id="${berita.id}"></i>
                                        <span class="like-count">${berita.likes_berita}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-comment text-secondary"></i>
                                        <span class="comment-count" data-id="${berita.id}">${berita.comment_count}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle p-1" data-bs-toggle="dropdown" aria-expanded="false">
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
                            </div>`;
                            
                            let kategoriHtml = "";
                            if (berita.kategori && berita.kategori.name_kategori) {
                                kategoriHtml = `<div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-folder-open text-primary me-2"></i>
                                    <a href="#" class="badge bg-primary text-white px-3 py-2 text-decoration-none">
                                        ${berita.kategori.name_kategori}
                                    </a>
                                </div>`;
                            }
                            
                            let articleHtml = `<article class="article">
                                ${carouselHtml}
                                <h2 class="title">${berita.judul}</h2>
                                <div class="d-flex align-items-center" style="margin-bottom: -5px;">
                                    <h6 class="text-primary fw-bold mb-0">BeritaKilau</h6>
                                    <i class="fas fa-check-circle text-primary ms-1"></i>
                                </div>
                                ${metaHtml}
                                ${kategoriHtml}
                                <div class="content py-3" id="konten-detail">
                                    ${berita.konten}
                                </div>
                            </article>`;
                            
                            $("#blog-details").html(articleHtml);
                            $("input[name='id_berita']").val(berita.id);
                            
                            // Jika ada hashtag di konten, konversikan menjadi link
                            convertAndApplyHashtags();
                        }
                    },
                    error: function() {
                        console.error("Gagal memuat berita utama untuk kategori ini.");
                    }
                });
            }
            
            // ----- Fungsi untuk Load Berita Berdasarkan Kategori (list berita kategori dengan pagination) -----
            function loadBeritaByKategori(categoryId, categoryName, append = false) {
                $("#recent-berita-container").html('');
                $(".recent-berita h4").text(`Berita Kategori ${categoryName}`);
                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita-terbaru-by-kategoriall/${categoryId}?page=${currentPageCategory}&per_page=${perPageCategory}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        let beritaHtml = "";
                        if (response.success && response.data) {
                            let beritaArray = Array.isArray(response.data) ? response.data : [response.data];
                            if (beritaArray.length > 0) {
                                beritaArray.forEach(function(berita) {
                                    // Buat link detail dengan data atribut untuk judul dan kategori.
                                    let beritaDetailUrl = "#";
                                    beritaHtml += `
                                        <div class="card berita-item">
                                            <img src="https://berbagipendidikan.org${berita.foto}" class="card-img-top">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <a href="${beritaDetailUrl}" class="text-decoration-none detail-link" data-judul="${berita.judul}" data-cat="${categoryId}">${berita.judul}</a>
                                                </h5>
                                            </div>
                                        </div>
                                    `;
                                });
                            }
                        }
                        if (beritaHtml === "") {
                            beritaHtml = "<p class='text-muted'>Berita untuk kategori ini belum tersedia.</p>";
                        }
                        $("#recent-berita-container").html(beritaHtml);
                    }
                });
            }

            // ----- Fungsi untuk Load Berita Terkini (default) dengan pagination -----
            function loadBeritaTerkini(append = false) {
                $(".recent-berita h4").text("Berita Terkini");
                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita?page=${currentPageLatest}&per_page=${perPageLatest}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            let beritaHtml = "";
                            response.data.forEach(function(berita) {
                                if (berita.status_berita === "Tidak Aktif") {
                                    return;
                                }
                                let beritaDetailUrl = "/berita/" + berita.judul.replace(/\s+/g, '-');
                                beritaHtml += `
                                    <div class="card berita-item">
                                        <img src="https://berbagipendidikan.org${berita.foto}" class="card-img-top">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <a href="${beritaDetailUrl}" class="text-decoration-none">${berita.judul}</a>
                                            </h5>
                                        </div>
                                    </div>
                                `;
                            });
                            $("#recent-berita-container").html(beritaHtml);
                        }
                    }
                });
            }
            
            // ----- Fungsi untuk Load Detail Berita Berdasarkan Judul dan Kategori -----
            function loadDetailBerita(judul, categoryId) {
                // Encode judul agar aman untuk URL
                const encodedJudul = encodeURIComponent(judul);
                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita/${encodedJudul}/${categoryId}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success && response.data) {
                            let berita = response.data;
                            
                            // Simpan data berita global untuk form komentar
                            // window.currentBerita = { id: berita.id, judul: berita.judul };
                            // $("input[name='id_berita']").val(berita.id);

                            window.currentBeritaId = berita.id;
                            window.currentCategoryId = categoryId;
                            window.currentEncodedJudul = encodeURIComponent(berita.judul);
                            sessionStorage.setItem('currentBeritaId', berita.id);

                            $("input[name='id_berita']").val(berita.id);
                            
                            // Render carousel jika ada gambar
                            let carouselHtml = "";
                            if (berita.foto || berita.foto2 || berita.foto3) {
                                let indicators = "";
                                let slides = "";
                                let active = "active";
                                if (berita.foto) {
                                    indicators += `<button type="button" data-bs-target="#detailCarousel" data-bs-slide-to="0" class="${active}"></button>`;
                                    slides += `<div class="carousel-item ${active}">
                                        <img src="https://berbagipendidikan.org${berita.foto}" class="d-block w-100">
                                    </div>`;
                                    active = "";
                                }
                                if (berita.foto2) {
                                    indicators += `<button type="button" data-bs-target="#detailCarousel" data-bs-slide-to="1" class="${active}"></button>`;
                                    slides += `<div class="carousel-item ${active}">
                                        <img src="https://berbagipendidikan.org${berita.foto2}" class="d-block w-100">
                                    </div>`;
                                    active = "";
                                }
                                if (berita.foto3) {
                                    indicators += `<button type="button" data-bs-target="#detailCarousel" data-bs-slide-to="2" class="${active}"></button>`;
                                    slides += `<div class="carousel-item ${active}">
                                        <img src="https://berbagipendidikan.org${berita.foto3}" class="d-block w-100">
                                    </div>`;
                                }
                                carouselHtml = `<div class="post-img">
                                    <div id="detailCarousel" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-indicators">${indicators}</div>
                                        <div class="carousel-inner">${slides}</div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#detailCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#detailCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                </div>`;
                            }
                            
                            // Render meta data dan kategori
                            let metaHtml = `<div class="meta-top">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                    <time>${berita.tanggal}</time>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-eye text-secondary"></i>
                                    <span class="fw-bold">${berita.views_berita}</span>
                                    <span class="text-muted">x dilihat</span>
                                </div>
                                <div class="meta-right">
                                    <div class="d-flex align-items-center">
                                        <i class="like-button far fa-thumbs-up text-secondary" data-id="${berita.id}"></i>
                                        <span class="like-count">${berita.likes_berita}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-comment text-secondary"></i>
                                        <span class="comment-count" data-id="${berita.id}">${berita.comment_count}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle p-1" data-bs-toggle="dropdown" aria-expanded="false">
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
                            </div>`;
                            
                            let kategoriHtml = "";
                            if (berita.kategori && berita.kategori.name_kategori) {
                                kategoriHtml = `<div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-folder-open text-primary me-2"></i>
                                    <a href="#" class="badge bg-primary text-white px-3 py-2 text-decoration-none">
                                        ${berita.kategori.name_kategori}
                                    </a>
                                </div>`;
                            }
                            
                            let articleHtml = `<article class="article">
                                ${carouselHtml}
                                <h2 class="title">${berita.judul}</h2>
                                <div class="d-flex align-items-center" style="margin-bottom: -5px;">
                                    <h6 class="text-primary fw-bold mb-0">BeritaKilau</h6>
                                    <i class="fas fa-check-circle text-primary ms-1"></i>
                                </div>
                                ${metaHtml}
                                ${kategoriHtml}
                                <div class="content py-3" id="konten-detail">
                                    ${berita.konten}
                                </div>
                            </article>`;
                            
                            $("#blog-details").html(articleHtml);
                            convertAndApplyHashtags();
                            
                            // Render komentar langsung jika tersedia pada response detail
                            if (berita.comments && berita.comments.length > 0) {
                                let komentarHtml = renderKomentar(berita.comments);
                                $('#daftar-komentar').html(komentarHtml);
                            } else {
                                $('#daftar-komentar').html('<p class="text-muted">Belum ada komentar.</p>');
                            }
                            
                            // Update global variables untuk loadKomentar selanjutnya
                            // window.currentBeritaId = berita.id;
                            // window.currentEncodedJudul = encodeURIComponent(berita.judul);
                            // window.currentCategoryId = categoryId;


                            // (Opsional) Update URL browser
                            history.pushState(null, null, `/berita/${berita.judul.replace(/\s+/g, '-')}`);
                            // const cleanJudul = berita.judul.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/gi, '');
                            // history.pushState(null, null, `/berita/${cleanJudul}-${berita.id}`);

                            // 🟢 Konversi hashtag (opsional)
                            // convertAndApplyHashtags();
                        }
                    },
                    error: function() {
                        console.error("Gagal memuat detail berita.");
                    }
                });
            }


            // ----- Fungsi untuk Convert Hashtags ke Link -----
            function convertHashtagsToLinks(text) {
                return text.replace(/#(\w+)/g, function(match, tag) {
                    let encodedTag = encodeURIComponent(tag);
                    return `<a href="https://www.google.com/search?q=${encodedTag}" class="text-primary" target="_blank">${match}</a>`;
                });
            }
            // Helper: Ambil konten detail dan ubah hashtag-nya
            function convertAndApplyHashtags() {
                var kontenElement = $('#konten-detail');
                if (kontenElement.length) {
                    kontenElement.html(convertHashtagsToLinks(kontenElement.html()));
                }
            }

            // ----- Load Berita Default: Berita Terkini -----
            loadBeritaTerkini();

            // ----- Event Handler: Ketika badge kategori diklik -----
            $(document).on("click", ".kategori-badge", function(e) {
                e.preventDefault();
                const categoryId = $(this).data("id");
                const categoryName = $(this).data("name");
                if (categoryId && categoryName) {
                    currentCategory = categoryId;    // Aktifkan mode kategori
                    currentPageCategory = 1;           // Reset halaman kategori
                    // Simpan state kategori untuk navigasi ke halaman detail
                    sessionStorage.setItem('selectedCategory', JSON.stringify({ id: categoryId, name: categoryName }));
                    loadMainBeritaByKategori(categoryId, categoryName);
                    loadBeritaByKategori(categoryId, categoryName, false);
                }
            });

            // ----- Event Handler: Untuk menangani klik pada link detail berita di daftar kategori -----
            $(document).on("click", ".detail-link", function(e) {
                e.preventDefault();
                const judul = $(this).data("judul");
                const cat = $(this).data("cat"); // id kategori
                loadDetailBerita(judul, cat);
            });

            // ----- Inisialisasi halaman: periksa apakah refresh atau navigasi biasa -----
            let navType = "";
            if (performance.getEntriesByType && performance.getEntriesByType("navigation").length > 0) {
                navType = performance.getEntriesByType("navigation")[0].type; // misalnya: 'reload', 'navigate'
            } else {
                navType = performance.navigation.type; // Fallback; 1 artinya reload
            }
            
            if (navType === "reload" || navType === 1) {
                // Jika halaman di-refresh, hapus state kategori dan load default Berita Terkini
                sessionStorage.removeItem('selectedCategory');
                loadBeritaTerkini();
            } else {
                // Jika bukan refresh, periksa state kategori yang tersimpan
                const selectedCategory = sessionStorage.getItem('selectedCategory');
                if (selectedCategory) {
                    const { id, name } = JSON.parse(selectedCategory);
                    currentCategory = id;
                    currentPageCategory = 1;
                    loadMainBeritaByKategori(id, name);
                    loadBeritaByKategori(id, name, false);
                } else {
                    loadBeritaTerkini();
                }
            }

            // ----- Event Handler: Tombol Load More -----
            $("#load-more").on("click", function() {
                if (currentCategory !== null) {
                    currentPageCategory++;
                    let categoryName = $(".kategori-badge[data-id='" + currentCategory + "']").data("name") || "";
                    loadBeritaByKategori(currentCategory, categoryName, false);
                } else {
                    currentPageLatest++;
                    loadBeritaTerkini(false);
                }
            });
            
            // ----- Fungsi untuk Update Statistik Komentar -----
            let beritaId = "{{ $berita['id'] }}";
            function updateCommentCount() {
                $.ajax({
                    url: `https://berbagipendidikan.org/api/komentar-count/${beritaId}`,
                    type: "GET",
                    success: function(response) {
                        if (response.status && response.data) {
                            $(".comment-count").text(response.data.jumlah_komentar);
                        }
                    },
                    error: function() {
                        console.error("Gagal mengambil jumlah komentar.");
                    }
                });
            }
            updateCommentCount();
            
            $(document).on("click", ".like-button", function(e) {
                e.preventDefault();
                
                // Cek apakah user sudah login
                if (!localStorage.getItem('user_token')) {
                    // Ubah pesan modal sesuai dengan konteks like berita
                    $('#authModal .modal-body').text("Untuk menyukai berita, silakan login atau registrasi terlebih dahulu agar data Anda tercatat.");
                    $('#authModal').modal('show');
                    return; // Hentikan proses like
                }
                
                let button = $(this);
                let id = button.data("id");
                
                // Jika sudah liked, maka jangan lakukan AJAX request lagi
                if (button.hasClass("liked")) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: 'Anda sudah menyukai berita ini.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }
                
                // Lakukan AJAX request untuk like berita
                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita/${id}/like`,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        if (response.success) {
                            // Perbarui like count di elemen .like-count
                            button.closest('.d-flex').find(".like-count").text(response.likes);
                            // Toggle tampilan tombol:
                            // Karena kita hanya memperbolehkan satu kali like, maka kita hanya tambahkan kelas "liked".
                            button.addClass("liked");
                            // Simpan status di sessionStorage (opsional)
                            sessionStorage.setItem(`liked_${id}`, "true");
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Suka ditambahkan!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function() {
                        console.error("Gagal menyukai berita.");
                    }
                });
            });
            
            // ----- Share Functionality dengan event delegation -----
            $(document).on("click", ".share-wa", function(e) {
                e.preventDefault();
                let url = encodeURIComponent(window.location.href);
                window.open(`https://wa.me/?text=${url}`, "_blank");
                Swal.fire({
                    icon: 'success',
                    title: 'Terima kasih sahabat Kilau!',
                    html: '<i class="fas fa-handshake fa-lg"></i> Link telah disalin ke clipboard.'
                });
            });
            
            $(document).on("click", ".share-fb", function(e) {
                e.preventDefault();
                let url = encodeURIComponent(window.location.href);
                window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, "_blank");
                Swal.fire({
                    icon: 'success',
                    title: 'Terima kasih sahabat Kilau!',
                    html: '<i class="fas fa-handshake fa-lg"></i> Link telah disalin ke clipboard.'
                });
            });
            
            $(document).on("click", ".copy-link", function(e) {
                e.preventDefault();
                navigator.clipboard.writeText(window.location.href).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terima kasih sahabat Kilau!',
                        html: '<i class="fas fa-handshake fa-lg"></i> Link telah disalin ke clipboard.'
                    });
                });
            });
            
            // ----- Convert Hashtags to Clickable Links -----
            function convertHashtagsToLinks(text) {
                return text.replace(/#(\w+)/g, function(match, tag) {
                    let encodedTag = encodeURIComponent(tag);
                    return `<a href="https://www.google.com/search?q=${encodedTag}" class="text-primary" target="_blank">${match}</a>`;
                });
            }
            var kontenElement = $('#konten-detail');
            if (kontenElement.length) {
                kontenElement.html(convertHashtagsToLinks(kontenElement.html()));
            }
            
            // Scroll Comment
            $(document).on('click', '.fa-comment', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $("#daftar-komentar").offset().top - 100
                }, 200);
            });
        });
    </script>
@endsection
