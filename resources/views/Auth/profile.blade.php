{{-- resources/views/Profile/profile.blade.php --}}
@extends('App.master')

@section('style')
<style>
  .profile-wrapper {max-width:500px;margin:auto}
  .avatar  {width:110px;height:110px;border-radius:50%;object-fit:cover;border:4px solid #f1f3f5}
  .label   {font-size:.85rem;color:#6c757d}
</style>
@endsection


@section('content')
<div class="container py-5">
  <div class="profile-wrapper card shadow-sm p-4">
      <div class="text-center mb-4">
          <img id="avatar" src="{{ asset('assets_admin/img/noimage.jpg') }}" class="avatar mb-2" alt="Avatar">
          <h5 id="user-name" class="mb-0 fw-semibold">Memuat…</h5>
          <div id="user-level" class="label"></div>
      </div>

      <ul class="list-group list-group-flush mb-3">
          <li class="list-group-item d-flex justify-content-between">
              <span class="label">Email</span><span id="user-email"></span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
              <span class="label">Kode Referral</span><span id="user-referral">-</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
              <span class="label">Status</span><span id="user-status"></span>
          </li>
      </ul>

      <div class="d-grid">
          <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal">
              <i class="fas fa-user-edit me-1"></i> Edit Profil / Password
          </button>
      </div>
  </div>
</div>

{{-- ───── Modal Edit Profil ───── --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
      <form id="edit-user-form">
          @csrf {{-- hanya untuk ajax X-CSRF --}}
          <div class="modal-header">
              <h5 class="modal-title">Perbarui Profil</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <div class="mb-3">
                  <label class="form-label">Nama</label>
                  <input type="text" name="nama" id="edit-nama" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" id="edit-email" class="form-control" required>
              </div>
              <hr>
              <div class="mb-2">
                  <label class="form-label">Password Baru <small class="text-muted">(kosongkan jika tidak diganti)</small></label>
                  <input type="password" name="password" id="edit-pass" class="form-control" minlength="6">
              </div>
          </div>
          <div class="modal-footer">
              <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
              <button class="btn btn-primary">Simpan</button>
          </div>
      </form>
  </div></div>
</div>
@endsection


@section('scripts')
<script>
(() => {
  const API   = "{{ url('https://kilauindonesia.org/kilau/api') }}";
  const id    = localStorage.getItem('user_id');
  const token = localStorage.getItem('user_token') || '';        // ← kalau back-end butuh bearer

  /* ---------- util render ---------- */
  function render(u){
      if(!u){
          Swal.fire('Gagal','Data user tidak ditemukan. Silakan login kembali.','error');
          return;
      }

      const isActive = ['1',1,true,'aktif'].includes(String(u.aktif).toLowerCase());

      $('#avatar').attr('src', u.foto ?? '{{ asset("assets_admin/img/noimage.jpg") }}');
      $('#user-name')   .text(u.nama);
      $('#user-level')  .text(u.level ?? 'umum');
      $('#user-email')  .text(u.email);
      $('#user-referral').text(u.referral_code ?? '-');
      $('#user-status').html(
          isActive
             ? '<span class="badge bg-success">Aktif</span>'
             : '<span class="badge bg-secondary">Non Aktif</span>'
      );
  }

  /* ---------- tampilkan dari cache ---------- */
  render({
      nama  : localStorage.getItem('user_name'),
      email : localStorage.getItem('user_email'),
      level : localStorage.getItem('user_level'),
      referral_code : localStorage.getItem('user_referral_code'),
      aktif : 1
  });

  /* ---------- refresh dari server ---------- */
  if(id){
      $.get(`${API}/showUser/${id}`)
       .done(r=>{
          if(r?.berhasil){
              const u = r.berhasil;
              /* cache */
              localStorage.setItem('user_name' , u.nama);
              localStorage.setItem('user_email', u.email);
              render(u);
          }
       });
  }

  /* ---------- isi otomatis saat modal muncul ---------- */
  $('#editUserModal').on('shown.bs.modal',()=>{
      $('#edit-nama').val(localStorage.getItem('user_name')  || '');
      $('#edit-email').val(localStorage.getItem('user_email') || '');
      $('#edit-pass').val('');
  });

  /* ---------- submit update ---------- */
  $('#edit-user-form').on('submit',function(e){
      e.preventDefault();
      if(!id) return Swal.fire('Error','User ID tidak ditemukan.','error');

      const body = {
          nama : $('#edit-nama').val().trim(),
          email: $('#edit-email').val().trim()
      };
      const pwd = $('#edit-pass').val().trim();
      if(pwd) body.password = pwd;        // hanya ikut jika diisi

      $.ajax({
          url   : `${API}/updateUserKilau/${id}`,
          type  : 'PUT',
          data  : body,
          headers: { 'X-CSRF-TOKEN':'{{ csrf_token() }}',
                     // 'Authorization':'Bearer '+token   // jika diperlukan
          },
          success: res=>{
              /* simpan ulang cache */
              localStorage.setItem('user_name' , res.user.name);
              localStorage.setItem('user_email', res.user.email);

              Swal.fire('Berhasil','Data berhasil diperbarui.','success')
                   .then(()=>{ $('#editUserModal').modal('hide'); render(res.user); });
          },
          error: xhr=>{
              Swal.fire('Error', xhr.responseJSON?.message || 'Gagal memperbarui.','error');
          }
      });
  });

})();
</script>
@endsection
