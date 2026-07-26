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
    #campaign-scroll-container::-webkit-scrollbar{display:none}
    #campaign-scroll-container{-ms-overflow-style:none;scrollbar-width:none}

    /* ===== MINIMAL PAGINATION (jika suatu saat dipakai) ===== */
    .pagination-wrapper{display:flex;justify-content:flex-start}
    .pagination{gap:.25rem}
    .pagination .page-link{border:none;background:transparent;padding:.25rem .6rem;line-height:1;color:#333}
    .pagination .page-item.active .page-link{background:#007bff;color:#fff;border-radius:.25rem}
    .pagination .page-item.disabled .page-link{opacity:.4;cursor:not-allowed}

    /* ===== MOBILE (≤576 px) → kartu ditumpuk kebawah ===== */
    @media (max-width:576px){
        /* kontainer tidak lagi nowrap & scroll‑x */
        #campaign-scroll-container{
            flex-wrap:wrap;overflow-x:hidden;gap:1rem;
        }
        /* kartu lebar penuh */
        .campaign-card-article{width:100%!important;min-width:100%!important;height:auto}
        .campaign-img-top{height:200px}
        /* kolom bootstrap lebar penuh agar padding sisi terpakai */
        #campaign-scroll-container>.col-auto{flex:0 0 100%;max-width:100%}
    }
</style>

{{-- ========== MARK‑UP ========== --}}
<div class="campaign-section mt-5">

    {{-- Judul / label --}}
    <h4 class="text-primary mb-3">Campaign Kilau Indonesia</h4>

    {{-- Kartu‑kartu campaign --}}
    <div id="campaign-scroll-container" class="row g-4 flex-md-nowrap overflow-x-auto scroll-hidden">
        @foreach($campaigns as $campaign)
            @php
                $target     = $campaignTargets[$campaign['id_category']] ?? 10_000_000;
                $terkumpul  = $campaign['terkumpul'] ?? 0;
                $persentase = $target>0 ? $terkumpul/$target*100 : 0;

                $sisaLabel  = $campaign['id_category']==='Open Goals'
                              ? '<i class="fas fa-infinity"></i>'
                              : max(\Carbon\Carbon::now()->diffInDays(
                                    \Carbon\Carbon::parse($campaign['end_date']),false),0).' Hari';
            @endphp

            <div class="col-12 col-sm-auto d-flex align-items-stretch wow fadeIn">
                <article class="campaign-card-article">
                    <div class="campaign-img-top">
                        <img src="{{ $campaign['url_image'] }}" class="d-block w-100 h-100" alt="campaign image">
                    </div>

                    <div class="card-body p-3 d-flex flex-column">
                        <div>
                            <h6 class="mb-2">
                                <a href="{{ $campaign['url_link'] }}" class="text-decoration-none">
                                    {{ \Illuminate\Support\Str::limit($campaign['title'],45,'…') }}
                                </a>
                            </h6>
                            <div class="text-muted small mb-2">
                                <i class="fas fa-check-circle text-primary"></i>
                                {{ $campaign['user']['name'] }}
                            </div>

                            <div class="d-flex justify-content-between mb-1 campaign-info">
                                <span>Terkumpul</span><span>Sisa Waktu</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 campaign-info">
                                <strong>Rp{{ number_format($terkumpul,0,',','.') }}</strong>
                                {!! $sisaLabel !!}
                            </div>
                            <div class="progress mb-2"><div class="progress-bar" style="width:{{ $persentase }}%"></div></div>
                        </div>

                        <a href="{{ $campaign['url_link'] }}"
                           class="btn btn-sm btn-outline-primary w-100 mt-auto">
                           Lihat Campaign
                        </a>
                    </div>
                </article>
            </div>
        @endforeach
    </div>

</div>

{{-- ========== (optional) SCRIPTS ========== --}}
@push('scripts')
<script>
/* jika suatu saat membutuhkan paginasi AJAX, tuliskan di sini */
</script>
@endpush
