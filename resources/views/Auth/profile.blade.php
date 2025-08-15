{{-- resources/views/Profile/profile.blade.php --}}
@extends('App.master')

@section('style')
    <style>
        .profile-wrapper {
            max-width: 500px;
            margin: auto
        }

        .avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f1f3f5
        }

        .label {
            font-size: .85rem;
            color: #6c757d
        }
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
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit-user-form">
                    @csrf {{-- hanya untuk ajax X-CSRF --}}
                    <div class="modal-header">
                        <h5 class="modal-title">Perbarui Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Foto (Users Umum)</label>
                            <input type="file" name="foto_users_umum" id="edit-foto" class="form-control"
                                accept="image/*">
                            <div class="form-text">Opsional. JPG/PNG/WEBP, maks 4MB.</div>
                        </div>

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
                            <label class="form-label">Password Baru <small class="text-muted">(kosongkan jika tidak
                                    diganti)</small></label>
                            <input type="password" name="password" id="edit-pass" class="form-control" minlength="6">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
<script>
(() => {
  const API   = "https://kilauindonesia.org/kilau/api";
  const id    = localStorage.getItem('user_id');
  const token = localStorage.getItem('user_token') || '';

  function render(u){
      if(!u){
          Swal.fire('Gagal','Data user tidak ditemukan. Silakan login kembali.','error');
          return;
      }
      const def = '{{ asset("assets_admin/img/noimage.jpg") }}';
      const foto = u.foto_users_umum || u.foto || def;

      $('#avatar').attr('src', foto);
      $('#user-name')   .text(u.nama);
      $('#user-level')  .text(u.level ?? 'umum');
      $('#user-email')  .text(u.email);
      $('#user-referral').text(u.referral_code ?? '-');

      const isActive = ['1',1,true,'aktif'].includes(String(u.aktif ?? 1).toLowerCase ? String(u.aktif ?? 1).toLowerCase() : (u.aktif ?? 1));
      $('#user-status').html(
          isActive ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non Aktif</span>'
      );
  }

  // tampilkan dari cache cepat
  render({
      nama  : localStorage.getItem('user_name'),
      email : localStorage.getItem('user_email'),
      level : localStorage.getItem('user_level'),
      referral_code : localStorage.getItem('user_referral_code'),
      aktif : 1,
      // pakai foto yang kita simpan saat login
      foto_users_umum : localStorage.getItem('user_photo_users_umum'),
      foto            : localStorage.getItem('user_photo'),
  });

  // refresh dari server (showUser)
  if(id){
      $.get(`${API}/showUser/${id}`)
       .done(r=>{
          if(r?.berhasil){
              const u = r.berhasil;

              // refresh cache
              localStorage.setItem('user_name',  u.nama ?? '');
              localStorage.setItem('user_email', u.email ?? '');
              // simpan ulang foto prioritas
              const chosen = u.foto_users_umum || u.foto || '';
              if (chosen) localStorage.setItem('user_photo', chosen);
              if (u.foto_users_umum) localStorage.setItem('user_photo_users_umum', u.foto_users_umum);

              render(u);
          }
       });
  }

  // isi form saat modal muncul
  $('#editUserModal').on('shown.bs.modal',()=>{
      $('#edit-nama').val(localStorage.getItem('user_name')  || '');
      $('#edit-email').val(localStorage.getItem('user_email') || '');
      $('#edit-pass').val('');
      $('#edit-foto').val(''); // reset file
  });

  // submit update (multipart + _method=PUT)
  $('#edit-user-form').on('submit',function(e){
      e.preventDefault();
      if(!id) return Swal.fire('Error','User ID tidak ditemukan.','error');

      const fd = new FormData();
      fd.append('nama',   $('#edit-nama').val().trim());
      fd.append('email',  $('#edit-email').val().trim());
      const pwd = $('#edit-pass').val().trim();
      if (pwd) fd.append('password', pwd);

      const f = document.getElementById('edit-foto').files[0];
      if (f) fd.append('foto_users_umum', f);   // ← field yang diminta
      fd.append('_method','PUT');               // metode spoofing

      $.ajax({
          url: `${API}/updateUserKilau/${id}`,
          type: 'POST',               // pakai POST + _method=PUT
          data: fd,
          processData: false,
          contentType: false,
          headers: {
              // 'Authorization': 'Bearer ' + token, // kalau API butuh
          },
          success: res=>{
              // update cache nama+email
              if (res?.user) {
                  localStorage.setItem('user_name',  res.user.name ?? '');
                  localStorage.setItem('user_email', res.user.email ?? '');
              }
              // update cache foto dari response API (server mengirim 'foto_url')
              if (res?.foto_url) {
                  localStorage.setItem('user_photo', res.foto_url);
                  localStorage.setItem('user_photo_users_umum', res.foto_url);
              }

              // render ulang dari server biar akurat
              $.get(`${API}/showUser/${id}`).done(r=>{
                  if (r?.berhasil) render(r.berhasil);
              });

              Swal.fire('Berhasil','Data berhasil diperbarui.','success')
                  .then(()=> $('#editUserModal').modal('hide'));
          },
          error: xhr=>{
              Swal.fire('Error', xhr.responseJSON?.message || 'Gagal memperbarui.','error');
          }
      });
  });

})();
</script>
@endsection
