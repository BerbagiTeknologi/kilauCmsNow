@extends('App.master')

@section('style')
<style>
/* ---------- KONTEN UTAMA ---------- */
.carousel-item img{
    max-height:600px;object-fit:contain;border-radius:10px;width:100%
}
.article-card{
    background:#fff;border-radius:.75rem;padding:1.5rem;
    box-shadow:0 2px 6px rgba(0,0,0,.08)
}

/* ---------- TAG ---------- */
.tag-link{
    display:inline-block;margin:0 8px 8px 0;padding:5px 10px;font-size:.9rem;
    color:#0d6efd;border:1px solid #0d6efd;border-radius:12px;transition:.2s
}
.tag-link:hover{background:#0d6efd;color:#fff;text-decoration:none}

/* ---------- SIDEBAR ---------- */
.latest-wrapper{max-height:600px;overflow-y:auto;padding-right:.25rem}
.latest-card{
    display:flex;gap:.75rem;align-items:center;
    border:none;cursor:pointer;transition:.15s
}
.latest-card:hover{transform:translateY(-2px);box-shadow:0 3px 6px rgba(0,0,0,.1)}
.latest-card img{
    width:120px;height:80px;object-fit:cover;border-radius:.5rem      /* gambar proporsional */
}
.badge-cat-sm{background:#0d6efd;font-size:.7rem;color:#fff}

/* ---------- CUSTOM GUTTER (jarak kolom) ---------- */
.row.gx-custom{--bs-gutter-x:4rem}          /* 64 px */
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

  {{-- gunakan gx-custom untuk jarak horizontal lebih lebar --}}
  <div class="row gx-custom gy-5">
    {{-- ===== Konten utama (7 kolom) ===== --}}
    <div class="col-lg-7">
      <div class="article-card">
        {{-- carousel --}}
        @php $pics = $photos->count() ? $photos : collect([$placeholder]); @endphp
        <div id="detailCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
          <div class="carousel-inner">
            @foreach($pics as $i=>$src)
              <div class="carousel-item {{ $i==0?'active':'' }}">
                <img src="{{ $src }}" class="d-block w-100">
              </div>
            @endforeach
          </div>
          @if($pics->count()>1)
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
                <img src="{{ $photo_author }}"
                     alt="Foto penulis"
                     class="rounded-circle me-3"
                     style="width:48px;height:48px;object-fit:cover">
              @endif
              <span class="fw-bold">{{ $article->author ?? '-' }}</span>
            </div>
          </div>
        @endif

        {{-- meta tanggal & kategori --}}
        <div class="small text-muted mb-3">
          <i class="fas fa-calendar-alt me-1"></i>{{ optional($article->created_at)->translatedFormat('d F Y H:i') ?? '-' }} WIB
          @if($article->kategori)&nbsp;•&nbsp;<i class="fas fa-folder-open me-1"></i>{{ $article->kategori->name_kategori }}@endif
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

    {{-- ===== Sidebar (5 kolom) ===== --}}
    <div class="col-lg-5">
      <h5 class="mb-3">Artikel Terbaru</h5>
      <div class="latest-wrapper">
        @foreach($latest as $l)
          <a href="{{ url('artikel/'.$l['slug']) }}" class="text-decoration-none text-dark">
            <div class="latest-card mb-3">
              <img src="{{ $l['thumb'] }}" alt="thumb">
              <div>
                <small class="fw-semibold d-block">{{ Str::limit($l['title'],60) }}</small>
                <span class="badge badge-cat-sm mt-1">
                  <i class="fas fa-folder-open me-1"></i>{{ $l['kategori'] }}
                </span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
