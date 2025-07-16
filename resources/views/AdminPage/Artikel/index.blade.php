@extends('AdminPage.App.master')

@section('style')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        #createArticleModal .modal-dialog { max-width: 800px; width: 90%; }
        .modal-header { color:#000; border-radius:5px 5px 0 0; }
        .modal-title  { font-size:18px; }
        .modal-footer .btn { padding:10px 15px; font-size:14px; }
        .img-thumb { max-width:120px; max-height:120px; border-radius:5px; }
        .ql-editor img { display:block; width:100%; height:auto; }
        #confirmDeleteModal .modal-dialog { max-width: 500px; } /* ubah jika perlu */
        #confirmDeleteModal .modal-content { border-radius: 8px; }

       .thumb-box{
            position:relative;
            margin:6px;                    /* jarak antar foto */
            display:inline-block;
        }
        .thumb-box img{
            width:150px;                   /* ← ganti di sini jika ingin lebih besar */
            height:auto;                   /* proporsi tetap */
            object-fit:cover;
            border:1px solid #ddd;
            border-radius:8px;
            box-shadow:0 1px 2px rgba(0,0,0,.08);
        }
        .thumb-box .remove-photo{
            position:absolute;
            top:4px;left:4px;
            width:22px;height:22px;
            line-height:18px;
            font-size:16px;
            border-radius:50%;
            background:rgba(0,0,0,.65);
            color:#fff;
            text-align:center;
            cursor:pointer;
        }

        .tag-chip{
            background:#eef2ff;border:1px solid #c7d2fe;color:#374151;
            border-radius:18px;padding:2px 10px 2px 12px;display:inline-flex;
            align-items:center;gap:6px;font-size:.85rem
        }
        .tag-chip .del{cursor:pointer;font-weight:bold;line-height:10px}
    </style>
@endsection

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    {{-- ========= HEADER ========= --}}
                    <div class="card-header">
                        <div class="d-flex align-items-center w-100">
                            <h4 class="card-title mb-0">Data Artikel</h4>

                            <form id="search-form" class="ms-auto d-flex">
                                <input type="text" id="search" name="search"
                                       class="form-control" placeholder="Cari artikel...">
                            </form>

                            <button class="btn btn-primary btn-round ms-2"
                                    data-toggle="modal" data-target="#createArticleModal">
                                <i class="fa fa-plus"></i> Tambah Artikel
                            </button>
                        </div>
                    </div>

                    {{-- ========= BODY ========= --}}
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="article-table" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul</th>
                                        <th>Penulis</th>
                                        <th>Tanggal Pembuatan Artikel</th>
                                        <th>Status Artikel</th>
                                        <th>Kategori Article</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="article-body">
                                    {{-- data di-isi Ajax --}}
                                </tbody>
                            </table>
                           @include('AdminPage.Artikel.show')
                        </div>

                        {{-- PAGINATION --}}
                        <div class="pagination-container">
                            <ul id="pagination" class="pagination justify-content-end"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Data --}}
    <div class="modal fade" id="createArticleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="create-article-form" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Artikel</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        {{-- Judul --}}
                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        {{-- Penulis (opsional) --}}
                       <div class="form-group">
                            <label>Penulis</label>
                            <input type="text"
                                name="author"
                                id="author-input"
                                class="form-control"
                                readonly>         {{-- <— readonly --}}
                        </div>

                           <div class="form-group">
                                <label>Konten</label>
                                <input type="hidden" name="content" id="content-input">
                                <div id="editor-create" style="min-height:260px"></div>
                            </div>

                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="kategori_article_id" id="kategori-select"
                                        class="form-control" required>
                                    <!-- opsi di‐isi via AJAX ketika modal dibuka -->
                                </select>
                            </div>
                        
                            {{-- =====================  TAGS  ===================== --}}
                            <div class="form-group">
                                <label>Tags <small class="text-muted">(bisa lebih dari satu)</small></label>

                                <table class="table table-sm mb-2" id="tags-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:45%">Nama Tag</th>
                                            <th style="width:45%">Link</th>
                                            <th style="width:10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                                <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-tag">
                                    <i class="fa fa-plus"></i> Tambah Tag
                                </button>
                            </div>

                        <div class="form-group">
                            <label>Foto Artikel (boleh lebih dari satu, prefer landscape)</label>
                            <input type="file"
                                name="photo[]"
                                id="photo-input"
                                class="form-control"
                                accept="image/*"
                                multiple>   {{-- <-- multiple! --}}
                        </div>

                        {{-- Preview thumbnail --}}
                        <div id="photo-preview" class="d-flex flex-wrap gap-2"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========== MODAL EDIT ========= --}}
    <div class="modal fade" id="editArticleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
        <form id="edit-article-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-header">
                <h5 class="modal-title">Edit Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>

                <div class="modal-body">

                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="title" id="edit-title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Penulis</label>
                    <input type="text" name="author" id="edit-author" class="form-control">
                </div>

                <div class="form-group">
                    <label>Konten</label>
                    <div id="editor-edit" style="height:250px;"></div>
                    <input type="hidden" name="content" id="edit-content">
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori_article_id"
                            id="edit-kategori-select"
                            class="form-control" required></select>
                </div>

                <div class="form-group">
                    <label>Tags <small class="text-muted">(bisa lebih dari satu)</small></label>

                    <table class="table table-sm mb-2" id="edit-tags-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:45%">Nama Tag</th>
                                <th style="width:45%">Link</th>
                                <th style="width:10%"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn-edit-add-tag">
                        <i class="fa fa-plus"></i> Tambah Tag
                    </button>
                </div>

                <div class="form-group">
                    <label>Tambah Foto (opsional)</label>
                    <input type="file" id="edit-photo-input"
                        name="photo[]" class="form-control" accept="image/*" multiple>
                </div>

                {{-- preview lama + baru --}}
                <div id="edit-photo-preview" class="d-flex flex-wrap gap-2"></div>
                </div>

                <div class="modal-footer">
                <button type="button" class="btn btn-secondary force-modal-close">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui</button>
                </div>
            </form>
            </div>
        </div>
    </div>


