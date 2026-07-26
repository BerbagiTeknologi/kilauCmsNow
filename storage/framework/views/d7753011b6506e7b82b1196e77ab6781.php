<?php $__env->startPush('meta'); ?>
    
    <meta property="og:type"        content="article">
    <meta property="og:title"       content="<?php echo e($article->title); ?>">
    <meta property="og:description" content="<?php echo e(Str::limit(strip_tags($article->content),150)); ?>">
    <meta property="og:url"         content="<?php echo e(url()->current()); ?>">
    <meta property="og:image"       content="<?php echo e($ogImage); ?>">

    
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?php echo e($article->title); ?>">
    <meta name="twitter:description" content="<?php echo e(Str::limit(strip_tags($article->content),150)); ?>">
    <meta name="twitter:image"       content="<?php echo e($ogImage); ?>">
<?php $__env->stopPush(); ?>



<?php $__env->startSection('style'); ?>
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
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>

<div class="container-fluid pt-5 bg-primary hero-header">
  <div class="container pt-5">
    <div class="row g-5 pt-5">
      <div class="col-12 text-center" style="margin-top:100px">
        <h1 class="display-4 text-white mb-4">Detail Artikel</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
            <li class="breadcrumb-item"><a class="text-white" href="#">Artikel</a></li>
            <li class="breadcrumb-item text-white active" aria-current="page"><?php echo e($article->title); ?></li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</div>


