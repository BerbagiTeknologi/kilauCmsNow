@extends('App.master')

@push('meta')
    {{-- Open-Graph --}}
    <meta property="og:type"        content="article">
    <meta property="og:title"       content="{{ $article->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($article->content),150) }}">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:image"       content="{{ $ogImage }}">

    {{-- Twitter --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $article->title }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($article->content),150) }}">
    <meta name="twitter:image"       content="{{ $ogImage }}">
@endpush



@section('style')
<style>
/* ---------- KONTEN UTAMA ---------- */
.carousel-item img{max-height:1000px;object-fit:contain;border-radius:10px;width:200%;}
.article-card {background:#fff;border-radius:.75rem;padding:1rem;box-shadow:0 2px 6px rgba(0,0,0,.08);}

/* ---------- TAG ---------- */
.tag-link{display:inline-block;margin:0 8px 8px 0;padding:5px 10px;font-size:.9rem;color:#0d6efd;
          border:1px solid #0d6efd;border-radius:12px;transition:.2s;}
.tag-link:hover{background:#0d6efd;color:#fff;text-decoration:none;}

/* ---------- SIDEBAR TERBARU ---------- */
.latest-wrapper{max-height:1000px;overflow-y:auto;padding-right:.25rem;}
.latest-card{display:flex;align-items:center;gap:.75rem;padding:.5rem .6rem;background:#fff;
            border-radius:.65rem;min-height:90px;transition:.15s;}
.latest-card:hover{transform:translateY(-2px);box-shadow:0 3px 6px rgba(0,0,0,.1);}

/* kotak gambar */
.latest-thumb{flex:0 0 110px;width:110px;height:74px;background:#f8f8f8;border-radius:.5rem;overflow:hidden;position:relative;}
.latest-thumb img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;transition:transform .3s;}
.latest-card:hover .latest-thumb img{transform:scale(1.05);}
.latest-thumb img.fit-contain{object-fit:contain;background:#f8f8f8;}

.badge-cat-sm{background:#0d6efd;font-size:.7rem;color:#fff;}

/* ---------- CUSTOM GUTTER ---------- */
.row.gx-custom{ --bs-gutter-x:4rem; }

.share-drop{margin-top:-50px;} @media(max-width:575.98px){.share-drop{margin-top:0}}
</style>
@endsection



@section('content')
{{-- ===== HERO ===== --}}
<div class="container-fluid pt-5 bg-primary hero-header">
  <div class="container pt-5">
    <div class="row g-5 pt-5">
      <div class="col-12 text-center" style="margin-top:100px">
        <h1 class="display-4 text-white mb-4">Detail Artikel</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
            <li class="breadcrumb-item"><a class="text-white" href="#">Artikel</a></li>
            <li class="breadcrumb-item text-white active" aria-current="page">{{ $article->title }}</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</div>

{{-- ===== BODY ===== --}}
<div class="container py-5">
  <div class="row mb-4">
    <div class="col-12 text-center">
      <h2 class="mb-1">Nikmati Bacaan &amp; Temukan Insight Terbaru</h2>
      <p>Dapatkan informasi lengkap di bawah dan jelajahi rekomendasi artikel lainnya.</p>
    </div>
  </div>

  <div class="row gx-custom gy-5">
    {{-- ===== Konten utama ===== --}}
    <div class="col-lg-8">
      <div class="article-card">

        {{-- carousel --}}
        @php $pics = $photos->count() ? $photos : collect([$placeholder]); @endphp
        <div id="detailCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
          <div class="carousel-inner">
            @foreach($pics as $i => $src)
              <div class="carousel-item {{ $i==0 ? 'active' : '' }}">
                <img src="{{ $src }}" class="d-block w-100" loading="lazy">
              </div>
            @endforeach
          </div>
          @if($pics->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#detailCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#detailCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          @endif
        </div>

        {{-- judul --}}
        <h2>{{ $article->title }}</h2>

        {{-- blok author --}}
        @if($photo_author || $article->author)
          <div class="mb-3">
            <small class="text-muted d-block mb-2">Dibuat&nbsp;oleh:</small>
            <div class="d-flex align-items-center">
              @if($photo_author)
                <img src="{{ $photo_author }}" alt="Foto penulis"
                     class="rounded-circle me-3" style="width:48px;height:48px;object-fit:cover">
              @endif
              <span class="fw-bold">{{ $article->author ?? '-' }}</span>
            </div>
          </div>
        @endif

        {{-- meta tanggal & kategori --}}
        <div class="small text-muted mb-1">
          <i class="fas fa-calendar-alt me-1"></i>
          {{ optional($article->created_at)->translatedFormat('d F Y H:i') }} WIB
          @if($article->kategori)
            &nbsp;•&nbsp;
            <a href="#"
               class="kategori-filter fw-bold"
               data-cat="{{ $article->kategori->id }}">
               <i class="fas fa-folder-open me-1"></i>{{ $article->kategori->name_kategori }}
            </a>
          @endif
        </div>

        {{-- views • likes • share --}}
        <div class="d-flex align-items-center flex-wrap gap-4 small mb-3">

          {{-- views & likes --}}
          <div class="d-flex align-items-center gap-4">
            <span>
              <i class="fas fa-eye me-1"></i>
              <span id="viewCount">{{ $article->views }}</span> kali dilihat
            </span>

            <button id="likeBtn" type="button" class="btn p-0 border-0 bg-transparent d-flex align-items-center">
              <i id="likeIcon" class="fas fa-heart me-1 text-muted"></i>
              <span id="likeCount">{{ $article->likes }}</span> suka
            </button>

            <span class="d-flex align-items-center">
                <i class="fas fa-comments me-1 text-muted"></i>
                <span id="commentCount">{{ $commentCount }}</span> komentar
            </span>
          </div>

          {{-- Bagikan --}}
          <div class="ms-auto share-drop">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle p-1" data-bs-toggle="dropdown">
              <i class="fas fa-share-alt me-1"></i> Bagikan
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item share-wa" href="#"><i class="fab fa-whatsapp text-success"></i> WhatsApp</a></li>
              <li><a class="dropdown-item share-fb" href="#"><i class="fab fa-facebook text-primary"></i> Facebook</a></li>
              <li><a class="dropdown-item copy-link" href="#"><i class="fas fa-link"></i> Salin Link</a></li>
            </ul>
          </div>
        </div>

        {{-- konten --}}
        <div class="content mb-4">{!! $article->content !!}</div>

        {{-- tags --}}
        <div>
          @forelse($article->tags as $t)
            <a href="{{ $t->link }}" target="_blank" class="tag-link">{{ $t->nama_tags }}</a>
          @empty
            <span class="text-muted">Tidak ada tag.</span>
          @endforelse
        </div>

      </div>
    </div>

    {{-- ===== Sidebar ===== --}}
    <div class="col-lg-4">
      {{-- judul akan berubah dinamis --}}
      <h5 id="latest-heading" class="mb-3">Artikel Terbaru</h5>

      <div class="latest-wrapper">
        @foreach($latest as $l)
          <a href="{{ url('artikel/'.$l['slug']) }}" class="text-decoration-none text-dark">
            <div class="latest-card mb-3">
              <div class="latest-thumb">
                <img src="{{ $l['thumb'] }}" alt="thumb" loading="lazy">
              </div>

              <div class="latest-info">
                <small class="fw-semibold d-block">{{ Str::limit($l['title'],60) }}</small>

                {{-- kirim id & nama kategori --}}
                <a  href="#"
                    class="badge badge-cat-sm kategori-filter mt-1"
                    data-cat="{{ $l['kat_id'] ?? '' }}"
                    data-cat-name="{{ $l['kategori'] }}">
                    <i class="fas fa-folder-open me-1"></i>{{ $l['kategori'] }}
                </a>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </div>


    @include('LandingPageKilau.Article.komentar')
    @include('LandingPageKilau.Berita.partials-donasi')
    <div class="modal fade" id="donasiPrompt" tabindex="-1" aria-labelledby="donasiPromptLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-body text-center p-4">
            <h5 class="fw-bold mb-2">Hai, maaf mengganggu…</h5>
            <p class="mb-3">
              Setelah membaca artikel ini, apakah Anda ingin berdonasi untuk
              mendukung program-program di <b>Kilau Indonesia</b>?
            </p>

            <div class="d-flex justify-content-center gap-2">
              <button id="promptDonasiYa" class="btn btn-primary">Ya, Donasi</button>
              <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Nanti saja</button>
            </div>
          </div>
        </div>
      </div>
    </div>

     @include('LandingPageKilau.Berita.campaign', [
                    'campaigns' => $campaigns
                ])
  </div>
</div>
@endsection



@section('scripts')

<script>
(function () {

  /* tampilkan 5-detik setelah halaman selesai dimuat */
  const DELAY_MS = 5_000;    // 5000 ms  (=5 detik)

  window.addEventListener('DOMContentLoaded', () => {

      const promptEl = document.getElementById('donasiPrompt');   // modal kecil “Hai, maaf…”
      const formEl   = document.getElementById('donasiModal');    // modal form donasi

      if (!promptEl) return;                                      // prompt tidak ada

      const promptModal = new bootstrap.Modal(promptEl);
      const btnYa       = document.getElementById('promptDonasiYa');

      /* klik “Ya, Donasi”  → tutup prompt, buka form  */
      btnYa?.addEventListener('click', () => {
          promptModal.hide();
          if (formEl) new bootstrap.Modal(formEl).show();
      });

      /* setelah 5 detik baru tampilkan prompt */
      setTimeout(() => promptModal.show(), DELAY_MS);
  });

})();
</script>

<script>
$(function () {

  /* ---------- like ---------- */
  const likeUrl = "{{ route('lp.article.like', $article->slug) }}";
  $('#likeBtn').on('click',function(){
      if($(this).prop('disabled')) return;
      $.post(likeUrl,{_token:'{{ csrf_token() }}'},res=>{
          $('#likeCount').text(res.likes);
          $('#likeIcon').removeClass('text-muted').addClass('text-danger');
          $(this).prop('disabled',true);
      });
  });

  /* ---------- share ---------- */
  const pageUrl  = encodeURIComponent("{{ url()->current() }}");
  const pageText = encodeURIComponent("{{ $article->title }}");

  $('.share-wa').click(e=>{
      e.preventDefault();
      window.open(`https://wa.me/?text=${pageText}%20-%20${pageUrl}`,'_blank');
  });
  $('.share-fb').click(e=>{
      e.preventDefault();
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`,'_blank');
  });
  $('.copy-link').click(e=>{
      e.preventDefault();
      navigator.clipboard.writeText("{{ url()->current() }}")
              .then(()=>Swal.fire({toast:true,position:'top-end',icon:'success',
                                   title:'Link disalin',showConfirmButton:false,timer:1500}));
  });

  /* ---------- thumb contain portrait ---------- */
  document.querySelectorAll('.latest-thumb img').forEach(img=>{
     if(img.complete) adjust(img); else img.onload=()=>adjust(img);
     function adjust(el){ if(el.naturalHeight/el.naturalWidth>1.35) el.classList.add('fit-contain'); }
  });

  /* ---------- sidebar filter by kategori ---------- */
  function renderCard(a){
    return `<a href="{{ url('artikel') }}/${a.slug}" class="text-decoration-none text-dark">
      <div class="latest-card mb-3">
        <div class="latest-thumb"><img src="${a.thumb}" alt="thumb"></div>
        <div class="latest-info">
          <small class="fw-semibold d-block">${a.title.substring(0,60)}</small>
          <a href="#" class="badge badge-cat-sm kategori-filter mt-1"
              data-cat="${a.kat_id}" data-cat-name="${a.kategori}">
              <i class="fas fa-folder-open me-1"></i>${a.kategori}
          </a>
        </div>
      </div></a>`;
  }

  function setHeading(name){
    $('#latest-heading').text(name ? `Artikel ${name}` : 'Artikel Terbaru');
  }

  function loadLatest(catId, catName){
    setHeading(catName);                       // ganti judul
    $('.latest-wrapper').html('<p class="text-muted px-2">Memuat…</p>');
    $.get("{{ url('artikel/sidebar-latest') }}/"+catId,res=>{
        if(res.length){
            $('.latest-wrapper').html(res.map(renderCard).join(''));
            // periksa orientation portrait → .fit-contain
            $('.latest-thumb img').each(function(){
                if(this.naturalHeight/this.naturalWidth>1.35) $(this).addClass('fit-contain');
            });
        }else{
            $('.latest-wrapper').html('<p class="text-muted px-2">Tidak ada artikel.</p>');
        }
    });
  }

  $(document).on('click','.kategori-filter',function(e){
      e.preventDefault();
      const id   = $(this).data('cat');
      const name = $(this).data('cat-name');
      if(id) loadLatest(id, name);
  });


  $(document).on('click','.kategori-filter',function(e){
      e.preventDefault();
      const id = $(this).data('cat');
      if(id) loadLatest(id);
  });

});
</script>
@endsection