</div>
 
@endsection

@section('scripts')
    {{-- Quill --}}
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    {{-- SweetAlert2 sudah dimuat di master.blade --}}

<script>
    /* --------------------------------------------------
    GLOBAL CSRF HEADER (sekali saja di layout)
    -------------------------------------------------- */
    $.ajaxSetup({
        headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}
    });

    /* -----------------------------------------------------------------
    1. Isi otomatis kolom "author" saat modal CREATE dibuka
    ----------------------------------------------------------------- */
    const AUTHOR_KEY = 'user_name';        // kunci di localStorage

    const katURL = "{{ url('admin/article-kilau/kategori-article/list') }}";

    $('#createArticleModal').on('shown.bs.modal', () => {
        const author = localStorage.getItem(AUTHOR_KEY) || '';
        $('#author-input').val(author);

          /* -------- muat kategori -------- */
        $.get(katURL, { per_page: 1000 }, res => {
            const sel = $('#kategori-select').empty();
            res.data                      // ambil hanya yang “Aktif”
            .filter(k => k.status_kategori_article === 'Aktif')
            .forEach(k => sel.append(
                `<option value="${k.id}">${k.name_kategori}</option>`
            ));
        });
    });

    /* --------------------------------------------------
    QUILL untuk form create
    -------------------------------------------------- */
    const quillCreate = new Quill('#editor-create',{
        theme:'snow',
        placeholder:'Tulis isi artikel…',
        modules:{
            toolbar:{
                container:[
                    [{'header':[1,2,false]}],
                    ['bold','italic','underline'],
                    ['link','blockquote','code-block','image'],
                    [{'list':'ordered'},{'list':'bullet'}],
                    [{'align':[]}],
                    ['clean']
                ],
                handlers:{
                    image:()=>uploadImage(quillCreate)          // handler ⬇️
                }
            }
        }
    });

    /* Upload gambar dari toolbar Quill */
    function uploadImage(q){
        const input=document.createElement('input');
        input.type='file'; input.accept='image/*'; input.click();

        input.onchange=()=>{
            const file=input.files[0]; if(!file) return;
            if(file.size>2*1024*1024) return Swal.fire('Ukuran maksimal 2 MB');
            const ok=['image/jpeg','image/png','image/jpg','image/gif'];
            if(!ok.includes(file.type)) return Swal.fire('Format harus JPG/PNG/GIF');

            const fd=new FormData(); fd.append('image',file);

            fetch("{{ url('admin/upload-image-article') }}",{
                method:'POST',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body:fd
            })
            .then(r=>r.json())
            .then(r=>{
                if(r.success){
                    const range=q.getSelection(true);
                    q.insertEmbed(range.index,'image',r.image_url,'user');
                }else{ Swal.fire('Gagal upload'); }
            })
            .catch(()=>Swal.fire('Gagal upload'));
        };
    }

    /* --------------------------------------------------
    Foto “utama” (di luar konten) – pratinjau & hapus
    -------------------------------------------------- */
    let selectedFiles=[];

    /* pilih file */
    $('#photo-input').on('change',function(){
        [...this.files].forEach(f=>{
            const dup=selectedFiles.some(x=>x.name===f.name&&x.size===f.size&&x.lastModified===f.lastModified);
            if(!dup) selectedFiles.push(f);
        });
        syncFileInput(this); renderThumb();
    });

    /* klik × */
    $(document).on('click','.remove-photo',function(){
        const i=$(this).parent().data('idx');
        selectedFiles.splice(i,1);
        syncFileInput($('#photo-input')[0]); renderThumb();
    });

    /* helper */
    function syncFileInput(inp){
        const dt=new DataTransfer();
        selectedFiles.forEach(f=>dt.items.add(f));
        inp.files=dt.files;
    }

    function renderThumb(){
        const box=$('#photo-preview').empty();
        selectedFiles.forEach((f,i)=>{
            if(!f.type.startsWith('image/'))return;
            const fr=new FileReader();
            fr.onload=e=>{
                $('<div>',{class:'thumb-box','data-idx':i,style:'position:relative;display:inline-block;margin:6px'})
                .append(
                    $('<img>',{src:e.target.result,
                            css:{width:'150px',height:'auto',objectFit:'cover',
                                    border:'1px solid #ddd',borderRadius:'6px'}}),
                    $('<span>',{html:'&times;',class:'remove-photo',
                                css:{position:'absolute',top:'4px',left:'4px',
                                    width:'22px',height:'22px',lineHeight:'18px',
                                    background:'rgba(0,0,0,.65)',color:'#fff',
                                    borderRadius:'50%',textAlign:'center',
                                    cursor:'pointer',fontSize:'15px'}})
                ).appendTo(box);
            };
            fr.readAsDataURL(f);
        });
    }

    /* ==================================================
   TAGS – tambah / hapus baris input dinamis
   ================================================== */
    let tagIdx = 0;

    $('#btn-add-tag').on('click', () => {
        $('#tags-table tbody').append(/*html*/`
            <tr>
                <td>
                    <input type="text"
                        name="tags[${tagIdx}][nama_tags]"
                        class="form-control" required>
                </td>
                <td>
                    <input type="text"
                        name="tags[${tagIdx}][link]"
                        class="form-control" placeholder="https://…" required>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-tag">
                        &times;
                    </button>
                </td>
            </tr>
        `);
        tagIdx++;
    });

    $(document).on('click', '.btn-remove-tag', function () {
        $(this).closest('tr').remove();
    });

    /* --------------------------------------------------
    SUBMIT CREATE
    -------------------------------------------------- */
    $('#create-article-form').on('submit', function (e) {
        e.preventDefault();

        $('#content-input').val(quillCreate.root.innerHTML.trim());
        const fd = new FormData(this);

        // appendTags(fd); 

        $.ajax({
            url : "{{ url('admin/article-kilau/article/create') }}",
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: res => {

                /* tampilkan alert dulu */
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text : res.message,
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => {
                    /* setelah alert hilang ➜ reload halaman */
                    location.reload();
                });

                /* reset form/modal (opsional, karena reload) */
                $('#createArticleModal').modal('hide');
                this.reset();
                quillCreate.setContents([]);
                selectedFiles = [];
                renderThumb();
            },
            error: xhr => {
                const msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan.';
                Swal.fire('Gagal', msg, 'error');
            }
        });
    });
