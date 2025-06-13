<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/LogoKilau2.png') }}" alt="Logo Kilau" class="me-2"
                style="max-height: 45px;">
            <span class="fw-bold">Kilau Indonesia</span>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Links -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'home' ? 'active' : '' }}" href="{{ route('home') }}">
                        Beranda
                    </a>
                </li>

                <!-- Dropdown Profil -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('profil*') ? 'active' : '' }}" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Profil
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ Route::currentRouteName() == 'profilLegalitas' ? 'active' : '' }}" href="{{ route('profilLegalitas') }}">Legalitas Lembaga</a></li>
                        <li><a class="dropdown-item {{ Route::currentRouteName() == 'profilPimpinan' ? 'active' : '' }}" href="{{ route('profilPimpinan') }}">Kepengurusan</a></li>
                        <li><a class="dropdown-item {{ Route::currentRouteName() == 'profilStruktur' ? 'active' : '' }}" href="{{ route('profilStruktur') }}">Struktur Kepegawaian</a></li>
                        <li><a class="dropdown-item {{ Route::currentRouteName() == 'profilSejarah' ? 'active' : '' }}" href="{{ route('profilSejarah') }}">Sejarah Kilau</a></li>
                        <li><a class="dropdown-item {{ Route::currentRouteName() == 'profilVisiMisi' ? 'active' : '' }}" href="{{ route('profilVisiMisi') }}">Visi & Misi</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dokumen' ? 'active' : '' }}" href="{{ route('dokumen') }}">
                        Dokumen
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'galery' ? 'active' : '' }}" href="{{ route('galery') }}">
                        Galeri
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'contact' ? 'active' : '' }}" href="{{ route('contact') }}">
                        Hubungi Kami
                    </a>
                </li>

                <li class="nav-item" id="loginWebsiteItem">
                    <a class="nav-link {{ Route::currentRouteName() == 'dashboardWebsite' ? 'active' : '' }}" href="{{ route('dashboardWebsite') }}">
                        Login Website
                    </a>
                </li>
                
                 <!-- Placeholder untuk Profile, akan ditambahkan oleh JS jika user sudah login -->
                 <li class="nav-item dropdown d-none" id="profileDropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="profile-avatar rounded-circle bg-secondary text-white d-inline-flex justify-content-center align-items-center" style="width: 40px; height: 40px; font-weight: bold;"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                          <li class="nav-item d-none" id="beritaAndaNav">
                                <a class="dropdown-item text-white fw-normal" href="{{ route('getBeritaUsers') }}">
                                    <i class="fas fa-newspaper me-1"></i> Berita Anda
                                </a>
                          </li>

                      <li class="d-none" id="poinAndaNav">
                            <a class="dropdown-item text-white fw-normal" href="{{ route('pointreferall') }}">
                                <i class="fas fa-coins me-2 text-warning"></i> Poin Anda
                            </a>
                        </li>

                        <li><a class="dropdown-item" href="#" id="btnLogout">Logout</a></li>
                    </ul>
                </li>

                <!-- Search -->
                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <form id="searchForm" class="d-flex">
                        <input class="form-control form-control-sm" type="search" id="searchInput"
                            placeholder="Cari berita..." aria-label="Search">
                    </form>
                </li>

            </ul>
        </div>
    </div>

    <!-- Hasil Pencarian -->
    <div id="searchResults"
        style="position: absolute; top: 100%; left: 0; width: 100%; background-color: #0d6efd; color: white; border: 1px solid #ddd; max-height: 200px; overflow-y: auto; display: none; z-index: 1000;">
    </div>
</nav>
<!-- Navbar End -->


