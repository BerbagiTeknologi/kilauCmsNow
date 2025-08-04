{{-- resources/views/LandingPageKilau/Components/article-users.blade.php --}}
@extends('App.master')

@section('style')
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
<style>
    #artikel-container{display:flex;gap:1.25rem;overflow-x:auto;scroll-behavior:smooth;
                       padding-bottom:.75rem;margin-bottom:1rem;}
    #artikel-container::-webkit-scrollbar{height:.35rem}
    #artikel-container::-webkit-scrollbar-thumb{background:#0d6efd;border-radius:5px}
    .art-card{flex:0 0 320px;background:#fff;border-radius:.8rem;overflow:hidden;
              box-shadow:0 0 .75rem rgba(0,0,0,.08);display:flex;flex-direction:column;}
    .art-card img{object-fit:cover;height:220px}
    .art-card .title{font-size:1.05rem;font-weight:600;min-height:48px}
    .art-card .meta{font-size:.8rem;color:#6c757d}
</style>
@endsection


@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Artikel Kilau Indonesia</h4>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createArticleModal">
            <i class="fas fa-plus me-1"></i> Tambah Artikel
        </button>
    </div>

    <input type="text" id="search" class="form-control mb-4" placeholder="Cari judul…">

    <div id="artikel-container">
        <div class="w-100 text-center py-4">Memuat data…</div>
    </div>

    <nav aria-label="page">
        <ul id="pagination" class="pagination justify-content-center"></ul>
    </nav>
</div>

{{-- — MODAL TAMBAH — --}}
<div class="modal fade" id="createArticleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form id="create-article-form" enctype="multipart/form-data">@csrf
      <div class="modal-header">
          <h5 class="modal-title">Tambah Artikel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
          <div class="mb-3">
              <label class="form-label">Judul</label>
              <input type="text" name="judul" id="judul-input" class="form-control" required>
              <small id="seo-title-analysis" class="form-text text-muted"></small>
          </div>

          <div class="mb-3">
              <label class="form-label">Nama Penulis</label>
              <input type="text" id="author-input" name="author" class="form-control">
          </div>

          <div class="mb-3 d-none">
            <label class="form-label">Foto Penulis</label>
            <input type="text" id="photo-author-input" name="photo_author" class="form-control">
        </div>

          <div class="mb-3">
              <label class="form-label">Konten</label>
              <textarea name="konten" id="konten-create" class="form-control d-none"></textarea>
              <small id="seo-content-analysis" class="form-text text-muted"></small>
          </div>

          <div class="row">
              <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal</label>
                  <input type="date" name="tanggal" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                  <label class="form-label">Kategori</label>
                  <select name="kategori_article_id" id="kategori_article_id" class="form-select" required>
                      <option value="">Pilih Kategori</option>
                  </select>
              </div>
          </div>

          <div class="mb-3">
              <label class="form-label">Tags</label>
              <div id="tags-container">
                  <div class="row tag-item g-2 mb-2">
                      <div class="col-md-6"><input name="tags[0][nama]" class="form-control" placeholder="Nama Tag"></div>
                      <div class="col-md-5"><input name="tags[0][link]" class="form-control" placeholder="Link Tag"></div>
                      <div class="col-md-1"></div>
                  </div>
              </div>
              <button type="button" id="add-tag-btn" class="btn btn-outline-secondary btn-sm">Tambah Tag</button>
          </div>

          <div class="row g-2">
              <div class="col-md-4"><label class="form-label">Foto 1</label><input type="file" name="photo[]"  class="form-control"></div>
              <div class="col-md-4"><label class="form-label">Foto 2</label><input type="file" name="photo[]" class="form-control"></div>
              <div class="col-md-4"><label class="form-label">Foto 3</label><input type="file" name="photo[]" class="form-control"></div>
          </div>
      </div>

      <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div></div>
</div>
@endsection


@section('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){

/* — CONSTANTS — */
const LIST_URL   = "{{ route('lp.article.external.list') }}";
const STORE_URL  = "{{ route('lp.article.external.store') }}";
const CAT_URL    = "{{ route('lp.article.external.kategori') }}";
const IMG_URL    = "{{ route('lp.article.external.uploadImage') }}";
const AUTHOR_KEY = 'user_name';
const PHOTO_KEY  = 'user_photo';
const perPage    = 6;
let   page       = 1;

/* -- helper untuk domain Kilau yang kadang double-slash dll. -- */
function fixKilauUrl(u){
    if(!u) return '';
    if(u.startsWith('http')) return u;
    return 'https://kilauindonesia.id'+(u.startsWith('/')?u:'/'+u);
}

/* — QUILL — */
$('#konten-create').after('<div id="konten-create-editor" style="height:200px"></div>').hide();
const ql = new Quill('#konten-create-editor',{
  theme:'snow',
  placeholder:'Tulis konten di sini…',
  modules:{
     toolbar:{
        container: [['bold','italic','underline'],['link','image'],[{list:'bullet'}],['clean']],
        handlers:{
           image: ()=>uploadImage(ql)      // ← pasang handler custom
        }
     }
  }
});

/* —— UPLOAD dari toolbar —— */
function uploadImage(q){
  const input=document.createElement('input');
  input.type='file'; input.accept='image/*'; input.click();
  input.onchange=()=>{
     const file=input.files[0];
     if(!file) return;
     if(file.size>2*1024*1024) return Swal.fire('Ukuran maksimal 2 MB');
     if(!['image/jpeg','image/png','image/jpg','image/gif'].includes(file.type))
        return Swal.fire('Format harus JPG/PNG/GIF');

     const fd=new FormData(); fd.append('image',file);
     fetch(IMG_URL,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:fd})
       .then(r=>r.json())
       .then(r=>{
          if(r.success){
             const range=q.getSelection(true);
             q.insertEmbed(range.index,'image',r.image_url,'user');
          }else Swal.fire('Gagal upload');
       })
       .catch(()=>Swal.fire('Gagal upload'));
  };
}

/* —— isi author & foto otomatis saat modal buka —— */
$('#createArticleModal').on('shown.bs.modal',()=>{
   const author = localStorage.getItem(AUTHOR_KEY) || '';
   const raw    = localStorage.getItem(PHOTO_KEY)  || '';
   const photo  = fixKilauUrl(raw);
   if(photo!==raw) localStorage.setItem(PHOTO_KEY,photo);

   $('#author-input').val(author);
   $('#photo-author-input').val(photo);

   loadKategori();     // muat dropdown setiap kali modal dibuka
});

/* — SEO hint — */
$('#judul-input').on('input',function(){
  const n=this.value.trim().length;
  $('#seo-title-analysis').text(n<50?`Terlalu pendek (${n}/50)`:n>70?`Terlalu panjang (${n})`:'Judul optimal');
});
ql.on('text-change',()=>{
  const w=ql.getText().trim().split(/\s+/).filter(Boolean).length;
  $('#seo-content-analysis').text(w<300?`Konten ${w}/300 kata`:`Konten cukup (${w})`);
});

/* — Tag dinamis — */
let tagIdx=1;
$('#add-tag-btn').on('click',()=>{
  $('#tags-container').append(`
    <div class="row tag-item g-2 mb-2">
      <div class="col-md-6"><input name="tags[${tagIdx}][nama]" class="form-control" placeholder="Nama Tag"></div>
      <div class="col-md-5"><input name="tags[${tagIdx}][link]" class="form-control" placeholder="Link Tag"></div>
      <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm remove-tag">&times;</button></div>
    </div>`); tagIdx++;
});
$('#tags-container').on('click','.remove-tag',function(){$(this).closest('.tag-item').remove();});

/* — Dropdown kategori (AJAX local / API) — */
function loadKategori(){
  $('#kategori_article_id').html('<option>Memuat…</option>');
  $.get(CAT_URL)                                      // ← gunakan helper
   .done(r=>{
      let opt='<option value="">Pilih Kategori</option>';
      (r.data||[]).forEach(k=>opt+=`<option value="${k.id}">${k.name_kategori}</option>`);
      $('#kategori_article_id').html(opt);
   })
   .fail(x=>{
      console.warn('Kategori error', x);
      $('#kategori_article_id').html('<option>Gagal memuat</option>');
   });
}
$('#createArticleModal').on('shown.bs.modal',loadKategori);

/* — Helpers — */
function formatTgl(s){return new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});}