</script>

<script>
    /* =====================================================
    GLOBAL CSRF HEADER (satu kali saja di layout)
    ===================================================== */
    $.ajaxSetup({
        headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}
    });

    $(function(){

    /* =====================================================
    QUILL – editor untuk modal *EDIT*
    ===================================================== */
    const quillEdit = new Quill('#editor-edit',{
        theme:'snow',
        placeholder:'Edit konten…',
        modules:{
            toolbar:{
                container:[
                    [{'header':[1,2,false]}],
                    ['bold','italic','underline'],
                    ['link','blockquote','code-block','image'],   // ← tombol Gambar
                    [{'list':'ordered'},{'list':'bullet'}],
                    [{'align':[]}],
                    ['clean']
                ],
                /* handler “image” */
                handlers:{
                    image:()=>uploadImage(quillEdit)
                }
            }
        }
    });

    /* fungsi upload gambar (dipakai create & edit) */
    function uploadImage(quill){
        const input=document.createElement('input');
        input.type='file'; input.accept='image/*'; input.click();

        input.onchange=()=>{
            const file=input.files[0]; if(!file) return;
            if(file.size>2*1024*1024) return Swal.fire('Ukuran maksimal 2 MB');
            const ok=['image/jpeg','image/png','image/jpg','image/gif'];
            if(!ok.includes(file.type)) return Swal.fire('Format harus JPG / PNG / GIF');

            const fd=new FormData(); fd.append('image',file);

            /* ← pakai URL, BUKAN route() */
            fetch("{{ url('admin/upload-image-article') }}",{
                method:'POST',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body:fd
            })
            .then(r=>r.json())
            .then(r=>{
                if(r.success){
                    const range=quill.getSelection(true);
                    quill.insertEmbed(range.index,'image',r.image_url,'user');
                }else{
                    Swal.fire('Gagal meng-upload gambar');
                }
            })
            .catch(()=>Swal.fire('Gagal meng-upload gambar'));
        };
    }

    /* =====================================================
    STATE & UTILITAS FOTO THUMBNAIL
    ===================================================== */
    let fotoLama=[],filesBaru=[],currentArticleId=null;

    function rebuildFileList(input){
        const dt=new DataTransfer();
        filesBaru.forEach(f=>dt.items.add(f));
        input.files=dt.files;
    }

    function renderPreview(){
        const box=$('#edit-photo-preview').empty();

        /* foto dari DB */
        fotoLama.forEach((p,i)=>{
            $('<div>',{class:'thumb-box','data-idx':i}).append(
                $('<img>',{src:p.url}),
                $('<span>',{class:'remove-photo',html:'&times;','data-type':'old'})
            ).appendTo(box);
        });

        /* foto baru (belum upload) */
        filesBaru.forEach((f,i)=>{
            if(!f.type.startsWith('image/'))return;
            const r=new FileReader();
            r.onload=e=>{
                $('<div>',{class:'thumb-box','data-idx':i}).append(
                    $('<img>',{src:e.target.result}),
                    $('<span>',{class:'remove-photo',html:'&times;','data-type':'new'})
                ).appendTo(box);
            };
            r.readAsDataURL(f);
        });
    }

    const katURL = "{{ url('admin/article-kilau/kategori-article/list') }}";

    /* =====================================================
    BUKA MODAL EDIT
    ===================================================== */
    $(document).on('click', '.edit-article', function () {
        currentArticleId = $(this).data('id');

        $.get("{{ url('admin/article-kilau/article/show') }}/" + currentArticleId)
        .done(res => {

             $.get(katURL,{per_page:1000}, list => {
                const sel = $('#edit-kategori-select').empty();
                list.data
                    .filter(k=>k.status_kategori_article==='Aktif')
                    .forEach(k=> sel.append(
                        `<option value="${k.id}">${k.name_kategori}</option>`
                    ));
                sel.val(res.kategori_article_id);           // pilih yg aktif
            });
            
            /* -------- isi field dasar -------- */
            $('#edit-id').val(res.id);
            $('#edit-title').val(res.title);
            $('#edit-author').val(res.author ?? '');
            quillEdit.root.innerHTML = res.content;

            /* -------- FOTO lama -------- */
            fotoLama = res.photos;   // [{path,url}, …]
            filesBaru = [];
            $('#edit-photo-input').val('');
            renderPreview();

            /* -------- TAGS -------- */                 // ⟵ TAMBAH
            editTagIdx = 0;                              // reset index
            const tbody = $('#edit-tags-table tbody').empty();

            if (res.tags.length) {
                res.tags.forEach(t => {
                    tbody.append(tagRowHTML(editTagIdx++, t.nama_tags, t.link));
                });
            } else {
                // jika artikel belum punya tag, buat satu baris kosong
                tbody.append(tagRowHTML(editTagIdx++));
            }

            /* tampilkan modal */
            $('#editArticleModal').modal({ backdrop: 'static' }).modal('show');
        })
        .fail(() => Swal.fire('Error', 'Gagal memuat data.', 'error'));
    });


    /* pilih foto tambahan */
    $('#edit-photo-input').on('change',function(){
        filesBaru=[...this.files];
        rebuildFileList(this);
        renderPreview();
    });

    /* hapus thumbnail */
    $(document).on('click','.remove-photo',function(){
        const type=$(this).data('type');      // old | new
        const idx =+$(this).parent().data('idx');

        if(type==='old'){
            const path=fotoLama[idx].path;

            $.ajax({
                url : "{{ url('admin/article-kilau/article/photo') }}/"+currentArticleId,
                type:'DELETE',
                data:{ path },
                success:()=>{
                    fotoLama.splice(idx,1);
                    renderPreview();
                },
                error:()=>Swal.fire('Error','Gagal menghapus foto.','error')
            });

        }else{
            filesBaru.splice(idx,1);
            rebuildFileList($('#edit-photo-input')[0]);
            renderPreview();
        }
    });

    function tagRowHTML(idx, nama = '', link = ''){
        return `
            <tr>
                <td>
                    <input type="text"
                        name="tags[${idx}][nama_tags]"
                        class="form-control"
                        value="${nama}" required>
                </td>
                <td>
                    <input type="text"
                        name="tags[${idx}][link]"
                        class="form-control"
                        value="${link}" required>
                </td>
                <td class="text-center align-middle">
                    <button type="button"
                            class="btn btn-danger btn-sm btn-remove-tag">
                        &times;
                    </button>
                </td>
            </tr>
        `;
    }

    let editTagIdx = 0;

    /* tambah baris baru kosong */
    $('#btn-edit-add-tag').on('click', () => {
        $('#edit-tags-table tbody')
            .append(tagRowHTML(editTagIdx++));
    });

    /* hapus baris (delegated) */
    $(document).on('click', '.btn-remove-tag', function () {
        $(this).closest('tr').remove();
    });


    /* =====================================================
    SUBMIT UPDATE
    ===================================================== */
    $('#edit-article-form').on('submit',function(e){
        e.preventDefault();
        if(!currentArticleId) return;

        $('#edit-content').val(quillEdit.root.innerHTML.trim());

        const fd=new FormData(this);
        fd.append('_method','PUT');

        $.ajax({
            url :"{{ url('admin/article-kilau/article/update') }}/"+currentArticleId,
            type:'POST',
            data:fd,
            processData:false,
            contentType:false,
            success:r=>{
                Swal.fire({icon:'success',title:'Berhasil',text:r.message,
                        timer:1500,showConfirmButton:false})
                    .then(()=>location.reload());
            },
            error:()=>Swal.fire('Error','Gagal memperbarui.','error')
        });
    });

    /* tombol Batal */
    $(document).on('click','.force-modal-close',()=>$('#editArticleModal').modal('hide'));
    $(document).on('click','#editArticleModal .close',       // <— tambahan
                ()=>$('#editArticleModal').modal('hide'));

    });
