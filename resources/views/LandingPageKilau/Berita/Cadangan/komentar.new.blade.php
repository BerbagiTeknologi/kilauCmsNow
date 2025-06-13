<div class="komentar-section mt-5">
    <h4 class="text-primary mb-4">Komentar</h4>

    <!-- Form Komentar -->
    <div class="form-komentar mb-4">
        <h5 class="mb-3">Tinggalkan Komentar</h5>
        <form id="form-komentar">
            <input type="hidden" name="id_berita" value="{{ $berita['id'] }}">
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

    <!-- Daftar Komentar -->
    <div id="daftar-komentar" class="daftar-komentar"></div>
</div>

<!-- Modal untuk autentikasi -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authModalLabel">Autentikasi Diperlukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Hi, mohon maaf. Untuk dapat meninggalkan komentar, silakan registrasi atau login terlebih dahulu agar data Anda terdaftar. Terima kasih!
            </div>
            <div class="modal-footer">
                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
            </div>
        </div>
    </div>
</div>


<style>
    .komentar-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }

    /* Styling komentar dan elemen lainnya */
    .daftar-komentar .komentar {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .komentar .avatar {
        width: 40px;
        height: 40px;
        background-color: #ccc;
        border-radius: 50%;
        color: white;
        font-weight: bold;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .komentar .konten {
        flex-grow: 1;
    }

    .konten .nama {
        font-weight: 600;
        font-size: 0.95rem;
        color: #0e4a9e;
        margin-bottom: 2px;
    }

    .konten .isi {
        font-size: 0.92rem;
        color: #333;
        margin-bottom: 6px;
    }

    .reply-btn {
        font-size: 0.85rem;
        color: #0d6efd;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .reply-btn:hover {
        text-decoration: underline;
    }

    .balasan {
        margin-left: 45px;
        padding-left: 15px;
    }

    .like-komentar i {
        color: #17a2b8;
        transition: color 0.3s ease, transform 0.2s ease;
    }

    .like-komentar:hover i {
        transform: scale(1.1);
    }

    .action-row {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 0.85rem;
    }

    .indent-0 {
        margin-left: 0px;
    }

    .indent-1 {
        margin-left: 40px;
    }

    .indent-2 {
        margin-left: 80px;
    }

    .indent-3 {
        margin-left: 120px;
    }

    .indent-4 {
        margin-left: 160px;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Jika user sudah login, isi field nama_pengirim secara otomatis
    let token = localStorage.getItem('user_token');
    let userName = localStorage.getItem('user_name');
    if (token && userName) {
        $("input[name='nama_pengirim']").val(userName).prop("readonly", true);
    }
    
    // Ambil judul berita dan encode untuk parameter URL API
    const judulBerita = "{{ $berita['judul'] }}";
    const encodedJudul = encodeURIComponent(judulBerita);
    console.log("Encoded Judul:", encodedJudul);

    // Variabel global untuk currentCategoryId.
    // Jika user mengklik kategori, variabel ini di-set (contoh: dari badge kategori).
    // Jika tidak, akan kosong sehingga menggunakan endpoint default berdasarkan judul.
    let currentCategoryId = window.currentCategoryId || "";

    // Contoh event handler untuk badge kategori.
    $(document).on("click", ".kategori-badge", function(e) {
        e.preventDefault();
        currentCategoryId = $(this).data("id");
        console.log("Current Category ID:", currentCategoryId);
        loadKomentar();
    });

    // Fungsi untuk menghasilkan warna acak (untuk avatar komentar)
    function getRandomColor(name) {
        const colors = ['#0d6efd', '#6f42c1', '#d63384', '#fd7e14', '#20c997', '#198754', '#dc3545', '#0dcaf0'];
        name = name ? name : "Anonymous";
        const index = name.charCodeAt(0) % colors.length;
        return colors[index];
    }

    // Fungsi renderKomentar: merender data komentar beserta nested replies secara rekursif
    function renderKomentar(komentar, indent = 0) {
        let html = '';
        komentar.forEach(k => {
            let nama = k.nama_pengirim ? k.nama_pengirim : (localStorage.getItem('user_name') || "Anonymous");
            const initial = nama.charAt(0).toUpperCase();
            const bgColor = getRandomColor(nama);
            
            html += `
            <div class="komentar balasan indent-${indent}" data-id="${k.id_komentar}">
                <div class="avatar" style="background-color: ${bgColor};">${initial}</div>
                <div class="konten">
                    <div class="nama">${nama}</div>
                    <div class="isi">${k.isi_komentar}</div>
                    <div class="action-row">
                        <span class="like-komentar" data-id="${k.id_komentar}" style="cursor:pointer;">
                            <i class="fas fa-thumbs-up me-1 text-secondary"></i>
                            <span class="like-count">${k.likes_komentar || 0}</span>
                        </span>
                        <span class="reply-btn" title="Balas" data-id="${k.id_komentar}" data-nama="${nama}">
                            <i class="fas fa-reply"></i> Balas
                        </span>
                    </div>
                </div>
            </div>
            `;
            if (k.replies && k.replies.length > 0) {
                html += renderKomentar(k.replies, indent + 1);
            }
        });
        return html;
    }

    // Fungsi loadKomentar: mengambil data komentar dari API berdasarkan kondisi
    function loadKomentar() {
        let url = "";

        // Gunakan ID berita jika tersedia (lebih spesifik)
        if (typeof window.currentBeritaId !== "undefined" && window.currentBeritaId) {
            url = `https://berbagipendidikan.org/api/komentar-berita-by-id/${window.currentBeritaId}`;
        }
        // Jika tidak, gunakan kategori jika tersedia
        else if (typeof currentCategoryId !== "undefined" && currentCategoryId) {
            url = `https://berbagipendidikan.org/api/berita-terbaru-by-kategori/${currentCategoryId}`;
        }
        // Fallback terakhir: berdasarkan judul berita (default awal)
        else {
            url = `https://berbagipendidikan.org/api/komentar-berita-by-judul/${encodedJudul}`;
        }

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function(response) {
                console.log("Response Komentar:", response);
                let comments = [];

                if (response.success && response.data) {
                    if (Array.isArray(response.data)) {
                        comments = response.data;
                    } else if (response.data.comments) {
                        comments = response.data.comments;
                    }
                } else if (response.status && response.data) {
                    // Untuk response dari endpoint komentar-by-judul
                    comments = response.data;
                }

                if (comments.length > 0) {
                    let komentarHtml = renderKomentar(comments);
                    $('#daftar-komentar').html(komentarHtml);
                } else {
                    $('#daftar-komentar').html('<p class="text-muted">Belum ada komentar.</p>');
                }
            },
            error: function() {
                $('#daftar-komentar').html('<p class="text-danger">Gagal memuat komentar.</p>');
            }
        });
    }

    
    loadKomentar();
    
    // Event delegation untuk tombol reply
    $(document).on('click', '.reply-btn', function() {
        const parentId = $(this).data('id');
        const nama = $(this).data('nama');
        $("input[name='parent_id']").val(parentId);
        $("textarea[name='isi_komentar']").focus().attr('placeholder', `Balas ke ${nama}...`);
    });
    
    // Event delegation untuk tombol like komentar.
    // Jika pengguna sudah menyukai komentar (sudah memiliki kelas "liked"),
    // maka klik selanjutnya tidak akan men-trigger AJAX request.
    $(document).on('click', '.like-komentar', function() {
        // Cek apakah user sudah login, jika belum munculkan modal autentikasi dengan pesan khusus.
        if (!localStorage.getItem('user_token')) {
            $('#authModal .modal-body').text("Untuk menyukai komentar, silakan login atau registrasi terlebih dahulu.");
            $('#authModal').modal('show');
            return;
        }
        
        const idKomentar = $(this).data('id');
        const likeElement = $(this);
        
        // Jika komentar sudah dilike, tampilkan pesan dan hentikan aksi like (tidak boleh double like).
        if (likeElement.hasClass("liked")) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Anda sudah menyukai komentar ini.',
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }
        
        // Jika belum dilike, kirim request AJAX untuk menambahkan like.
        $.ajax({
            url: `https://berbagipendidikan.org/api/komentar/${idKomentar}/like`,
            type: 'POST',
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            success: function(response) {
                if (response.success && response.likes !== undefined) {
                    // Tambahkan kelas "liked" sebagai tanda bahwa komentar sudah disukai
                    likeElement.addClass("liked");
                    // Perbarui like count dengan nilai yang diterima dari server
                    likeElement.find('.like-count').text(response.likes);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Suka ditambahkan!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menyukai komentar.'
                });
            }
        });
    });
    
    // Event handler untuk mengirim komentar.
    $('#form-komentar').on('submit', function(e) {
        e.preventDefault();
        if (!localStorage.getItem('user_token')) {
            // Ubah pesan modal khusus untuk komentar
            $('#authModal .modal-body').text("Untuk meninggalkan komentar, silakan login atau registrasi terlebih dahulu agar data Anda terdaftar. Terima kasih!");
            $('#authModal').modal('show');
            return;
        }
        const formData = $(this).serialize();
        $.ajax({
            url: 'https://berbagipendidikan.org/api/komentar-create',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Komentar berhasil dikirim.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $("textarea[name='isi_komentar']").val('');
                    $("input[name='parent_id']").val('');
                    loadKomentar();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Komentar tidak berhasil dikirim.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Terjadi kesalahan saat mengirim komentar.'
                });
            }
        });
    });
});
</script>
@endpush