<script>
    // Fungsi untuk mengambil warna acak berdasarkan input (misalnya nama)
    function getRandomColor(input) {
        const colors = ['#0d6efd', '#6f42c1', '#d63384', '#fd7e14', '#20c997', '#198754', '#dc3545', '#0dcaf0'];
        if (!input || input.length === 0) return colors[0];
        const index = input.charCodeAt(0) % colors.length;
        return colors[index];
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Cek apakah user sudah login (ada token)
        var userToken = localStorage.getItem('user_token');
        if (userToken) {
            // Sembunyikan link "Login Website"
            var loginItem = document.getElementById('loginWebsiteItem');
            if (loginItem) loginItem.classList.add('d-none');
            
            // Tampilkan profile dropdown
            var profileDropdown = document.getElementById('profileDropdown');
            if (profileDropdown) {
                profileDropdown.classList.remove('d-none');
                // Ambil nama user dari localStorage
                var userName = localStorage.getItem('user_name') || "U";
                // Ambil huruf awal nama
                var initial = userName.trim().charAt(0).toUpperCase();
                // Tampilkan inisial di dalam elemen avatar dan hapus kelas bg-secondary
                var avatarEl = profileDropdown.querySelector('.profile-avatar');
                if (avatarEl) {
                    avatarEl.textContent = initial;
                    avatarEl.classList.remove('bg-secondary');
                    // Set backgroundColor dengan !important agar bisa menimpa Bootstrap (gunakan setProperty)
                    avatarEl.style.setProperty('background-color', getRandomColor(userName), 'important');
                }

                // 👉 Tampilkan tombol Poin Anda
                var poinNav = document.getElementById('poinAndaNav');
                if (poinNav) poinNav.classList.remove('d-none');

                var beritaAndaNav = document.getElementById('beritaAndaNav');
                if (beritaAndaNav) beritaAndaNav.classList.remove('d-none');
            }
        }
        
        // Fungsi logout: hapus token dan reload halaman
        var btnLogout = document.getElementById('btnLogout');
        if (btnLogout) {
            btnLogout.addEventListener('click', function(e) {
                e.preventDefault();
                localStorage.removeItem('user_token');
                localStorage.removeItem('user_level');
                localStorage.removeItem('user_name');
                window.location.href = "{{ route('home') }}";
            });
        }
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const searchResults = document.getElementById("searchResults");

    // Event listener untuk mendeteksi perubahan pada kolom pencarian
    searchInput.addEventListener("input", function() {
        const query = searchInput.value.trim();

        if (query !== "") {
            const encodedQuery = encodeURIComponent(query);

            fetch(`https://berbagipendidikan.org/api/berita?search=${encodedQuery}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        let resultsHtml = "";

                        // Menampilkan hasil pencarian, tapi hanya berita yang "Aktif"
                        data.data.forEach(berita => {
                            if (berita.status_berita !== "Tidak Aktif") { // **Filter berita tidak aktif**
                                let beritaUrl = `/berita/${encodeURIComponent(berita.judul.replace(/\s+/g, '-'))}`;
                                resultsHtml += `<a href="${beritaUrl}" class="dropdown-item">${berita.judul}</a>`;
                            }
                        });

                        // Jika tidak ada berita aktif yang sesuai, tampilkan pesan "Berita Tidak Ditemukan"
                        if (resultsHtml === "") {
                            resultsHtml = '<a class="dropdown-item">Berita Tidak Ditemukan</a>';
                        }

                        searchResults.innerHTML = resultsHtml;
                        searchResults.style.display = "block";
                    } else {
                        searchResults.innerHTML = '<a class="dropdown-item">Berita Tidak Ditemukan</a>';
                        searchResults.style.display = "block";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: "error",
                        title: "Terjadi Kesalahan!",
                        text: "Gagal memuat berita. Silakan coba lagi.",
                    });
                });
        } else {
            // Menyembunyikan hasil pencarian jika input kosong
            searchResults.style.display = "none";
        }
    });

    // Menyembunyikan hasil pencarian ketika klik di luar form
    document.addEventListener("click", function(event) {
        if (!event.target.closest("#searchForm")) {
            searchResults.style.display = "none";
        }
    });
});

</script>
