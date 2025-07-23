@extends('AdminPage.App.master')

@section('content')
<div class="container">
  <div class="page-inner">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Daftar Semua Komentar Artikel</h4>
            <input id="search-komentar" class="form-control w-auto" placeholder="Cari nama pengirim">
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Pengirim</th>
                    <th>Komentar</th>
                    <th>Judul Artikel</th>
                    <th>Status</th>
                    <th>Likes</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody id="komentar-body">
                  {{-- data dimuat via AJAX --}}
                </tbody>
              </table>
            </div>

            <ul id="pagination" class="pagination justify-content-end mt-3" style="display:none"></ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){

  /* ---------- konfigurasi ---------- */
  let currentPage = 1, perPage = 10, keyword = '';

  const listUrl   = "{{ route('admin.commentArticle.list') }}";
  const toggleUrl = id => "{{ route('admin.commentArticle.toggleStatus', ':id') }}".replace(':id', id);
  const deleteUrl = id => "{{ route('admin.commentArticle.delete',      ':id') }}".replace(':id', id);

  /* ---------- render baris ---------- */
  function row(k,no='-',isReply=false,parentTitle='-'){
      const pad   = isReply ? ' style="padding-left:30px"' : '';
      const lbl   = isReply ? '<span class="badge badge-info">Reply</span> ' : '';
      const statC = k.status_komentar==='Aktif' ? 'btn-success' : 'btn-danger';
      const judul = k.berita?.judul || parentTitle;
      const badge = (!isReply && k.replies.length)
                    ? `<span class="badge badge-info ms-2">${k.replies.length} Balasan</span>` : '';

      return `
        <tr>
          <td>${no}</td>
          <td${pad}>${lbl}${k.nama_pengirim ?? '-'} ${badge}</td>
          <td>${k.isi_komentar}</td>
          <td>${judul}</td>
          <td><span class="btn btn-sm ${statC}" style="width:100px;pointer-events:none">${k.status_komentar}</span></td>
          <td><span class="badge bg-primary">${k.likes_komentar}</span></td>
          <td>${k.created_at}</td>
          <td>
              <div class="btn-group gap-2">
                    <button class="btn btn-info btn-sm rounded-circle toggle" data-id="${k.id_komentar}"  title="Ubah Status">
                    <i class="fas fa-exchange-alt"></i>
                    </button>
                    <button class="btn btn-danger btn-sm rounded-circle delete" data-id="${k.id_komentar}" title="Hapus">
                    <i class="fas fa-trash"></i>
                    </button>
                </div>
          </td>
        </tr>`;
  }
  function renderReplies(r,parentTitle){
      return r.map(v=>row(v,'-',true,parentTitle)+renderReplies(v.replies,parentTitle)).join('');
  }

  /* ---------- load data ---------- */
  function load(p=1,search=''){
      $('#komentar-body').html('<tr><td colspan="8" class="text-center">Memuat…</td></tr>');
      $.get(listUrl,{page:p,per_page:perPage,search},res=>{
          if(res.status && res.data.length){
              let html='', no=(p-1)*perPage+1;
              res.data.forEach(k=>{
                  html+=row(k,no++);
                  html+=renderReplies(k.replies,k.berita.judul);
              });
              $('#komentar-body').html(html);
              buildPagination(res.pagination);
          }else{
              $('#komentar-body').html('<tr><td colspan="8" class="text-center text-muted">Tidak ada data</td></tr>');
              $('#pagination').empty().hide();
          }
      }).fail(()=>$('#komentar-body').html('<tr><td colspan="8" class="text-danger text-center">Gagal memuat data</td></tr>'));
  }

  /* ---------- pagination ---------- */
  function buildPagination(p){
      let html='', cur=p.current_page, last=p.last_page;
      if(cur>1){
          html+=`<li class="page-item"><a class="page-link" data-p="1">First</a></li>
                 <li class="page-item"><a class="page-link" data-p="${cur-1}">&laquo;</a></li>`;
      }
      for(let i=Math.max(1,cur-2); i<=Math.min(last,cur+2); i++){
          html+=`<li class="page-item ${i===cur?'active':''}">
                    <a class="page-link ${i===cur?'text-white bg-primary':''}" data-p="${i}">${i}</a>
                  </li>`;
      }
      if(cur<last){
          html+=`<li class="page-item"><a class="page-link" data-p="${cur+1}">&raquo;</a></li>
                 <li class="page-item"><a class="page-link" data-p="${last}">Last</a></li>`;
      }
      $('#pagination').html(html).show();
  }
  $(document).on('click','#pagination .page-link',e=>{
      e.preventDefault(); currentPage=$(e.target).data('p'); load(currentPage,keyword);
  });

  /* ---------- search debounce ---------- */
  let t=null;
  $('#search-komentar').on('keyup',function(){
      clearTimeout(t); keyword=$(this).val();
      t=setTimeout(()=>load(1,keyword),400);
  });

  /* ---------- toggle status ---------- */
  $(document).on('click','.toggle',function(){
      const id=$(this).data('id');
      Swal.fire({title:'Ubah status?',icon:'question',showCancelButton:true}).then(r=>{
          if(r.isConfirmed){
              $.ajax({url:toggleUrl(id),type:'PATCH',data:{_token:'{{ csrf_token() }}'}})
                .done(x=>Swal.fire('Sukses',x.message,'success').then(()=>load(currentPage,keyword)));
          }
      });
  });

  /* ---------- delete ---------- */
  $(document).on('click','.delete',function(){
      const id=$(this).data('id');
      Swal.fire({title:'Hapus komentar?',icon:'warning',showCancelButton:true,confirmButtonText:'Ya'}).then(r=>{
          if(r.isConfirmed){
              $.ajax({url:deleteUrl(id),type:'DELETE',data:{_token:'{{ csrf_token() }}'}})
                .done(x=>Swal.fire('Dihapus',x.message,'success').then(()=>load(currentPage,keyword)));
          }
      });
  });

  /* first load */
  load();

});
</script>
@endsection