/* — Render list — */
function loadArtikel(p=1,q=''){
  $('#artikel-container').html('<div class="w-100 text-center py-4">Memuat…</div>');
  $.get(LIST_URL,{page:p,per_page:perPage,search:q})
   .done(r=>{
      const list=r.data||[], noImg="{{ asset('assets_admin/img/noimage.jpg') }}";
      if(!list.length){$('#artikel-container').html('<div class="w-100 text-center py-4">Belum ada artikel.</div>');return;}
      let html='';
      list.forEach((a,i)=>{
        if(a.status_artikel==='Tidak Aktif') return;
        const imgs=a.photos.length?a.photos:[noImg];
        const slug=`/artikel/${a.slug}`;
        html+=`
        <article class="art-card wow fadeIn" data-wow-delay="${(i+1)*.1}s">
          <div id="c${a.id}" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              ${imgs.map((x,j)=>`<div class="carousel-item ${!j?'active':''}"><img src="${x}" class="d-block w-100"></div>`).join('')}
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#c${a.id}" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#c${a.id}" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          </div>
          <div class="p-3 d-flex flex-column h-100">
            <a href="${slug}" class="title text-dark text-decoration-none mb-2">${a.title}</a>
            <div class="meta mt-auto">${a.author??'Anon'} · ${formatTgl(a.tanggal)}</div>
          </div>
        </article>`;
      });
      $('#artikel-container').html(html);
      renderPag(r.pagination);
   })
   .fail(()=>$('#artikel-container').html('<div class="w-100 text-center text-danger py-4">Gagal memuat.</div>'));
}

