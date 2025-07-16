@extends('AdminPage.App.master')

@section('style')
    <style>
        #createKategoriModal .modal-dialog,
        #editKategoriModal .modal-dialog {
            max-width: 600px;
            width: 90%;
        }

        .modal-header {
            color: black;
            border-radius: 5px 5px 0 0;
        }

        .modal-title {
            font-size: 18px;
        }

        .modal-footer .btn {
            padding: 10px 15px;
            font-size: 14px;
        }

        /* pagination – angka biasa */
        #pagination .page-link            { color:#007bff; }

        /* pagination – halaman aktif */
        #pagination .page-item.active .page-link{
            background:#007bff;   /* atau brand color Anda */
            border-color:#007bff;
            color:#fff;           /* ⬅️ angka putih */
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Data Kategori Article</h4>

                                <!-- Form Pencarian -->
                                <form id="search-form" class="ms-auto d-flex">
                                    <input type="text" id="search" name="search" class="form-control"
                                        placeholder="Cari kategori...">
                                </form>

                                <!-- Tombol Tambah Kategori -->
                                <button class="btn btn-primary btn-round ms-2" data-toggle="modal"
                                    data-target="#createKategoriModal">
                                    <i class="fa fa-plus"></i> Tambah Kategori
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="kategori-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Kategori</th>
                                            <th>Status Kategori Article</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kategori-body">
                                        <!-- Data kategori akan dimuat dengan AJAX -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="pagination-container">
                                <ul id="pagination" class="pagination justify-content-end"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH KATEGORI -->
    <div class="modal fade" id="createKategoriModal" tabindex="-1" role="dialog"
        aria-labelledby="createKategoriModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="create-kategori-form">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kategori</h5>
                        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Kategori</label>
                            <input type="text" name="name_kategori" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL SHOW KATEGORI -->
    <div class="modal fade" id="showKategoriModal" tabindex="-1" role="dialog" aria-labelledby="showKategoriModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Kategori</h5>
                    <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Nama Kategori:</strong></label>
                        <p id="show-name_kategori"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Status Kategori:</strong></label>
                        <p id="show-status_kategori"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Dibuat Pada:</strong></label>
                        <p id="show-created_at"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Terakhir Diperbarui:</strong></label>
                        <p id="show-updated_at"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT KATEGORI -->
    <div class="modal fade" id="editKategoriModal" tabindex="-1" role="dialog" aria-labelledby="editKategoriModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="edit-kategori-form">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kategori</h5>
                        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="form-group">
                            <label>Nama Kategori</label>
                            <input type="text" id="edit-name_kategori" name="name_kategori" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
const base = "{{ url('admin/article-kilau/kategori-article') }}";
$.ajaxSetup({headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});

/* ========== LIST & PAGINATION ========== */
function loadKategori(page=1, q=''){
    $.get(`${base}/list`, {page:page, per_page:10, search:q}, res => {
        /* ---- tabel ---- */
        const tbody = $('#kategori-body').empty();
        res.data.forEach((k,i)=>{
            const badge = k.status_kategori_article === 'Aktif'
                  ? 'badge-success' : 'badge-danger';
            tbody.append(`
              <tr>
                <td>${ (res.from ?? 1) + i }</td>
                <td>${ k.name_kategori }</td>
                <td>
                    <span class="badge ${badge} d-inline-block text-center"
                            style="min-width:100px; line-height:28px; padding:4px 0;">
                        ${k.status_kategori_article}
                    </span>
                </td>
                <td>
                <button class="btn btn-primary btn-sm rounded-circle p-2 show-kat" data-id="${k.id}" title="Detail">
                    <i class="fa fa-eye"></i>
                </button>
                <button class="bbtn btn-info btn-sm rounded-circle p-2 toggle-status" data-id="${k.id}" title="Ubah Status">
                    <i class="fas fa-exchange-alt"></i>
                </button>
                <button class="btn btn-warning btn-sm rounded-circle p-2 edit-kat" data-id="${k.id}" title="Edit">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm rounded-circle p-2 del-kat"  data-id="${k.id}" title="Hapus">
                    <i class="fa fa-trash"></i>
                </button>
                </td>
              </tr>
            `);
        });

        /* ---- pagination ---- */
        const pag = $('#pagination').empty();
        if(res.last_page > 1){
            for(let p=1; p<=res.last_page; p++){
                pag.append(`
                  <li class="page-item ${p==res.current_page?'active':''}">
                      <a class="page-link page-link-kat" href="#" data-page="${p}">${p}</a>
                  </li>
                `);
            }
        }
    });
}

