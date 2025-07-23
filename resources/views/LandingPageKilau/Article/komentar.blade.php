<style>
.komentar-section{background:#f8f9fa;padding:20px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.05)}
#daftar-komentar .komentar{display:flex;align-items:flex-start;gap:14px;padding:14px 0;border-bottom:1px solid #eee}
.komentar .avatar{width:42px;height:42px;border-radius:50%;flex-shrink:0;background:#ccc;color:#fff;
                  font-weight:700;display:flex;align-items:center;justify-content:center}
.komentar .konten{flex:1;min-width:0;display:flex;flex-direction:column}
.konten .nama{font-weight:600;font-size:.95rem;color:#0e4a9e;margin-bottom:2px}
.konten .isi{font-size:.93rem;color:#333;margin-bottom:6px;white-space:pre-wrap;word-break:break-word}
.action-row{display:flex;align-items:center;gap:18px;font-size:.85rem}
.reply-btn{color:#0d6efd;cursor:pointer;display:inline-flex;gap:4px}
.reply-btn:hover{text-decoration:underline}
.like-komentar i{color:#17a2b8;transition:transform .2s}
.like-komentar:hover i{transform:scale(1.1)}
</style>

<div class="komentar-section mt-5">
    <h4 class="text-primary mb-4">Komentar</h4>

    {{-- ===== Form Komentar ===== --}}
    <div class="form-komentar mb-4">
        <h5 class="mb-3">Tinggalkan Komentar</h5>
        <form id="form-komentar">
            @csrf
            <input type="hidden" name="parent_id" value="">
            <div class="mb-2">
                <input type="text" name="nama_pengirim" class="form-control" placeholder="Nama Anda" required>
            </div>
            <div class="mb-2">
                <textarea name="isi_komentar" class="form-control" rows="3" placeholder="Tulis komentar..." required></textarea>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Kirim Komentar</button>
        </form>
    </div>

    {{-- ===== Daftar Komentar ===== --}}
    <div id="daftar-komentar"></div>
</div>

{{-- ===== Modal login/registrasi ===== --}}
<div class="modal fade" id="authModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Autentikasi Diperlukan</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">Hi, silakan login / registrasi terlebih dahulu.</div>
    <div class="modal-footer">
      <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
      <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
    </div>
  </div></div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function () {

    /* ---------- konfigurasi ---------- */
    const token    = localStorage.getItem('user_token');
    const username = localStorage.getItem('user_name');
    const getUrl   = "{{ route('lp.article.comment.index', $article->slug) }}";
    const postUrl  = "{{ route('lp.article.comment.store', $article->slug) }}";
    const likeBase = "{{ url('/komentar') }}/";

    if (token && username){
        $('input[name="nama_pengirim"]').val(username).prop('readonly',true);
    }

    /* ---------- helper ---------- */
    const randomColor = n=>{
        const c=['#0d6efd','#6f42c1','#d63384','#fd7e14','#20c997','#198754','#dc3545','#0dcaf0'];
        return c[(n.charCodeAt(0)+n.length)%c.length];
    };

    /* ---------- render komentar (tanpa batas depth) ---------- */
    function renderComment(list, indent=0){
        return list.map(c=>{
            const name = c.nama_pengirim || 'Anonymous';
            return `
              <div class="komentar" data-id="${c.id_komentar}" style="margin-left:${indent*44}px">
                <div class="avatar" style="background:${randomColor(name)}">${name[0].toUpperCase()}</div>
                <div class="konten">
                  <div class="nama">${name}</div>
                  <div class="isi">${c.isi_komentar}</div>
                  <div class="action-row">
                    <span class="like-komentar" data-id="${c.id_komentar}" style="cursor:pointer">
                      <i class="fas fa-thumbs-up me-1 text-secondary"></i>
                      <span class="like-count">${c.likes_komentar}</span>
                    </span>
                    <span class="reply-btn" data-id="${c.id_komentar}" data-nama="${name}">
                      <i class="fas fa-reply"></i> Balas
                    </span>
                  </div>
                </div>
              </div>
              ${c.replies?.length ? renderComment(c.replies, indent+1) : ''}`;
        }).join('');
    }

    /* ---------- load komentar ---------- */
    function loadKomentar(){
        $('#daftar-komentar').html('<p>Memuat...</p>');
        $.get(getUrl,res=>{
            $('#daftar-komentar').html(
                (res.data||[]).length ? renderComment(res.data) : '<p class="text-muted">Belum ada komentar.</p>'
            );
        }).fail(()=>$('#daftar-komentar').html('<p class="text-danger">Gagal memuat komentar.</p>'));
    }
    loadKomentar();

    /* ---------- reply ---------- */
    $(document).on('click','.reply-btn',function(){
        $('input[name="parent_id"]').val($(this).data('id'));
        $('textarea[name="isi_komentar"]').focus()
            .attr('placeholder',`Balas ke ${$(this).data('nama')}...`);
    });

    /* ---------- like komentar ---------- */
    $(document).on('click','.like-komentar',function(){
        if (!token){ $('#authModal').modal('show'); return; }
        if ($(this).hasClass('liked')) return;
        const el=$(this),id=el.data('id');
        $.post(likeBase+id+'/like', {_token:'{{ csrf_token() }}'}, res=>{
            el.addClass('liked').find('.like-count').text(res.likes);
            Swal.fire({toast:true,position:'top-end',icon:'success',
                       title:'Suka ditambahkan',showConfirmButton:false,timer:1200});
        });
    });

    /* ---------- submit komentar ---------- */
    $('#form-komentar').on('submit',function(e){
        e.preventDefault();
        if (!token){ $('#authModal').modal('show'); return; }
        $.post(postUrl, $(this).serialize(), res=>{
            if (res.status){
                this.reset(); $('input[name="parent_id"]').val('');
                loadKomentar();
                Swal.fire({toast:true,position:'top-end',icon:'success',
                           title:'Komentar berhasil dikirim',showConfirmButton:false,timer:1500});
            }
        }).fail(()=>Swal.fire('Oops','Gagal mengirim komentar','error'));
    });
});
</script>
@endpush
