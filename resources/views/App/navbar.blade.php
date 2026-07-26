<!-- Navbar Start -->
@php
    $authUser = auth()->user();
    $authName = $authUser ? trim($authUser->name) : '';
    $authInitial = strtoupper(substr($authName !== '' ? $authName : 'U', 0, 1));
@endphp
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
                        <li><a class="dropdown-item {{ Route::currentRouteName() == 'dokumen' ? 'active' : '' }}" href="{{ route('dokumen') }}">Dokumen</a></li>
                    </ul>
                </li>

                {{-- <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dokumen' ? 'active' : '' }}" href="{{ route('dokumen') }}">
                        Dokumen
                    </a>
                </li> --}}

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'galery' ? 'active' : '' }}" href="{{ route('galery') }}">
                        Galeri
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'lp.article.index' ? 'active' : '' }}" href="{{ route('lp.article.index') }}">
                        Artikel
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'contact' ? 'active' : '' }}" href="{{ route('contact') }}">
                        Hubungi Kami
                    </a>
                </li>

                @guest
                    <li class="nav-item" id="loginWebsiteItem">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboardWebsite' ? 'active' : '' }}" href="{{ route('dashboardWebsite') }}">
                            Sign In
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown" id="profileDropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="profile-avatar rounded-circle bg-secondary text-white d-inline-flex justify-content-center align-items-center" style="width: 40px; height: 40px; font-weight: bold;">
                                {{ $authInitial }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @if ($authUser?->role === 'admin')
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard Admin
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <a class="dropdown-item" href="{{ route('getDataUsersProfile') }}">
                                    <i class="fas fa-user me-1"></i> Profile Anda
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('lp.article.external.index') }}">
                                    <i class="fas fa-newspaper me-1"></i> Cerita Anda
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('pointreferall') }}">
                                    <i class="fas fa-hand-holding-heart me-2 text-danger"></i> Fundraiser
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest

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