/* pertama kali */
loadKategori();

/* klik nomor halaman */
$(document).on('click','.page-link-kat', function(e){
    e.preventDefault();
    const p = $(this).data('page');
    loadKategori(p, $('#search').val());
});

$('#search').on('keyup', e => loadKategori(1, e.target.value));

/* ==================== CREATE ==================== */
$('#create-kategori-form').on('submit', function(e){
    e.preventDefault();
    $.post("{{ url('admin/article-kilau/kategori-article/create') }}", $(this).serialize())
      .done(r=>{
          $('#createKategoriModal').modal('hide');
          this.reset();
          loadKategori();
          Swal.fire('Sukses', r.message,'success');
      })
      .fail(x=>Swal.fire('Error', x.responseJSON?.message || 'Gagal', 'error'));
});

/* buka modal create -> reset */
$('#createKategoriModal').on('shown.bs.modal',function(){
    $('#create-kategori-form')[0].reset();
});

/* ==================== SHOW ==================== */
$(document).on('click','.show-kat',function(){
    $.get("{{ url('admin/article-kilau/kategori-article/show') }}/"+$(this).data('id'), res=>{
        $('#show-name_kategori').text(res.name_kategori);
        $('#show-status_kategori').text(res.status_kategori_article ?? '-');
        $('#show-created_at').text(res.created_at);
        $('#show-updated_at').text(res.updated_at);
        $('#showKategoriModal').modal('show');
    });
});

/* ==================== EDIT (open) ==================== */
let editId=null;
$(document).on('click','.edit-kat',function(){
    editId=$(this).data('id');
    $.get("{{ url('admin/article-kilau/kategori-article/show') }}/"+editId, res=>{
        $('#edit-id').val(res.id);
        $('#edit-name_kategori').val(res.name_kategori);
        $('#editKategoriModal').modal('show');
    });
});

/* ==================== EDIT (submit) ==================== */
$('#edit-kategori-form').on('submit',function(e){
    e.preventDefault();
    if(!editId) return;
    $.ajax({
        url : "{{ url('admin/article-kilau/kategori-article/update') }}/"+editId,
        type: 'POST',
        data: $(this).serialize() + '&_method=PUT',
        success:r=>{
            $('#editKategoriModal').modal('hide');
            loadKategori();
            Swal.fire('Sukses', r.message,'success');
        },
        error:x=>Swal.fire('Error',x.responseJSON?.message||'Gagal','error')
    });
});

$(document).on('click','.toggle-status',function(){
    const id = $(this).data('id');

    Swal.fire({
        title:'Ubah status kategori?',
        text:'Apakah Anda yakin ingin mengubah status kategori article?',
        icon:'question',
        showCancelButton:true,
        confirmButtonText:'Ya, ubah!'
    }).then(ok=>{
        if(!ok.isConfirmed) return;

        $.ajax({
            url : `${base}/status/${id}`,
            type: 'POST',
            data:{_method:'PATCH'},
            success:r=>{
                loadKategori(1, $('#search').val());   // refresh tabel
                Swal.fire('Sukses', r.message,'success');
            },
            error:x=> Swal.fire('Error', x.responseJSON?.message||'Gagal','error')
        });
    });
});


/* ==================== DELETE ==================== */
$(document).on('click','.del-kat',function(){
    const id=$(this).data('id');
    Swal.fire({title:'Hapus kategori?',icon:'warning',showCancelButton:true})
     .then(ok=>{
        if(!ok.isConfirmed) return;
        $.ajax({
            url : "{{ url('admin/article-kilau/kategori-article/delete') }}/"+id,
            type: 'POST',
            data: {_method:'DELETE'},
            success:r=>{
                loadKategori();
                Swal.fire('Sukses', r.message,'success');
            },
            error:x=>Swal.fire('Error', x.responseJSON?.message||'Gagal','error')
        });
    });
});

/* ==================== close modal helper ==================== */
$('.close-modal').on('click', function(){
    $(this).closest('.modal').modal('hide');
});
</script>
@endsection