/* — Pagination UI — */
function renderPag(p){if(!p)return $('#pagination').html('');let h='';
const btn=(pg,l,a)=>`<li class="page-item ${a?'active':''}"><a class="page-link" href="#" data-p="${pg}">${l}</a></li>`;
if(p.current_page>1)h+=btn(p.current_page-1,'«');
let s=Math.max(1,p.current_page-2),e=Math.min(p.last_page,s+4);
for(let i=s;i<=e;i++)h+=btn(i,i,i===p.current_page);
if(p.current_page<p.last_page)h+=btn(p.current_page+1,'»');
$('#pagination').html(h);}
$(document).on('click','#pagination a',e=>{
  e.preventDefault();page=$(e.target).data('p');loadArtikel(page,$('#search').val());
});

/* — Live search — */
$('#search').on('keyup',()=>loadArtikel(1,$('#search').val()));

/* — Submit — */
$('#create-article-form').on('submit',function(e){
  e.preventDefault();
  $('#konten-create').val(ql.root.innerHTML.trim());
  $.ajax({
    url: STORE_URL,
    method:'POST',
    data:new FormData(this),
    processData:false,contentType:false,
    success:()=>{
      Swal.fire("Berhasil","Artikel ditambahkan","success");
      $('#createArticleModal').modal('hide');this.reset();ql.setContents([]);
      loadArtikel(page);
    },
    error:x=>{
      const msg=x.status===422?Object.values(x.responseJSON.errors)[0][0]:"Gagal menyimpan";
      Swal.fire("Error",msg,"error");
    }
  });
});

/* — First load — */
loadArtikel();

});
</script>
@endsection
