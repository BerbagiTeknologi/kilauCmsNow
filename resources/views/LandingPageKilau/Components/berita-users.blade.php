{{-- resources/views/berita-users.blade.php --}}
@extends('App.master')

@section('style')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    /* —— KARTU ——————————————————————————————— */
    #berita-container{
        display:flex; gap:1.25rem; overflow-x:auto; scroll-behavior:smooth;
        padding-bottom:.75rem; margin-bottom:1rem;
    }
    #berita-container::-webkit-scrollbar{height:.35rem}
    #berita-container::-webkit-scrollbar-thumb{background:#0d6efd;border-radius:5px}

    .news-card{flex:0 0 320px; background:#fff; border-radius:.8rem; overflow:hidden;
               box-shadow:0 0 .75rem rgba(0,0,0,.08); display:flex; flex-direction:column;}
    .news-card img{object-fit:cover; height:220px}
    .news-card .title{font-size:1.05rem;font-weight:600;min-height:48px}
    .news-card .meta{font-size:.8rem;color:#6c757d}
</style>
@endsection



@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Berita Kilau Indonesia</h4>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createBeritaModal">
            <i class="fas fa-plus me-1"></i> Tambah Berita
        </button>
    </div>

    {{-- Input pencarian (opsional) --}}
    <input type="text" id="search" class="form-control mb-4" placeholder="Cari judul…">

    {{-- Daftar kartu --}}
    <div id="berita-container">
        <div class="w-100 text-center py-4">Memuat data…</div>
    </div>

    {{-- Pagination --}}
    <nav aria-label="page">
        <ul id="pagination" class="pagination justify-content-center"></ul>
    </nav>
</div>



{{-- ————————————————  MODAL TAMBAH  ———————————————— --}}
<div class="modal fade" id="createBeritaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form id="create-berita-form" enctype="multipart/form-data">@csrf
      <div class="modal-header">
          <h5 class="modal-title">Tambah Berita</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
          <div class="mb-3">
              <label class="form-label">Judul</label>
              <input type="text" name="judul" id="judul-input" class="form-control" required>
              <small id="seo-title-analysis" class="form-text text-muted"></small>
          </div>

          <div class="mb-3">
              <label class="form-label">Konten</label>
              <textarea name="konten" id="konten-create" class="form-control" style="display:none"></textarea>
              <small id="seo-content-analysis" class="form-text text-muted"></small>
          </div>

          <div class="row">
              <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal</label>
                  <input type="date" name="tanggal" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                  <label class="form-label">Kategori Berita</label>
                  <select name="id_kategori_berita" id="id_kategori_berita" class="form-select" required>
                      <option value="">Pilih Kategori</option>
                  </select>
              </div>
          </div>

          <div class="mb-3">
              <label class="form-label">Tags</label>
              <div id="tags-container">
                  <div class="row tag-item g-2 mb-2">
                      <div class="col-md-6"><input type="text" name="tags[0][nama]" class="form-control" placeholder="Nama Tag"></div>
                      <div class="col-md-5"><input type="text" name="tags[0][link]" class="form-control" placeholder="Link Tag"></div>
                      <div class="col-md-1"></div>
                  </div>
              </div>
              <button type="button" id="add-tag-btn" class="btn btn-outline-secondary btn-sm">Tambah Tag</button>
          </div>

          <div class="row g-2">
              <div class="col-md-4"><label class="form-label">Foto 1</label><input type="file" name="foto"  class="form-control"></div>
              <div class="col-md-4"><label class="form-label">Foto 2</label><input type="file" name="foto2" class="form-control"></div>
              <div class="col-md-4"><label class="form-label">Foto 3</label><input type="file" name="foto3" class="form-control"></div>
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

/* ——— CONFIG ——— */
const API      = 'https://berbagipendidikan.org';
const token    = localStorage.getItem('user_token');   // hanya untuk POST create
let   page     = 1;
const perPage  = 6;

/* ——— QUILL (modal) ——— */
$('#konten-create').after('<div id="konten-create-editor" style="height:200px"></div>').hide();
const ql = new Quill('#konten-create-editor',{
    theme:'snow', placeholder:'Tulis konten di sini…',
    modules:{toolbar:[['bold','italic','underline'],['link','image'],[{list:'bullet'}],['clean']]}
});

/* ——— ANALISIS SEO sederhana ——— */
$('#judul-input').on('input',function(){
    const n = this.value.trim().length;
    $('#seo-title-analysis').text(
        n<50?`Terlalu pendek (${n}/50)` : n>70?`Terlalu panjang (${n})` : 'Judul optimal'
    );
});
ql.on('text-change',()=>{
    const words = ql.getText().trim().split(/\s+/).filter(Boolean).length;
    $('#seo-content-analysis').text(words<300?`Konten ${words}/300 kata`:`Konten cukup (${words})`);
});

/* ——— TAG dinamis ——— */
let tagIdx = 1;
$('#add-tag-btn').on('click',()=>{
  $('#tags-container').append(`
    <div class="row tag-item g-2 mb-2">
      <div class="col-md-6"><input name="tags[${tagIdx}][nama]" class="form-control" placeholder="Nama Tag"></div>
      <div class="col-md-5"><input name="tags[${tagIdx}][link]" class="form-control" placeholder="Link Tag"></div>
      <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm remove-tag">&times;</button></div>
    </div>`); tagIdx++;
});
$('#tags-container').on('click','.remove-tag',function(){ $(this).closest('.tag-item').remove();});

/* ——— isi dropdown kategori saat modal buka ——— */
$('#createBeritaModal').on('shown.bs.modal',()=>{
  $('#id_kategori_berita').html('<option>Memuat…</option>');
  $.get(`${API}/api/kategori-berita`,r=>{
      let opt='<option value="">Pilih Kategori</option>';
      if(r.success) r.data.data.forEach(k=>opt+=`<option value="${k.id}">${k.name_kategori}</option>`);
      $('#id_kategori_berita').html(opt);
  }).fail(()=>$('#id_kategori_berita').html('<option>Gagal memuat</option>'));
});

/* ——— RENDER KARTU LIST BERITA ——— */
function formatTgl(s){return new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});}
function loadBerita(p=1,q=''){
  $('#berita-container').html('<div class="w-100 text-center py-4">Memuat…</div>');
  $.get(`${API}/api/berita`,{page:p,per_page:perPage,search:q})
   .done(r=>{
      const list=r.data||[]; const base=API; const noImg="{{ asset('assets_admin/img/noimage.jpg') }}";
      if(!list.length){$('#berita-container').html('<div class="w-100 text-center py-4">Belum ada berita.</div>');return;}
      let html='';
      list.forEach((b,i)=>{
          if(b.status_berita==='Tidak Aktif') return;
          const f1=b.foto?base+b.foto:noImg, f2=b.foto2?base+b.foto2:f1, f3=b.foto3?base+b.foto3:f1;
          const slug=`/berita/${b.judul.replace(/\s+/g,'-')}`;
          html+=`
             <article class="news-card wow fadeIn" data-wow-delay="${(i+1)*.1}s">
               <div id="c${b.id}" class="carousel slide" data-bs-ride="carousel">
                 <div class="carousel-inner">
                    <div class="carousel-item active"><img src="${f1}" class="d-block w-100" alt=""></div>
                    <div class="carousel-item"><img src="${f2}" class="d-block w-100" alt=""></div>
                    <div class="carousel-item"><img src="${f3}" class="d-block w-100" alt=""></div>
                 </div>
                 <button class="carousel-control-prev" type="button" data-bs-target="#c${b.id}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                 </button>
                 <button class="carousel-control-next" type="button" data-bs-target="#c${b.id}" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                 </button>
               </div>
               <div class="p-3 d-flex flex-column h-100">
                   <a href="${slug}" class="title text-dark text-decoration-none mb-2">${b.judul}</a>
                   <div class="meta mt-auto">Kilau Indonesia · ${formatTgl(b.tanggal)}</div>
               </div>
             </article>`;
      });
      $('#berita-container').html(html);
      renderPag(r.pagination);
   })
   .fail(()=>$('#berita-container').html('<div class="w-100 text-center text-danger py-4">Gagal memuat.</div>'));
}

