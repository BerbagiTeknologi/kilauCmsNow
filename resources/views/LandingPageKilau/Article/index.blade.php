@extends('App.master')

@section('style')
<style>
/* ================= GALERI STYLE ================ */
.gallery-wrapper{
    position:relative;
    padding:0 60px;                /* ruang untuk panah */
}

.gallery-container{
    display:flex;
    gap:20px;
    overflow-x:auto;
    padding-block:8px;
    scroll-behavior:smooth;
    scroll-snap-type:x mandatory;

    /* sembunyikan scrollbar */
    scrollbar-width:none;   -ms-overflow-style:none;
}
.gallery-container::-webkit-scrollbar{display:none}

.card-article{
    flex:0 0 300px;                /* lebar kartu tetap */
    scroll-snap-align:start;
}

/* ---- GAMBAR / CAROUSEL DI KARTU ---- */
.card-article .carousel,
.card-article img{
    width:100%;
    aspect-ratio:3/2;              /* tinggi otomatis ≈ 200 px utk lebar 300 */
    object-fit:cover;              /* isi penuh, crop secukupnya */
    border-radius:.5rem .5rem 0 0;
}

.badge-cat{
    background:#0d6efd;
    font-size:.75rem;
}

/* ---- Tombol panah galeri ---- */
.gallery-arrow{
    position:absolute;
    top:50%; transform:translateY(-50%);
    width:46px; height:46px;
    background:#fff; border:none;
    border-radius:50%;
    box-shadow:0 2px 6px rgba(0,0,0,.25);
    z-index:10;
}
.gallery-arrow-left  { left:-60px; }
.gallery-arrow-right { right:-60px; }
</style>
@endsection

@section('content')
<!-- Hero ------------------------------------------------------------->
<div class="container-fluid pt-5 bg-primary hero-header">
    <div class="container pt-5">
        <div class="row g-5 pt-5">
            <div class="col-12 text-center" style="margin-top:100px!important;">
                <h1 class="display-4 text-white mb-4">Artikel</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
                        <li class="breadcrumb-item"><a class="text-white" href="#">Profil</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Artikel Kami</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Galeri Artikel --------------------------------------------------->
<div class="container py-5">
    <div class="row text-center">
        <h2 class="mb-1">Galeri Artikel</h2>
        <p>Lihat kumpulan artikel terbaru kami.</p>
    </div>

    <div class="gallery-wrapper my-4">
        <button class="gallery-arrow gallery-arrow-left"  id="arrowLeft"><i class="fas fa-chevron-left"></i></button>
        <div   class="gallery-container" id="galleryRow"><!-- kartu dirender JS --></div>
        <button class="gallery-arrow gallery-arrow-right" id="arrowRight"><i class="fas fa-chevron-right"></i></button>
    </div>

    <!-- pagination nomor -->
    <nav><ul class="pagination justify-content-center" id="pagBox"></ul></nav>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    const $row = $('#galleryRow');
    const $pag = $('#pagBox');
    const cardW = 320;                        // 300 kartu + 20 gap
    const PLACEHOLDER = '{{ asset('assets_admin/img/noimage.jpg') }}';

    let perPage = 6;
    let cur = 1, last = 1;

    /* ------- ambil data paginasi ------- */
    function fetchPage(p = 1) {
        $.get("{{ route('lp.article.list') }}", { page: p }, res => {
            cur     = res.current_page;
            last    = res.last_page;
            perPage = res.per_page ?? 6;

            renderCards(res.data);
            renderPagination();
            centerScroll();
        }).fail(()=>alert('Gagal memuat data'));
    }

    /* ------- render kartu ------- */
    function renderCards(list) {
        $row.empty();

        list.forEach(a => {
            const carouselId = `carousel-${a.id}`;
            const thumbs = a.thumbs.length ? a.thumbs : [PLACEHOLDER];

            /* inner carousel */
            let inner = '';
            thumbs.forEach((src,i)=>{
                inner += `
                    <div class="carousel-item ${i===0?'active':''}">
                        <img src="${src}" class="d-block w-100" alt="thumbnail">
                    </div>`;
            });

            /* panah carousel bila gambar > 1 */
            const controls = thumbs.length > 1 ? `
                <button class="carousel-control-prev"  type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next"  type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                  <span class="carousel-control-next-icon"></span>
                </button>` : '';

            /* kartu */
            $row.append(`
              <div class="card shadow-sm card-article">
                <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-inner">${inner}</div>
                  ${controls}
                </div>
                <div class="card-body">
                  <span class="badge badge-cat mb-2">
                    <i class="fas fa-folder-open me-1"></i>${a.kategori ?? '-'}
                  </span>
                  <h6 class="fw-bold mb-1">
                    <!-- hanya judul yang dapat diklik -->
                    <a href="{{ url('artikel') }}/${a.slug}" class="text-decoration-none text-dark">
                      ${a.title}
                    </a>
                  </h6>
                  <small class="text-muted">${a.created}</small>
                </div>
              </div>
            `);
        });
    }

    /* ------- render pagination ------- */
    function renderPagination() {
        let html = '';
        if (last > 1) {
            html += `
              <li class="page-item ${cur===1?'disabled':''}">
                <a class="page-link" data-p="${cur-1}" href="#"><i class="fas fa-angle-left"></i></a>
              </li>`;
            for (let i=1;i<=last;i++){
                html += `
                  <li class="page-item ${i===cur?'active':''}">
                    <a class="page-link" data-p="${i}" href="#">${i}</a>
                  </li>`;
            }
            html += `
              <li class="page-item ${cur===last?'disabled':''}">
                <a class="page-link" data-p="${cur+1}" href="#"><i class="fas fa-angle-right"></i></a>
              </li>`;
        }
        $pag.html(html);
    }

    /* ------- event pagination ------- */
    $(document).on('click','#pagBox .page-link',function(e){
        e.preventDefault();
        const p = $(this).data('p');
        if (p>=1 && p<=last) fetchPage(p);
    });

    /* ------- panah luar galeri ------- */
    $('#arrowLeft') .on('click',()=> $row.scrollLeft($row.scrollLeft()-cardW));
    $('#arrowRight').on('click',()=> $row.scrollLeft($row.scrollLeft()+cardW));

    function centerScroll(){
        const center = ($row[0].scrollWidth - $row[0].clientWidth) / 2;
        $row.scrollLeft(center);
    }

    /* ------- load pertama ------- */
    fetchPage(1);
});
</script>
@endsection