<div class="container py-5">
  <div class="row mb-4">
    <div class="col-12 text-center">
      <h2 class="mb-1">Nikmati Bacaan &amp; Temukan Insight Terbaru</h2>
      <p>Dapatkan informasi lengkap di bawah dan jelajahi rekomendasi artikel lainnya.</p>
    </div>
  </div>

  <div class="row gx-custom gy-5">
    
    <div class="col-lg-8">
      <div class="article-card">

        
        <?php $pics = $photos->count() ? $photos : collect([$placeholder]); ?>
        <div id="detailCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php $__currentLoopData = $pics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="carousel-item <?php echo e($i==0 ? 'active' : ''); ?>">
                <img src="<?php echo e($src); ?>" class="d-block w-100" loading="lazy">
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <?php if($pics->count() > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#detailCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#detailCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          <?php endif; ?>
        </div>

        
        <h2><?php echo e($article->title); ?></h2>

        
        <?php if($photo_author || $article->author): ?>
          <div class="mb-3">
            <small class="text-muted d-block mb-2">Dibuat&nbsp;oleh:</small>
            <div class="d-flex align-items-center">
              <?php if($photo_author): ?>
                <img src="<?php echo e($photo_author); ?>" alt="Foto penulis"
                     class="rounded-circle me-3" style="width:48px;height:48px;object-fit:cover">
              <?php endif; ?>
              <span class="fw-bold"><?php echo e($article->author ?? '-'); ?></span>
            </div>
          </div>
        <?php endif; ?>

        
        <div class="small text-muted mb-1">
          <i class="fas fa-calendar-alt me-1"></i>
          <?php echo e(optional($article->created_at)->translatedFormat('d F Y H:i')); ?> WIB
          <?php if($article->kategori): ?>
            &nbsp;•&nbsp;
            <a href="#"
               class="kategori-filter fw-bold"
               data-cat="<?php echo e($article->kategori->id); ?>">
               <i class="fas fa-folder-open me-1"></i><?php echo e($article->kategori->name_kategori); ?>

            </a>
          <?php endif; ?>
        </div>

        
        <div class="d-flex align-items-center flex-wrap gap-4 small mb-3">

          
          <div class="d-flex align-items-center gap-4">
            <span>
              <i class="fas fa-eye me-1"></i>
              <span id="viewCount"><?php echo e($article->views); ?></span> kali dilihat
            </span>

            <button id="likeBtn" type="button" class="btn p-0 border-0 bg-transparent d-flex align-items-center"
              data-ga-event="like_article" data-ga-article-slug="<?php echo e($article->slug); ?>">
              <i id="likeIcon" class="fas fa-heart me-1 text-muted"></i>
              <span id="likeCount"><?php echo e($article->likes); ?></span> suka
            </button>

            <span class="d-flex align-items-center">
                <i class="fas fa-comments me-1 text-muted"></i>
                <span id="commentCount"><?php echo e($commentCount); ?></span> komentar
            </span>
          </div>

          
          <div class="ms-auto share-drop">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle p-1" data-bs-toggle="dropdown">
              <i class="fas fa-share-alt me-1"></i> Bagikan
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item share-wa" href="#" data-ga-event="share_article" data-ga-share-method="whatsapp" data-ga-article-slug="<?php echo e($article->slug); ?>"><i class="fab fa-whatsapp text-success"></i> WhatsApp</a></li>
              <li><a class="dropdown-item share-fb" href="#" data-ga-event="share_article" data-ga-share-method="facebook" data-ga-article-slug="<?php echo e($article->slug); ?>"><i class="fab fa-facebook text-primary"></i> Facebook</a></li>
              <li><a class="dropdown-item copy-link" href="#" data-ga-event="share_article" data-ga-share-method="copy_link" data-ga-article-slug="<?php echo e($article->slug); ?>"><i class="fas fa-link"></i> Salin Link</a></li>
            </ul>
          </div>
        </div>

        
        <div class="content mb-4"><?php echo $article->content; ?></div>

        
        <div>
          <?php $__empty_1 = true; $__currentLoopData = $article->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e($t->link); ?>" target="_blank" class="tag-link"><?php echo e($t->nama_tags); ?></a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <span class="text-muted">Tidak ada tag.</span>
          <?php endif; ?>
        </div>

      </div>
    </div>

    
    <div class="col-lg-4">
      
      <h5 id="latest-heading" class="mb-3">Artikel Terbaru</h5>

      <div class="latest-wrapper">
        <?php $__currentLoopData = $latest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(url('artikel/'.$l['slug'])); ?>" class="text-decoration-none text-dark"
            data-ga-event="open_article" data-ga-article-slug="<?php echo e($l['slug']); ?>" data-ga-source="article_sidebar">
            <div class="latest-card mb-3">
              <div class="latest-thumb">
                <img src="<?php echo e($l['thumb']); ?>" alt="thumb" loading="lazy">
              </div>

              <div class="latest-info">
                <small class="fw-semibold d-block"><?php echo e(Str::limit($l['title'],60)); ?></small>

                
                <a  href="#"
                    class="badge badge-cat-sm kategori-filter mt-1"
                    data-cat="<?php echo e($l['kat_id'] ?? ''); ?>"
                    data-cat-name="<?php echo e($l['kategori']); ?>">
                    <i class="fas fa-folder-open me-1"></i><?php echo e($l['kategori']); ?>

                </a>
              </div>
            </div>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>


    <?php echo $__env->make('LandingPageKilau.Article.komentar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('LandingPageKilau.Berita.partials-donasi', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
              <button id="promptDonasiYa" class="btn btn-primary" data-ga-event="click_donation_prompt" data-ga-source="article_prompt">Ya, Donasi</button>
              <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Nanti saja</button>
            </div>
          </div>
        </div>
      </div>
    </div>

     <?php echo $__env->make('LandingPageKilau.Berita.campaign', [
                    'campaigns' => $campaigns
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  </div>
</div>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('scripts'); ?>

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
  const likeUrl = "<?php echo e(route('lp.article.like', $article->slug)); ?>";
  $('#likeBtn').on('click',function(){
      if($(this).prop('disabled')) return;
      $.post(likeUrl,{_token:'<?php echo e(csrf_token()); ?>'},res=>{
          $('#likeCount').text(res.likes);
          $('#likeIcon').removeClass('text-muted').addClass('text-danger');
          $(this).prop('disabled',true);
      });
  });

  /* ---------- share ---------- */
  const pageUrl  = encodeURIComponent("<?php echo e(url()->current()); ?>");
  const pageText = encodeURIComponent("<?php echo e($article->title); ?>");

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
      navigator.clipboard.writeText("<?php echo e(url()->current()); ?>")
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
    return `<a href="<?php echo e(url('artikel')); ?>/${a.slug}" class="text-decoration-none text-dark" data-ga-event="open_article" data-ga-article-slug="${a.slug}" data-ga-source="article_sidebar">
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
    $.get("<?php echo e(url('artikel/sidebar-latest')); ?>/"+catId,res=>{
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\LandingPageKilau\Article\show.blade.php ENDPATH**/ ?>