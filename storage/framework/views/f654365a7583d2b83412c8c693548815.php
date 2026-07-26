<!-- Navbar Start -->
<div class="container-fluid sticky-top custom-navbar-shadow">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark p-0">
            <a href="<?php echo e(route('home')); ?>" class="navbar-brand d-flex align-items-center">
                <img src="<?php echo e(asset('assets/img/LogoKilau2.png')); ?>" alt="Logo Kilau" class="img-fluid me-2"
                    style="max-width: 60px; height: auto;" />
                <!--<span class="text-white h4 mb-0">Kilau Indonesia</span>-->
            </a>

            <button type="button" class="navbar-toggler ms-auto me-0" data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto align-items-center">
                    <a href="<?php echo e(route('home')); ?>"
                        class="nav-item nav-link <?php echo e(Route::currentRouteName() == 'home' ? 'active' : ''); ?>">Beranda</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle <?php echo e(Request::is('profil*') ? 'active' : ''); ?>"
                            data-bs-toggle="dropdown">Profil</a>
                        <div class="dropdown-menu">
                            <a href="<?php echo e(route('profilLegalitas')); ?>"
                            class="dropdown-item <?php echo e(Route::currentRouteName() == 'profilLegalitas' ? 'active' : ''); ?>">Legalitas Lembaga</a>
                            <a href="<?php echo e(route('profilPimpinan')); ?>"
                                class="dropdown-item <?php echo e(Route::currentRouteName() == 'profilPimpinan' ? 'active' : ''); ?>">Kepengurusan Lembaga</a>
                            <a href="<?php echo e(route('profilStruktur')); ?>"
                                class="dropdown-item <?php echo e(Route::currentRouteName() == 'profilStruktur' ? 'active' : ''); ?>">Struktur Kepegawaian
                            </a>
                            <a href="<?php echo e(route('profilSejarah')); ?>"
                                class="dropdown-item <?php echo e(Route::currentRouteName() == 'profilSejarah' ? 'active' : ''); ?>">Sejarah Kilau
                            </a>
                            <a href="<?php echo e(route('profilVisiMisi')); ?>"
                                class="dropdown-item <?php echo e(Route::currentRouteName() == 'profilVisiMisi' ? 'active' : ''); ?>">Visi & Misi Kilau</a>
                        </div>
                    </div>
                    <a href="<?php echo e(route('dokumen')); ?>"
                        class="nav-item nav-link <?php echo e(Route::currentRouteName() == 'dokumen' ? 'active' : ''); ?>">Dokumen</a>
                   
                    <a href="<?php echo e(route('galery')); ?>"
                        class="nav-item nav-link <?php echo e(Route::currentRouteName() == 'galery' ? 'active' : ''); ?>">Galeri</a>
                    <a href="<?php echo e(route('contact')); ?>"
                        class="nav-item nav-link <?php echo e(Route::currentRouteName() == 'contact' ? 'active' : ''); ?>">Hubungi Kami</a>
                    <a href="<?php echo e(route('dashboardWebsite')); ?>"
                        class="nav-item nav-link <?php echo e(Route::currentRouteName() == 'dashboardWebsite' ? 'active' : ''); ?>">Login Website</a>

                    <!-- Form Pencarian Berita -->
                    <form id="searchForm" class="d-flex ms-3">
                        <input class="form-control me-2" type="search" id="searchInput" placeholder="Cari berita..."
                            aria-label="Search">
                    </form>

                    <!-- Tempat untuk menampilkan hasil pencarian -->
                    <div id="searchResults"
                        style="position: absolute; top: 100%; left: 0; width: 100%; background-color: #1363c6; color: white; border: 1px solid #ddd; max-height: 200px; overflow-y: auto; display: none;">
                        <!-- Daftar hasil pencarian akan dimuat di sini -->
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>
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
                            // Menampilkan beberapa hasil pencarian
                            data.data.forEach(berita => {
                                resultsHtml += `
                                    <a href="<?php echo e(url('berita')); ?>/${berita.judul}" class="dropdown-item">${berita.judul}</a>
                                `;
                            });
                            searchResults.innerHTML = resultsHtml;
                            searchResults.style.display = "block";
                        } else {
                            searchResults.innerHTML =
                                '<a class="dropdown-item">Berita Tidak Ditemukan</a>';
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
<?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\App\navbarv2.blade.php ENDPATH**/ ?>