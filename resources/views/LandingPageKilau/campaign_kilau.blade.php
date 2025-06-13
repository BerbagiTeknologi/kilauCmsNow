{{-- resources/views/LandingPageKilau/campaign_kilau.blade.php --}}

{{-- ========== STYLE ========== --}}
<style>
    /* ===== CARD & LAYOUT ===== */
    .campaign-card-article{width:410px;min-width:410px;height:470px;border-radius:.80rem!important;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 5px 15px rgba(0,0,0,.1);background:#fff;transition:transform .3s ease;}
    .campaign-card-article:hover{transform:translateY(-5px)}
    .campaign-img-top{height:250px;object-fit:cover;overflow:hidden}
    .progress{height:8px;background:#e9ecef;border-radius:10px}.progress-bar{background:#007bff}.campaign-info{font-size:.9rem}
    
    /* ===== HORIZONTAL SCROLL ===== */
    #campaign-scroll-container{display:flex;flex-wrap:nowrap;overflow-x:auto;gap:1rem;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;padding-bottom:1rem;}
    .campaign-card-article{scroll-snap-align:start}
    #campaign-scroll-container::-webkit-scrollbar{display:none}#campaign-scroll-container{-ms-overflow-style:none;scrollbar-width:none}
    
    /* ===== MINIMAL PAGINATION ===== */
    .pagination-wrapper{display:flex;justify-content:flex-start}
    .pagination{gap:.25rem}
    .pagination .page-link{border:none;background:transparent;padding:.25rem .6rem;line-height:1;color:#333}
    .pagination .page-item.active .page-link{background:#007bff;color:#fff;border-radius:.25rem}
    .pagination .page-item.disabled .page-link{opacity:.4;cursor:not-allowed}


    /* ===== MOBILE (≤576 px) → kartu ditumpuk kebawah ===== */
    @media (max-width: 576px) {

    /* kontainer tidak lagi nowrap & scroll-x */
    #campaign-scroll-container{
        flex-wrap:wrap;          /* baris baru */
        overflow-x:hidden;       /* hilangkan scroll horizontal */
        gap:1rem;                /* jarak antar kartu */
    }

    /* kartu lebar penuh */
    .campaign-card-article{
        width:100%!important;
        min-width:100%!important;
    }

    /* kolom bootstrap lebar penuh agar padding sisi terpakai */
    #campaign-scroll-container > .col-auto{
        flex:0 0 100%;
        max-width:100%;
    }
    }
    </style>
    
    {{-- ========== MARKUP ========== --}}
    <div class="container-fluid bg-light py-5">
      <div class="container py-5">
    
        {{-- Judul --}}
        <div class="row justify-content-center mb-4">
          <div class="col-lg-8 text-center">
            <h1 class="display-5 fw-bold mb-2">{{ $campaignMenu->judul }}</h1>
            <p class="lead mb-4">{{ $campaignMenu->subjudul }}</p>
          </div>
        </div>
    
        {{-- Kartu Campaign --}}
        <div id="campaign-scroll-container" class="row g-4 flex-md-nowrap overflow-x-auto scroll-hidden">
          @foreach($campaigns as $campaign)
          @php
              $target     = $campaignTargets[$campaign['id_category']] ?? 10_000_000;
              $terkumpul  = $campaign['terkumpul'] ?? 0;
              $persentase = $target > 0 ? $terkumpul / $target * 100 : 0;
      
              if ($campaign['id_category'] === 'Open Goals') {
                  $sisaLabel = '<i class="fas fa-infinity"></i>';
              } else {
                  $diff = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($campaign['end_date']), false);
                  $sisaLabel = $diff > 0 ? $diff.' Hari' : '0 Hari';
              }
          @endphp
      
           <div class="col-12 col-sm-auto d-flex align-items-stretch wow fadeIn">
              <article class="campaign-card-article">
                <div class="campaign-img-top"><img src="{{ $campaign['url_image'] }}" class="d-block w-100 h-100"></div>
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                  <div>
                    <h6 class="mb-2"><a href="{{ $campaign['url_link'] }}" class="text-decoration-none">{{ \Illuminate\Support\Str::limit($campaign['title'],45,'…') }}</a></h6>
                    <div class="text-muted small mb-2"><i class="fas fa-check-circle text-primary"></i> {{ $campaign['user']['name'] }}</div>
                    <div class="d-flex justify-content-between mb-1 campaign-info"><span>Terkumpul</span><span>Sisa Waktu</span></div>
                    <div class="d-flex justify-content-between mb-2 campaign-info">
                      <strong>Rp{{ number_format($terkumpul,0,',','.') }}</strong>
                      {!! $sisaLabel !!}
                  </div>
                    <div class="progress mb-2"><div class="progress-bar" style="width:{{ $persentase }}%"></div></div>
                  </div>
                  <a href="{{ $campaign['url_link'] }}" class="btn btn-sm btn-outline-primary w-100 mt-auto">Lihat Campaign</a>
                </div>
              </article>
            </div>
          @endforeach
        </div>
    
       {{-- Pagination --}}
      @isset($page)
      @php
          // ------- hitung rentang angka (maks 5) -------
          if ($lastPage <= 5) {
              $start = 1;                 $end = $lastPage;
          } else {
              $start = max(1, min($page - 2, $lastPage - 4));
              $end   = min($lastPage, $start + 4);
          }
      @endphp
      <div class="pagination-wrapper mt-4" id="camp-pagination-wrapper">
          <nav aria-label="Pagination">
              <ul class="pagination pagination-sm mb-0" id="camp-pagination">

                  {{-- First & « Prev : tampil hanya kalau halaman > 1 --}}
                  @if ($page > 1)
                      <li class="page-item">
                          <a class="page-link"
                            href="{{ request()->fullUrlWithQuery(['camp_page'=>1,'camp_per_page'=>$perPage]) }}">
                            First
                          </a>
                      </li>
                      <li class="page-item">
                          <a class="page-link"
                            href="{{ request()->fullUrlWithQuery(['camp_page'=>$page-1,'camp_per_page'=>$perPage]) }}">
                            «
                          </a>
                      </li>
                  @endif

                  {{-- Angka halaman --}}
                  @for ($i = $start; $i <= $end; $i++)
                      <li class="page-item {{ $i == $page ? 'active' : '' }}">
                          <a class="page-link"
                            href="{{ $i == $page ? '#' : request()->fullUrlWithQuery(['camp_page'=>$i,'camp_per_page'=>$perPage]) }}">
                            {{ $i }}
                          </a>
                      </li>
                  @endfor

                  {{-- » Next --}}
                  <li class="page-item {{ $page == $lastPage ? 'disabled' : '' }}">
                      <a class="page-link"
                        href="{{ $page == $lastPage ? '#' : request()->fullUrlWithQuery(['camp_page'=>$page+1,'camp_per_page'=>$perPage]) }}">
                        »
                      </a>
                  </li>

                  {{-- Last --}}
                  <li class="page-item {{ $page == $lastPage ? 'disabled' : '' }}">
                      <a class="page-link"
                        href="{{ $page == $lastPage ? '#' : request()->fullUrlWithQuery(['camp_page'=>$lastPage,'camp_per_page'=>$perPage]) }}">
                        Last
                      </a>
                  </li>

              </ul>
          </nav>
      </div>
      @endisset

      </div>
    </div>
    
    {{-- ========== SCRIPTS ========== --}}
    @push('scripts')
    <script>
    $(function(){
    
      /* ---- Intersep klik pagination (AJAX) ---- */
      $(document).on('click','#camp-pagination a',function(e){
        const url = $(this).attr('href');
        if(url==='#' || $(this).parent().hasClass('disabled')) return;
        e.preventDefault();
    
        const $cards   = $('#campaign-scroll-container');
        const $wrapper = $('#camp-pagination-wrapper');
        const xPos     = $cards.scrollLeft();           // simpan posisi scroll horizontal
    
        $cards.fadeTo(150,.3);
    
        $.get(url,function(html){
            const dom  = $(html);
            $cards.html(dom.find('#campaign-scroll-container').html()).fadeTo(150,1);
            $wrapper.html(dom.find('#camp-pagination-wrapper').html());
            $cards.scrollLeft(xPos);                    // kembalikan posisi scroll
        });
      });
    
    });
    </script>
    @endpush
    