/* ——— PAGINATION ——— */
function renderPag(p){ if(!p) return $('#pagination').html(''); let h='';
  const btn=(pg,l,a)=>`<li class="page-item ${a?'active':''}"><a class="page-link" href="#" data-p="${pg}">${l}</a></li>`;
  if(p.current_page>1) h+=btn(p.current_page-1,'«');
  let s=Math.max(1,p.current_page-2), e=Math.min(p.last_page,s+4);
  for(let i=s;i<=e;i++) h+=btn(i,i,i===p.current_page);
  if(p.current_page<p.last_page) h+=btn(p.current_page+1,'»');
  $('#pagination').html(h);
}
$(document).on('click','#pagination a',e=>{
  e.preventDefault(); page=$(e.target).data('p'); loadBerita(page,$('#search').val());
});

/* ——— FILTER JUDUL (optional) ——— */
$('#search').on('keyup',()=>loadBerita(1,$('#search').val()));

/* ——— SUBMIT TAMBAH (token wajib) ——— */
$('#create-berita-form').on('submit',function(e){
  e.preventDefault();
  if(!token) return Swal.fire("Harus login","", "warning");
  $('#konten-create').val(ql.root.innerHTML.trim());
  $.ajax({
     url:`${API}/api/berita-create`,
     method:'POST',
     headers:{Authorization:`Bearer ${token}`},
     data:new FormData(this),
     processData:false,contentType:false,
     success:()=>{
        Swal.fire("Berhasil","Berita ditambahkan","success");
        $('#createBeritaModal').modal('hide'); this.reset(); ql.setContents([]);
        loadBerita(page);
     },
     error:x=>{
        const msg=x.status===422?Object.values(x.responseJSON.errors)[0][0]:"Gagal menyimpan";
        Swal.fire("Error",msg,"error");
     }
  });
});

/* ——— INISIASI PERTAMA ——— */
loadBerita();

});
</script>
@endsection