</script>

<script>
    $(function () {

        const fmtID = new Intl.DateTimeFormat('id-ID', {
            day:   '2-digit',
            month: 'long',
            year:  'numeric'
        });


        const baseToggleUrl = "{{ url('admin/article-kilau/article/status') }}";

        /* ============ STATE ============ */
        const perPage       = 10;
        let   currentPage   = 1;
        let   currentSearch = '';
        let   allData       = [];   

        /* ============ LOAD PERTAMA ============ */
        fetchArticles();

        /* ----- live search ----- */
        $('#search').keyup(function () {
            currentSearch = $(this).val();
            fetchArticles(1, currentSearch);
        });

        /* ---- Tombol delete (SweetAlert confirm) ---- */
        $(document).on('click', '.delete-article', function () {
            const id  = $(this).data('id');
            const row = $(`#row-${id}`);

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text : 'Data yang dihapus tidak dapat dikembalikan!',
                icon : 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor : '#3085d6',
                confirmButtonText : 'Ya, hapus!',
                cancelButtonText  : 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url : "{{ route('deleteArticle') }}",
                        type: 'DELETE',
                        data: { id },
                        success: res => {
                            // hapus baris tabel &/atau refresh list
                            row.remove();
                            Swal.fire('Terhapus!', res.message, 'success');
                        },
                        error: () => Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus.', 'error')
                    });
                }
            });
        });

        // Update Status Approval
        $(document).on('click', '.toggle-status', function () {
            const id = $(this).data('id');

            Swal.fire({
                icon: 'question',
                title: 'Ubah Status?',
                text : 'Artikel akan di-aktif/non-aktif-kan.',
                showCancelButton: true,
                confirmButtonText: 'Ya, ubah'
            }).then(result => {

                if (!result.isConfirmed) return;

                $.ajax({
                    url : `${baseToggleUrl}/${id}`, // ← tambahkan id di runtime
                    type: 'POST',                  // form-urlencoded
                    data: { _method: 'PATCH' },    // spoof menjadi PATCH
                    success: res => {
                        Swal.fire({
                            icon:'success',
                            title:'Berhasil',
                            text : res.message,
                            timer:1500,
                            showConfirmButton:false
                        }).then(() => fetchArticles(currentPage, currentSearch));
                    },
                    error: xhr => {
                        const msg = xhr.responseJSON?.message ?? 'Gagal mengubah status.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });
        });

        /* ============ PAGINATION CLICK (delegasi) ============ */
        $(document).on('click', '.page-number', function (e) {
            e.preventDefault();
            currentPage = $(this).data('page');
            renderCurrentPage();
        });

        /* ============ FETCH & CACHE ============ */
        function fetchArticles(page = 1, search = '') {
            $.get("{{ route('article.list') }}", { search }, function (data) {
                allData      = data;   // array of articles
                currentPage  = page;
                renderCurrentPage();
            }).fail(() => Swal.fire('Error', 'Gagal mengambil data artikel.', 'error'));
        }

        /* ============ RENDER PAGE AKTIF ============ */
        function renderCurrentPage() {
            const start    = (currentPage - 1) * perPage;
            const pageData = allData.slice(start, start + perPage);

            renderTable(pageData, start);
            renderPagination(Math.ceil(allData.length / perPage));
        }

        /* ----- table body ----- */
        function renderTable(data, startIndex) {
            let html = '';
            let no   = startIndex + 1;
            const defaultImg = "{{ asset('assets_admin/img/noimage.jpg') }}";

            data.forEach(a => {
                const foto  = a.photo || defaultImg;
                const badge = a.status_artikel === 'Aktif' ? 'badge-success' : 'badge-danger';
    
                const tgl = fmtID.format(new Date(a.created_at));

                html += `
                    <tr id="row-${a.id}">
                        <td>${no++}</td>
                        <td>${a.title}</td>
                        <td>${a.author ?? '-'}</td>
                        <td>${tgl}</td>
                        <td>
                                <span class="badge ${badge} d-inline-block text-center"
                                    style="min-width:100px; line-height:28px; padding:4px 0;">
                                    ${a.status_artikel}
                                </span>
                            </td>
                         <td>${a.kategori?.name_kategori ?? '-'}</td>
                        <td>
                            <div class="btn-group gap-2">
                                <button class="btn btn-primary btn-sm rounded-circle p-2 show-article"
                                        data-id="${a.id}" title="Detail"> <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-info btn-sm rounded-circle p-2 toggle-status"
                                        data-id="${a.id}" title="Ubah Status">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            <button class="btn btn-warning btn-sm rounded-circle p-2 edit-article"
                                        data-id="${a.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm rounded-circle p-2 delete-article" data-id="${a.id}" title="Hapus"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
            });

            $('#article-body').html(html);
        }

        /* ----- pagination ul ----- */
        function renderPagination(lastPage) {
            let html = '';
            if (lastPage > 1) {
                for (let i = 1; i <= lastPage; i++) {

                    const isActive = i === currentPage ? 'active' : '';
                    const txtWhite = i === currentPage ? 'text-white' : '';

                    html += `
                        <li class="page-item ${isActive}">
                            <a href="#"
                            class="page-link page-number ${txtWhite}"
                            data-page="${i}">
                            ${i}
                            </a>
                        </li>`;
                }
            }
            $('#pagination').html(html);
        }
    });
</script>

<script>
$(function () {

    /* ───────── tombol “mata” ───────── */
    $(document).on('click', '.show-article', function () {
        const id  = $(this).data('id');
        const url = "{{ url('admin/article-kilau/article/show') }}/" + id;

        $.get(url)
          .done(res => {
              $('#modal-judul').text(res.title);
              $('#modal-author').text(res.author ?? '-');
              $('#modal-kategori #kategori-text').text(res.kategori ?? '-'); 

              $('#modal-konten').html(res.content);

            const box = $('#modal-tags').empty();
            if (res.tags.length) {
                res.tags.forEach(t => {
                    box.append(`<a href="${t.link}"
                                    target="_blank"
                                    class="tag-link">${t.nama_tags}</a>`);
                });
            } else {
                box.append('<span class="text-muted">- tidak ada tag -</span>');
            }

              /* -------- carousel gambar -------- */
              const wrap = $('#modal-images').empty();

              /* res.photos kini array objek {path,url} */
              const images = res.photos.length
                  ? res.photos.map(p => p.url)
                  : ['{{ asset('assets_admin/img/noimage.jpg') }}'];

              images.forEach((src, i) => {
                  wrap.append(`
                    <div class="carousel-item ${i === 0 ? 'active' : ''}">
                        <img src="${src}" class="d-block w-100">
                    </div>`);
              });

              $('#showArticleModal').modal('show');
          })
          .fail(() => Swal.fire('Error','Gagal mengambil detail artikel.','error'));
    });

    /* --------- tutup modal manual --------- */
    $(document).on('click','.force-modal-close',() =>
        $('#showArticleModal').modal('hide'));

    /* tutup juga via ESC */
    $(document).on('keydown',e=>{
        if(e.key==='Escape') $('#showArticleModal').modal('hide');
    });

});
</script>

@endsection
