<?php
    // Cek apakah fungsi getEmbedURL sudah dideklarasikan sebelumnya
    if (!function_exists('getEmbedURL')) {
        // Fungsi untuk mendapatkan embed URL dari URL YouTube
        function getEmbedURL($url)
        {
            // Menghapus parameter query (seperti ?si=...) jika ada
            $url = preg_replace('/\?[^\/]+$/', '', $url);

            // Cek apakah URL dalam format 'youtu.be', 'youtube.com', atau 'youtube.com/shorts'
            $regExp =
                '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:shorts\/([\w\-]{11})|(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/)([\w\-]{11}))|youtu\.be\/([\w\-]{11}))/';
            preg_match($regExp, $url, $match);

            if (!empty($match[1])) {
                // Jika URL adalah Shorts
                return 'https://www.youtube.com/embed/' . $match[1]; // Return embed URL untuk Shorts
            } elseif (!empty($match[2])) {
                // Jika URL adalah video standar YouTube
                return 'https://www.youtube.com/embed/' . $match[2]; // Return embed URL untuk video standar
            } elseif (!empty($match[3])) {
                // Jika URL menggunakan format 'youtu.be'
                return 'https://www.youtube.com/embed/' . $match[3]; // Return embed URL untuk video standar
            }

            return null; // Jika URL tidak valid
        }
    }

    // Cek apakah fungsi extractVideoId sudah dideklarasikan sebelumnya
    if (!function_exists('extractVideoId')) {
        // Fungsi untuk mengekstrak ID video dari URL standar YouTube
        function extractVideoId($url)
        {
            $url = preg_replace('/\?[^\/]+$/', '', $url); // Menghapus query parameter

            // Mengambil ID video dari URL standar YouTube atau YouTube Shorts
            $pattern =
                '/(?:https?:\/\/)?(?:www\.)?youtube\.com\/(?:shorts\/([\w\-]{11})|(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/)([\w\-]{11}))|youtu\.be\/([\w\-]{11})/';
            preg_match($pattern, $url, $matches);

            return $matches[1] ?? ($matches[2] ?? ($matches[3] ?? null));
        }
    }
?>

<?php $__env->startSection('style'); ?>
    <style>
        #testimonial-container {
            position: relative;
            overflow-x: hidden;
            /* Menyembunyikan scrollbar horizontal */
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }

        #testimonial-container .col-12 {
            scroll-snap-align: start;
        }

        /* Media Query untuk Mobile */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            #berita-container {
                flex-direction: column;
                overflow-x: auto;
            }

            .col-lg-4,
            .col-md-6 {
                width: 100% !important;
                flex: 0 0 100%;
            }

            .card-title a {
                font-size: 16px;
            }

            .card-text {
                font-size: 12px;
            }
        }

        /* Media Query untuk Tablet */
        @media (max-width: 1024px) {

            .col-lg-4,
            .col-md-6 {
                width: 100% !important;
            }
        }

        .modal-dialog {
            max-width: 800px;
            max-height: none;
            overflow-y: hidden;
        }

        .modal-body {
            /* max-height: 75vh; */
            overflow-y: auto;
        }

        /* Tampilan Mobile Dinamis */
        @media (max-width: 768px) {

            /* Menyesuaikan gambar agar full width */
            .modal-body img {
                width: 100%;
                height: auto;
                /* Agar tidak terdistorsi */
                object-fit: cover;
            }

            /* Menyesuaikan ukuran tombol agar full width */
            .modal-footer .btn {
                width: 100%;
                padding: 12px;
                font-size: 16px;
                /* Memperbesar ukuran font agar lebih terlihat */
            }
        }

        #donasiModal .modal-content {
            background-color: #1363c6;
            color: white;
            background-image: url(<?php echo e(asset('assets/img/bg-hero.png')); ?>);
            background-size: cover;
            background-position: center;
        }

        /* Modal Header Styling */
        #donasiModal .modal-header {
            background-color: #1363c6;
            color: white;
        }

        /* Styling untuk tombol donasi */
        #donasiModal .btn-custome {
            background-color: rgb(9, 9, 188);
            color: white;
        }

        #donasiModal .btn-custome:hover {
            background-color: rgb(9, 9, 97);
            color: white;
        }

        .selected-btn {
            background-color: white !important;
            color: black !important;
        }

        .bg-white {
            background-color: white !important;
        }

        .bg-transparent {
            background-color: transparent !important;
        }

        /* ========= IKLAN MODAL – aturan umum (SEMUA ukuran) ========= */
        #iklanModal .modal-dialog {
            /* ukuran desktop/tablet – Anda boleh ubah angka */
            max-width: 750px;
        }

        #iklanModal .modal-body {
            padding: 0;
            /* tetap tanpa padding di semua ukuran */
            overflow: hidden;
            /* cegah scroll horizontal */
        }

        #iklanModal .modal-body img {
            width: 100%;
            object-fit: contain;
        }

        /* footer & tombol (berlaku di semua ukuran) */
        #iklanModal .modal-footer {
            padding: .5rem 1rem .75rem;
        }

        #iklanModal .modal-footer .btn {
            font-size: 15px;
            padding: 10px 12px;
        }

        /* ========= penyesuaian khusus MOBILE (≤ 576 px) ========= */
        @media (max-width:576px) {

            /* beri jarak atas‑bawah via variable Bootstrap */
            #iklanModal {
                --bs-modal-margin: 1rem;
            }

            #iklanModal .modal-dialog {
                max-width: 90vw !important;
                /* kartu lebih ramping */
                margin: auto;
                /* biarkan Bootstrap “center” */
                width: auto;
            }

            #iklanModal .modal-body {
                max-height: 65vh;
                /* batasi tinggi total */
            }

            #iklanModal .modal-body img {
                height: 260px !important;
                /* override tinggi gambar */
            }
        }

        /* ===== versi layar >576 px (tinggi gambar 420 px) ===== */
        @media (min-width:577px) {
            #iklanModal .modal-body img {
                height: 420px;
                /* tinggi desktop */
            }
        }

        /* cegah scroll horisontal di mana pun */
        #iklanModal,
        #iklanModal .modal-dialog,
        #iklanModal .modal-content {
            overflow-x: hidden;
        }

        /* hilangkan kompensasi scrollbar Bootstrap */
        body.modal-open {
            padding-right: 0 !important;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <!-- Modal Iklan (muncul saat halaman dimuat) -->
    <div class="modal fade" id="iklanModal" tabindex="-1" aria-labelledby="iklanModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" style="max-width:750px; max-height: 500px;">
            <div class="modal-content border rounded-3 shadow">

                
                <div class="modal-body pt-4 pb-2 p-0 position-relative">

                    <div id="carouselIklan" class="carousel slide" data-bs-ride="carousel">

                        <div class="carousel-inner">
                            <?php $__currentLoopData = $donasiiklan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isPng = strtolower(pathinfo($item->file, PATHINFO_EXTENSION)) === 'png';
                                    $link = trim($item->link ?? '');
                                    $label = trim($item->name_button_iklan ?? '');
                                    $icon = trim($item->icon_iklan ?? '');
                                ?>

                                <div class="carousel-item <?php echo e($idx ? '' : 'active'); ?>" data-link="<?php echo e($link); ?>"
                                    data-label="<?php echo e($label); ?>" data-icon="<?php echo e($icon); ?>">
                                    <img src="<?php echo e(Storage::url($item->file)); ?>"
                                        class="d-block mx-auto img-fluid <?php echo e($isPng ? '' : 'bg-white'); ?>"
                                        style="max-width:85%;max-height:380px;object-fit:contain;
                                                padding:<?php echo e($isPng ? 0 : '10px'); ?>;border-radius:.5rem;">
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselIklan"
                            data-bs-slide="prev" style="background: none; border: none;">
                            <i class="fas fa-chevron-left" style="color: #1363c6; font-size: 1.8rem;"></i>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselIklan"
                            data-bs-slide="next" style="background: none; border: none;">
                            <i class="fas fa-chevron-right" style="color: #1363c6; font-size: 1.8rem;"></i>
                        </button>

                    </div>
                </div>

                
                <div class="modal-footer border-0 px-4 pb-4">
                    <?php
                        // ambil slide pertama (bisa null kalau koleksi kosong)
                        $first = $donasiiklan->first();

                        // ===== Label tombol =====
                        //  - kalau name_button_iklan null, "", atau "   " → default
                        $label = blank($first?->name_button_iklan) ? 'Berbagi Sekarang' : $first->name_button_iklan;

                        // ===== Ikon tombol =====
                        //  - kalau icon_iklan null / "" / " " → default
                        $ikon = blank($first?->icon_iklan) ? 'fa-money-bill-wave' : $first->icon_iklan;
                    ?>

                    <button id="openDonasiModal" data-ga-event="click_donation_cta" data-ga-source="home_donation_card"
                        class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 rounded-pill shadow-sm">
                        <i id="btn-icon" class="fas <?php echo e($ikon); ?>"></i>
                        <span id="btn-label"><?php echo e($label); ?></span>
                    </button>

                </div>

            </div>
        </div>
    </div>

    <!-- Modal Donasi Start -->
    <div class="modal fade" id="donasiModal" tabindex="-1" aria-labelledby="donasiModalLabel" aria-hidden="true"
        data-ga-modal-event="open_donation_modal" data-ga-source="donation_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-light" id="donasiModalLabel">Donasi Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Hi, apakah kamu ingin menjadi bagian dari kami untuk menebarkan manfaat?</p>
                    <p><strong>⭐ Ayo Berbagi sekarang ⭐</strong></p>

                    <form id="donasiForm" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Donatur <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                placeholder="Masukkan Nama Donatur" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor HP </label>
                            <input type="tel" class="form-control" id="no_hp" name="no_hp"
                                placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="nama@email.com">
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_anonymous"
                                name="is_anonymous" value="1">
                            <label class="form-check-label" for="is_anonymous">Donasi sebagai anonim</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Doa atau Dukungan</label>
                            <textarea class="form-control" id="feedback" name="feedback" rows="3" placeholder="Tulis pesan atau doa…"></textarea>
                        </div>

                        <!-- Jenis Donasi -->
                        <div class="mb-3">
                            <label class="form-label">Jenis Donasi <span class="text-danger">*</span></label><br>
                            <!-- Donasi Program Button -->
                            <div class="row">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-light w-100" id="donasiProgramBtn">Donasi
                                        Program</button>
                                </div>
                                <!-- Donasi Umum Button -->
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-light w-100" id="donasiUmumBtn">Donasi
                                        Umum</button>
                                </div>
                            </div>
                        </div>
                        <!-- Jenis Donasi End -->

                        <div class="mb-3" id="program-cards" style="display: none;">
                            <label for="program-cards" class="form-label text-white">Program Kami</label>
                            <div class="row row-cols-2 row-cols-md-4 g-3"> <!-- Bootstrap Grid -->
                                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col">
                                        <div class="card program-card h-100" data-program="<?php echo e($program->judul); ?>"
                                            data-program-id="<?php echo e($program->id); ?>">
                                            <img src="<?php echo e(asset('storage/' . $program->thumbnail_image)); ?>"
                                                class="card-img-top program-img img-fluid" alt="<?php echo e($program->judul); ?>">
                                            <div class="card-body p-2 text-center">
                                                <!--<small class="text-white"><?php echo e(Str::limit($program->judul, 20)); ?></small>-->
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <input type="hidden" id="programIdInput" name="id_program" value="">

                        <!-- Informasi Deskripsi dan Statistik Program -->
                        <div class="mb-3" id="program-info" style="display: none;">
                            <h6 class="text-white" id="program-title"></h6> <!-- Added title element -->
                            <p id="program-description"></p>
                            <p id="program-statistics"></p>
                        </div>

                        <!-- Opsional Umum -->
                        <div class="mb-3" id="opsionalUmum" style="display: none;">
                            <label class="form-label">Pilih Opsional Umum</label><br>
                            <!-- Row to contain the buttons -->
                            <div class="row">
                                <!-- Button for Zakat -->
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-light w-100" name="opsional_umum"
                                        value="1" data-value="1">Zakat</button>
                                </div>
                                <!-- Button for Infaq -->
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-light w-100" name="opsional_umum"
                                        value="2" data-value="2">Infaq</button>
                                </div>
                            </div>
                        </div>

                        <!-- Input Hidden untuk opsional_umum -->
                        <input type="hidden" id="opsionalValueInput" name="opsional_umum" value="">

                        <!-- Pilihan Jumlah Donasi -->
                        <div class="mb-3">
                            <label for="donasi" class="form-label">Pilih Jumlah Donasi <span
                                    class="text-danger">*</span></label>
                            <div class="row">
                                <?php $__currentLoopData = [1, 25000, 50000, 75000, 100000]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-4 mt-2">
                                        <button type="button" class="btn btn-outline-light donasi-btn"
                                            data-amount="<?php echo e($amount); ?>" style="width: 100%;">Rp
                                            <?php echo e(number_format($amount, 0, ',', '.')); ?></button>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-4 mt-2">
                                    <button type="button" class="btn btn-outline-light" id="donasiCustom"
                                        style="width: 100%;">Isi Sendiri</button>
                                </div>
                            </div>
                        </div>

                        <!-- Input Custom Donasi -->
                        <div class="mb-3" id="customDonasi" style="display: none;">
                            <label for="customAmount" class="form-label">Masukkan Nominal Donasi</label>
                            <input type="number" class="form-control" id="customAmount" name="customAmount"
                                placeholder="Masukkan jumlah donasi">
                        </div>

                        <!-- Total Donasi -->
                        <div class="mb-3">
                            <label for="total" class="form-label">Total Donasi</label>
                            <input type="text" class="form-control" id="total" name="total" readonly>
                        </div>

                        <button type="submit" class="btn btn-custome w-100">Berbagi Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Donasi End -->

    <!-- Hero Start -->
    <div class="container-fluid py-5 bg-primary hero-header" style="">
        <div class="container py-5">
            <!-- Carousel -->
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
                <!-- Indicators -->
                <div class="carousel-indicators" style="bottom: -90px !important;">
                    <?php $__currentLoopData = $homeKilau; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo e($index); ?>"
                            class="<?php echo e($index == 0 ? 'active' : ''); ?>"
                            aria-current="<?php echo e($index == 0 ? 'true' : 'false'); ?>"
                            aria-label="Slide <?php echo e($index + 1); ?>"></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Slides -->
                <div class="carousel-inner hero-inner">
                    <?php $__currentLoopData = $homeKilau; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="carousel-item <?php echo e($index == 0 ? 'active' : ''); ?>">
                            <div class="row g-5 pt-5">
                                <!-- Kolom Teks -->
                                <div class="col-lg-6 col-12 align-self-center text-center text-lg-start mb-lg-5">
                                    <h1 class="display-4 text-white ">
                                        <?php echo e($item->judul_home); ?>

                                    </h1>
                                </div>

                                <!-- Kolom Gambar atau Animasi Lottie -->
                                <div class="col-lg-6 col-12 order-1 order-lg-3 ps-3 d-flex justify-content-center">
                                    <?php if($item->file_home): ?>
                                        <!-- Tampilkan Gambar Jika Ada -->
                                        <img src="<?php echo e(Storage::url($item->file_home)); ?>" class="img-fluid"
                                            alt="<?php echo e($item->judul_home); ?>" style="width: 50% !important; height:auto;">
                                    <?php else: ?>
                                        <!-- Tampilkan Animasi Lottie Jika Tidak Ada Gambar -->
                                        <dotlottie-player
                                            src="https://lottie.host/e0a24f8d-7cba-4c46-8167-c7cb86af6595/17K5vHPYIi.lottie"
                                            background="transparent" speed="1"
                                            style="max-width: 100%; height: auto;" loop autoplay>
                                        </dotlottie-player>
                                        
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    
    <?php if($tentangMenu && $tentangMenu->status == 'Aktif'): ?>
        <div class="container-fluid py-5">
            <div class="container py-5">
                <!-- Judul Section -->
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <!-- Teks "Tentang Kami" dengan Efek Hover -->
                        <div class="tooltip-container">
                            <h1 class="display-5 fw-bold mb-2 hover-text"><?php echo e($tentangMenu->judul); ?></h1>
                        </div>
                        <p class="lead mb-4">
                            <?php echo e($tentangMenu->subjudul); ?>

                        </p>
                    </div>
                </div>

                <div class="row gx-0 gy-4 py-3">
                    <?php $__currentLoopData = $tentangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tentang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <!-- Bagian Teks -->
                        <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up"
                            data-aos-delay="200">
                            <div class="content">
                                <h2><?php echo e($tentang->judul_tentang_kami); ?></h2>
                                <p><?php echo e($tentang->deskripsi); ?></p>

                                <!-- Lihat Selengkapnya -->
                                <a href="<?php echo e(route('tentangkami.landingpage')); ?>" class="btn btn-primary mt-2">
                                    Lihat Selengkapnya
                                </a>
                            </div>
                        </div>

                        <!-- Bagian Gambar -->
                        <div class="col-lg-6 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
                            <img src="<?php echo e($tentang->file ? Storage::url($tentang->file) : asset('assets/img/default.jpg')); ?>"
                                class="img-fluid about-img" alt="Tentang Kami">
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if($timelineMenu && $timelineMenu->status == 'Aktif'): ?>
        <?php echo $__env->make('LandingPageKilau.timline_interaktif', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <?php if($beritaMenu && $beritaMenu->status == 'Aktif'): ?>
        <!-- Berita Start -->
        <div id="berita-donatur" class="container-fluid py-5">
            <div class="container py-5">
                <!-- Judul Section -->
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h1 class="display-5 fw-bold mb-2"> <?php echo e($beritaMenu->judul); ?></h1>
                        <p class="lead mb-4">
                            <?php echo e($beritaMenu->subjudul); ?>

                        </p>
                    </div>
                </div>

                <!-- Konten Berita -->
                <div class="row g-4 flex-nowrap overflow-x-auto pb-3 scroll-hidden" id="berita-container">
                    <!-- Data Berita Akan Dimuat Dinamis dengan AJAX -->
                </div><!-- End Row -->

                <!-- Pagination -->
                <div class="row justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul id="pagination" class="pagination">
                            <!-- Tombol pagination akan dimuat oleh JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div><!-- End Container -->
        </div><!-- End Berita -->
    <?php endif; ?>

    <?php if($campaignMenu && $campaignMenu->status == 'Aktif'): ?>
        <?php echo $__env->make('LandingPageKilau.campaign_kilau', [
            'campaignMenu' => $campaignMenu,
            'campaigns' => $campaigns,
            'page' => $page,
            'perPage' => $perPage,
            'lastPage' => $lastPage,
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <?php $__currentLoopData = $iklanKilau; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($item->iklanKilauLists->isNotEmpty()): ?>
            <!-- Memastikan ada data IklanKilauList yang aktif -->
            <div class="container-fluid bg-primary feature pt-5">
                <div class="container pt-5">
                    <div class="row g-5 align-items-center">
                        <!-- Kolom Kiri: Deskripsi -->
                        <div class="col-lg-6 align-self-center mb-md-5 pb-md-5 wow fadeIn" data-wow-delay="0.3s">
                            <div class="btn btn-sm border rounded-pill text-white px-3 mb-3">
                                Mengapa Memilih Kami
                            </div>
                            <h1 class="text-white mb-4"><?php echo e($item->judul); ?></h1>
                            <p class="text-light mb-4"><?php echo e($item->deskripsi); ?></p>

                            <!-- Menampilkan semua IklanKilauList tanpa kondisi status aktif -->
                            <?php $__currentLoopData = $item->iklanKilauLists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iklanList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="d-flex align-items-center text-white mb-3">
                                    <div class="btn-sm-square bg-white text-primary rounded-circle me-3">
                                        <i class="fa fa-check"></i>
                                    </div>
                                    <span><?php echo e($iklanList->name); ?></span> <!-- Menampilkan name dari IklanKilauList -->
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <!-- Menampilkan IklanKilauList yang aktif -->
                            <?php $__currentLoopData = $item->iklanKilauLists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iklanList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($iklanList->status_iklan_kilau_list == '1'): ?>
                                    <!-- Cek jika statusnya aktif -->
                                    <div class="d-flex align-items-center text-white mb-3">
                                        <div class="btn-sm-square bg-white text-primary rounded-circle me-3">
                                            <i class="fa fa-check"></i>
                                        </div>
                                        <span><?php echo e($iklanList->name); ?></span>
                                        <!-- Menampilkan name dari IklanKilauList yang aktif -->
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <div class="row g-4 pt-3">
                                <div class="col-sm-6">
                                    <div class="d-flex rounded p-3" style="background: rgba(256, 256, 256, 0.1)">
                                        <i class="fa fa-building fa-3x text-white"></i>
                                        <div class="ms-3">
                                            <h2 class="text-white mb-0"><?php echo e($item->jumlah_yayasan); ?></h2>
                                            <p class="text-white mb-0">Kantor Cabang</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex rounded p-3" style="background: rgba(256, 256, 256, 0.1)">
                                        <i class="fa fa-hand-holding-heart fa-3x text-white"></i>
                                        <div class="ms-3">
                                            <h2 class="text-white mb-0" id="jumlahDonatur">Loading...</h2>
                                            <p class="text-white mb-0">Donatur Kami</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Animasi Lottie atau Gambar -->
                        <div class="col-lg-6 d-flex justify-content-center align-items-center wow fadeIn"
                            data-wow-delay="0.5s">
                            <?php if($item->file): ?>
                                <!-- Jika ada gambar, tampilkan gambar -->
                                <img src="<?php echo e(Storage::url($item->file)); ?>" alt="Home Kilau Image" class="img-fluid"
                                    style="max-width: 350px; margin-left: 50px !important; height: auto;">
                            <?php else: ?>
                                <!-- Jika tidak ada gambar, tampilkan animasi Lottie -->
                                <dotlottie-player
                                    src="https://lottie.host/aad7ce21-732e-4713-b698-1318a30a3e18/xsi4Rvux4O.lottie"
                                    background="transparent" speed="1"
                                    style="max-width: 300px; width: 100%; height: auto;" loop autoplay>
                                </dotlottie-player>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if($programMenu && $programMenu->status == 'Aktif'): ?>
        <?php echo $__env->make('LandingPageKilau.ourprogram', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <?php if($testimoniMenu && $testimoniMenu->status == 'Aktif'): ?>
        <?php echo $__env->make('LandingPageKilau.testimoni', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    

    <!-- Modal untuk Komentar -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Beri Komentar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="commentForm" action="<?php echo e(route('testimonihome.create')); ?>" method="POST"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="commentName" class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" id="commentName"
                                placeholder="Masukkan nama Anda" required>
                        </div>
                        <div class="mb-3">
                            <label for="commentKerja" class="form-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" id="commentKerja"
                                placeholder="Masukkan pekerjaan Anda (Opsional)">
                        </div>
                        <div class="mb-3">
                            <label for="commentMessage" class="form-label">Komentar</label>
                            <textarea name="komentar" class="form-control" id="commentMessage" rows="3" placeholder="Tulis komentar Anda"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="commentFile" class="form-label">Upload Gambar (Opsional)</label>
                            <input type="file" name="file" class="form-control" id="commentFile"
                                accept="image/*">
                            <small class="text-muted">Hanya gambar dengan format jpeg, png, jpg, gif, svg yang
                                diterima.</small>
                            <div class="mt-2">
                                <img id="imagePreview" src="#" alt="Pratinjau Gambar"
                                    style="max-width: 50%; display: none;" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Kirim Komentar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    

    <?php if($mitraMenu && $mitraMenu->status == 'Aktif'): ?>
        <!-- Mitra Donatur Start -->
        <div id="mitra-donatur" class="container-fluid py-5">
            <div class="container py-5">
                <!-- Judul Section -->
                <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 500px">
                    <h1 class="mb-4"><?php echo e($mitraMenu->judul); ?></h1>
                    <p class="lead"><?php echo e($mitraMenu->subjudul); ?></p>
                </div>

                <!-- Semua Baris Mitra Donatur -->
                <div class="row justify-content-center wow fadeIn py-5" data-wow-delay="0.3s"
                    style="gap: 20px; flex-wrap: wrap;">
                    <?php if($mitras && $mitras->count()): ?>
                        <?php $__currentLoopData = $mitras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mitra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-lg-1 col-md-2 col-3 mb-4 text-center">
                                <img src="<?php echo e($mitra->file ? Storage::url($mitra->file) : asset('images/default.png')); ?> "
                                    class="img-fluid w-100" style="max-width: 100px;" alt="<?php echo e($mitra->nama_mitra); ?>">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <p class="text-center">Belum ada mitra donatur yang aktif.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Mitra Donatur End -->
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="<?php echo e(config('services.midtrans.client_key')); ?>"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

    <script>
        $(function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const debounce = (fn, ms = 500) => {
                let t;
                return (...a) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, a), ms);
                };
            };

            function cleanPhone(raw) {
                return (raw || '').toString().replace(/\D+/g, '');
            }

            // Fungsi untuk masking email sebelum ditampilkan di Swal
            function maskEmail(value) {
                const raw = (value || '').toString();
                if (!raw) return raw;
                const parts = raw.split('@');
                if (parts.length < 2) return raw;
                const domain = parts.slice(1).join('@');
                const local = parts[0];
                if (!domain) return raw;
                if (local.length <= 2) {
                    const firstChar = local.charAt(0) || '*';
                    return firstChar + '*@' + domain;
                }
                return local.slice(0, 2) + '***@' + domain;
            }

            // Fungsi untuk masking nama pengguna sebelum ditampilkan di Swal
            function maskName(value) {
                const raw = (value || '').toString();
                if (!raw) return raw;
                if (raw.length <= 1) return raw + '*' + raw;
                if (raw.length <= 4) return raw.charAt(0) + '*' + raw.slice(-1);
                const middleMask = '*'.repeat(raw.length - 4);
                return raw.slice(0, 2) + middleMask + raw.slice(-2);
            }

            // Fungsi untuk masking nomor HP sebelum ditampilkan di Swal
            function maskPhone(value) {
                const raw = (value || '').toString().trim();
                if (!raw) return raw;
                const digits = raw.replace(/[^0-9]/g, '');
                if (!digits) return raw;
                let maskedDigits;
                if (digits.length === 1) {
                    maskedDigits = '*';
                } else if (digits.length === 2) {
                    maskedDigits = digits.charAt(0) + '*';
                } else if (digits.length === 3) {
                    maskedDigits = digits.charAt(0) + '*' + digits.slice(-1);
                } else {
                    const firstCount = Math.min(3, digits.length - 2);
                    const lastCount = Math.min(3, digits.length - firstCount - 1);
                    const maskLen = Math.max(1, digits.length - firstCount - lastCount);
                    const first = digits.slice(0, firstCount);
                    const last = digits.slice(-lastCount);
                    maskedDigits = first + '*'.repeat(maskLen) + last;
                }
                let result = '';
                let index = 0;
                for (const char of raw) {
                    if (/\d/.test(char) && index < maskedDigits.length) {
                        result += maskedDigits.charAt(index++);
                    } else {
                        result += char;
                    }
                }
                if (index < maskedDigits.length) {
                    result += maskedDigits.slice(index);
                }
                return result;
            }

            function fillForm(d) {
                if (d.nama && !$('#nama').val()) $('#nama').val(d.nama);
                if (d.email && $('#email').val() !== d.email) $('#email').val(d.email);
                if (d.no_hp && $('#no_hp').val() !== d.no_hp) $('#no_hp').val(d.no_hp);
            }

            // ====== GUARD STATE ======
            let isPromptOpen = false;
            const handledKeys = new Set(); // kombinasi email+hp yang sudah diproses
            let lookupXHR = null;

            const keyOf = (email, hp) => `e:${(email||'').toLowerCase()}|p:${hp||''}`;

            const doLookup = debounce(function() {
                let email = ($('#email').val() || '').trim();
                let no_hp = cleanPhone($('#no_hp').val());
                if ($('#no_hp').val() !== no_hp) $('#no_hp').val(no_hp); // normalisasi hanya jika berubah

                const canCheck = (email && emailRegex.test(email)) || (no_hp && no_hp.length >= 9);
                if (!canCheck || isPromptOpen) return;

                const key = keyOf(email, no_hp);
                if (handledKeys.has(key)) return; // sudah ditangani sebelumnya

                // Batalkan request sebelumnya bila ada
                if (lookupXHR) {
                    try {
                        lookupXHR.abort();
                    } catch (e) {}
                    lookupXHR = null;
                }

                lookupXHR = $.ajax({
                    url: "<?php echo e(route('donasi.cek-donatur')); ?>",
                    method: "GET",
                    data: {
                        email: emailRegex.test(email) ? email : undefined,
                        no_hp: (no_hp && no_hp.length >= 9) ? no_hp : undefined
                    },
                    complete: function() {
                        lookupXHR = null;
                    },
                    success: function(res) {
                        if (!res || !res.found || !res.data) return;

                        const via = res.source === 'email' ? 'Email' : 'No HP';
                        const namaAsli = (res.data.nama || '').trim();
                        const emailAsli = (res.data.email || '').trim();
                        const phoneAsli = (res.data.no_hp || '').trim();
                        const nm = namaAsli || '(tanpa nama)';
                        const em = emailAsli || '-';
                        const hp = phoneAsli || '-';
                        const maskedNm = namaAsli ? maskName(namaAsli) : nm;
                        const maskedEm = emailAsli ? maskEmail(emailAsli) : em;
                        const maskedHp = phoneAsli ? maskPhone(phoneAsli) : hp;

                        isPromptOpen = true;
                        const pesanSwal = via + ' sudah terdaftar atas nama <b>' + maskedNm + '</b><br>' +
                            'Email: <b>' + maskedEm + '</b><br>' +
                            'No HP: <b>' + maskedHp + '</b><br><br>' +
                            'Apakah ingin memakai data ini untuk mengisi form?';
                        Swal.fire({
                            icon: 'question',
                            title: 'Data donatur ditemukan',
                            html: pesanSwal,
                            showCancelButton: true,
                            confirmButtonText: 'Pakai',
                            cancelButtonText: 'Tidak'
                        }).then((r) => {
                            isPromptOpen = false;
                            handledKeys.add(
                            key); // tandai: jangan munculkan lagi untuk kombinasi ini
                            if (r.isConfirmed) fillForm(res.data);
                        });
                    },
                    error: function() {
                        /* boleh diam */ }
                });
            }, 600);

            // Hanya dengarkan 'input' untuk mengurangi duplikasi
            $('#email').on('input', doLookup);
            $('#no_hp').on('input', doLookup);

            // ===== Validasi sebelum submit (tetap seperti punyamu) =====
            $('#donasiForm').on('submit', function(e) {
                const nama = ($('#nama').val() || '').trim();
                const email = ($('#email').val() || '').trim();
                const nohp = cleanPhone($('#no_hp').val());

                if (!nama) {
                    e.preventDefault();
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Nama wajib diisi'
                    });
                }
                if (email && !emailRegex.test(email)) {
                    e.preventDefault();
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Format email tidak valid'
                    });
                }
                if (nohp && nohp.length < 9) {
                    e.preventDefault();
                    return Swal.fire({
                        icon: 'warning',
                        title: 'No HP minimal 9 digit'
                    });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const modalId = urlParams.get("modal");
            const judulSlug = urlParams.get("judul");

            var iklanModalElement = document.getElementById('iklanModal');
            var donasiModalElement = document.getElementById('donasiModal');
            var iklanModal = iklanModalElement ? new bootstrap.Modal(iklanModalElement) : null;
            var donasiModal = donasiModalElement ? new bootstrap.Modal(donasiModalElement) : null;
            var isModalOpen = false;

            // **Cek apakah halaman dibuka dengan parameter modal program**
            if (!modalId && !judulSlug && iklanModal) {
                // **Jika tidak ada modal program, tampilkan modal iklan**
                iklanModal.show();
            }

            // **Klik "Berbagi Sekarang" untuk membuka modal donasi**
            $('#openDonasiModal').on('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                closeAllModals();

                setTimeout(() => {
                    if (donasiModal) {
                        donasiModal.show();
                        isModalOpen = true;
                    }
                }, 300);
            });

            // **Fungsi untuk menutup semua modal terbuka dengan benar**
            function closeAllModals() {
                $('.modal.show').modal('hide'); // Tutup semua modal yang sedang terbuka
                setTimeout(() => {
                    $('.modal-backdrop').remove(); // Hapus backdrop modal setelah animasi selesai
                    $('body').removeClass('modal-open').css({
                        overflow: '',
                        paddingRight: ''
                    }); // Pastikan body bisa di-scroll
                }, 300);
            }

            // **Pastikan modal donasi tertutup dengan bersih**
            $('#donasiModal, #iklanModal').on('hidden.bs.modal', function() {
                isModalOpen = false;
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });

            $('#iklanModal').on('hidden.bs.modal', function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });

            // **Pastikan tidak ada modal yang tertinggal setelah refresh**
            window.addEventListener('beforeunload', function() {
                closeAllModals();
            });
        });

        $(document).ready(function() {
            $('#donasiModal').on('shown.bs.modal', function() {
                $.post('<?php echo e(route('track.donasi.modal')); ?>', {
                    _token: $('meta[name="csrf-token"]').attr('content')
                });
            });
        });

        $(document).ready(function() {
            // Menampilkan modal donasi saat halaman dimuat
            // var donasiModal = new bootstrap.Modal(document.getElementById('donasiModal'));
            // donasiModal.show()

            var programId = null; // Variable untuk id program
            var opsionalValue = null; // Variable untuk opsional (jika ada)

            function formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(amount);
            }

            // Mengklik tombol donasi untuk memilih jumlah
            $('.donasi-btn').on('click', function() {
                var amount = $(this).data('amount');
                $('#total').val(formatRupiah(amount));
                $('#donasiForm').data('amount', amount);
            });

            // Menampilkan form custom donasi saat tombol "Isi Sendiri" diklik
            $('#donasiCustom').on('click', function() {
                $('#customDonasi').show();
            });

            // Memperbarui total donasi jika input customAmount diubah
            $('#customAmount').on('input', function() {
                var amount = $(this).val();
                $('#total').val(formatRupiah(amount));
                $('#donasiForm').data('amount', amount); // Menyimpan jumlah custom
            });

            // Fungsi untuk menangani perubahan jenis donasi
            $('#donasiProgramBtn').on('click', function() {
                $('#program-cards').show();
                $('#opsionalUmum').hide();
            });

            $('#donasiUmumBtn').on('click', function() {
                $('#program-cards').hide();
                $('#opsionalUmum').show();
            });

            /* Perubahan Active Button */
            $('#donasiProgramBtn, #donasiUmumBtn').on('click', function() {
                $('#donasiProgramBtn, #donasiUmumBtn').removeClass('selected-btn');
                $(this).addClass('selected-btn');
            });

            $('[name="opsional_umum"]').on('click', function() {
                $('[name="opsional_umum"]').removeClass('selected-btn');
                $(this).addClass('selected-btn');
                var opsionalValue = $(this).data('value');
                $('#opsionalValueInput').val(opsionalValue);
            });

            // Menangani klik pada card program dan mengambil informasi program
            $('#program-cards .program-card').on('click', function() {
                // Ambil nilai program judul dan id
                var programId = $(this).data('program-id'); // ID program
                var programJudul = $(this).data('program'); // Judul program

                // Set ID program ke dalam input hidden untuk dikirim ke server
                $('#programIdInput').val(programId);

                // Menampilkan informasi program terkait
                fetchProgramInfo(programJudul); // Mengirim judul program untuk mencari detail
            });

            // Fungsi untuk mengambil informasi program dari server
            function fetchProgramInfo(programJudul) {
                $.ajax({
                    url: '/get-program-info', // Ganti dengan URL yang sesuai untuk mengambil info program
                    method: 'GET',
                    data: {
                        program: programJudul // Mengirimkan judul program
                    },
                    success: function(response) {
                        if (response.success_percentage) {
                            $('#program-title').text('Deskripsi Program ' + programJudul);
                            // $('#program-description').text(response.description);
                            $('#program-description').text($('<div>').html(response.description)
                                .text());
                            $('#program-statistics').text(response.success_percentage +
                                ' orang telah terdampak dari target ' + response.target +
                                ' penerima manfaat');
                            $('#program-info').show();
                        } else {
                            $('#program-title').text('Program tidak ditemukan');
                            $('#program-description').text('');
                            $('#program-statistics').text('');
                            $('#program-info').hide();
                        }
                    },
                    error: function() {
                        alert('Error fetching program data.');
                    }
                });
            }

            // Menangani pengiriman form donasi
            $('#donasiForm').on('submit', function(e) {
                e.preventDefault(); // Mencegah form agar tidak submit secara normal

	                var amount = $('#donasiForm').data('amount');
	                var programId = $('#programIdInput').val();
	                var opsionalValue = $('#opsionalValueInput').val();
	                var nama = $('#nama').val();
	                var affiliateSub = window.KilauAffiliate ? window.KilauAffiliate.getAffiliateSub() : null;

                if (!amount || amount <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nominal tidak valid',
                        text: 'Silakan pilih atau masukkan jumlah donasi.',
                    });
                    return;
                }

                    var donationParams = window.KilauAnalytics ?
                        window.KilauAnalytics.donationParamsFromForm(this, amount) : {
                            source: 'donation_form',
                            currency: 'IDR',
                            value: Number(amount),
                            amount: Number(amount),
                            donation_type: programId ? 'program' : 'general',
                            program_id: programId,
                            opsional_umum: opsionalValue
                        };

                    function trackDonationEvent(name, extra) {
                        if (!window.trackKilauEvent) return;
                        window.trackKilauEvent(name, Object.assign({
                            form_name: 'donation_home'
                        }, donationParams, extra || {}));
                    }

                    trackDonationEvent('submit_donation_form');

	                $.ajax({
	                    url: '<?php echo e(route('donasi.store')); ?>',
	                    type: 'POST',
	                    data: {
	                        nama: nama,
	                        type_donasi: programId ? 1 : 2,
	                        total: amount,
	                        id_program: programId,
	                        opsional_umum: opsionalValue,
	                        affiliate_sub: affiliateSub,
	                        referral_code: affiliateSub,

	                        no_hp: $('#no_hp').val(),
	                        email: $('#email').val(),
	                        feedback: $('#feedback').val(),
	                        is_anonymous: $('#is_anonymous').is(':checked') ? 1 : 0,

                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        var donasiId = response.donasi_id;
                        trackDonationEvent('donation_saved');

                        Swal.fire({
                            icon: 'success',
                            title: 'Donasi Berhasil Disimpan',
                            text: 'Silakan lanjutkan pembayaran.',
                            confirmButtonText: 'Lanjutkan',
	                        }).then((result) => {
	                            if (result.isConfirmed) {
                                    trackDonationEvent('begin_donation_payment');
	                                // Kirim ke API Midtrans eksternal
		                                $.ajax({
		                                    url: 'https://kilauindonesia.org/api/get_token_midtrans_sb',
		                                    type: 'POST',
		                                    data: {
		                                        order_id: 'donasi-' + donasiId,
		                                        total: amount,
		                                        nama: nama,
		                                        email: $('#email').val(),
		                                        no_hp: $('#no_hp').val(),
		                                        donasi_id: donasiId,
		                                        source: 'kilaucms',
		                                        affiliate_sub: affiliateSub
		                                    },
		                                    success: function(res) {
		                                        if (res.token) {
		                                            snap.pay(res.token, {
	                                                onSuccess: function(
                                                    result) {
                                                    trackDonationEvent('donation_payment_success');
                                                    window.KilauPayment.confirm(
                                                        response.payment_verification_url,
                                                        'Donasi Anda berhasil. Semoga berkah!'
                                                    );

                                                    // Pastikan donasiModal sudah terdefinisi
                                                    var donasiModal =
                                                        bootstrap
                                                        .Modal
                                                        .getInstance(
                                                            document
                                                            .getElementById(
                                                                'donasiModal'
                                                            )
                                                        );
                                                    if (!
                                                        donasiModal
                                                    ) {
                                                        donasiModal
                                                            =
                                                            new bootstrap
                                                            .Modal(
                                                                document
                                                                .getElementById(
                                                                    'donasiModal'
                                                                )
                                                            );
                                                    }
                                                    donasiModal
                                                        .hide();

                                                },
                                                onPending: function() {
                                                    trackDonationEvent('donation_payment_pending');
                                                    Swal.fire({
                                                        icon: 'info',
                                                        title: 'Pembayaran Sedang Diproses',
                                                        text: 'Mohon tunggu hingga pembayaran selesai.',
                                                    });
                                                },
                                                onError: function() {
                                                    trackDonationEvent('donation_payment_failed');
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Pembayaran Gagal',
                                                        text: 'Terjadi kesalahan saat proses pembayaran.',
                                                    });
                                                }
                                            });
                                        } else {
                                            trackDonationEvent('donation_token_failed');
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Gagal Mendapatkan Token',
                                                text: 'Midtrans gagal mengirimkan token pembayaran.'
                                            });
                                        }
                                    },
                                    error: function() {
                                        trackDonationEvent('donation_token_failed');
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal Terhubung ke Midtrans',
                                            text: 'Pastikan koneksi Anda stabil.'
                                        });
                                    }
                                });
                            }
                        });
                    },
                    error: function() {
                        trackDonationEvent('donation_save_failed');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan Donasi',
                            text: 'Silakan coba lagi.'
                        });
                    }
                });
            });

            (function initFromQuery() {
                const params = new URLSearchParams(window.location.search);
                const id = params.get('donasi');
                if (!id) return;

                const modalEl = document.getElementById('donasiModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    modalEl.addEventListener('shown.bs.modal', function onShown() {
                        modalEl.removeEventListener('shown.bs.modal', onShown);

                        // aktifkan mode Program
                        if ($('#donasiProgramBtn').length) $('#donasiProgramBtn').trigger('click');
                        else {
                            $('#program-cards').show();
                            $('#opsionalUmum').hide();
                        }

                        // klik kartu agar handler-mu jalan (set #programIdInput + fetchProgramInfo)
                        const select = () => {
                            const $card = $(
                                `#program-cards .program-card[data-program-id="${id}"]`);
                            if ($card.length) {
                                $card.addClass('selected-btn').trigger('click');
                                return true;
                            }
                            return false;
                        };
                        if (!select()) {
                            let tries = 0;
                            const iv = setInterval(() => {
                                if (select() || ++tries >= 10) clearInterval(iv);
                            }, 120);
                        }
                    }, {
                        once: true
                    });

                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            })();
        });

        $(document).ready(function() {
            // Preview gambar sebelum upload
            $('#commentFile').on('change', function() {
                const file = this.files[0]; // Ambil file yang dipilih
                if (file) {
                    const reader = new FileReader(); // Buat FileReader untuk membaca file
                    reader.onload = function(e) {
                        // Tampilkan pratinjau gambar
                        $('#imagePreview').attr('src', e.target.result).css('display', 'block');
                    };
                    reader.readAsDataURL(file); // Baca file sebagai data URL
                } else {
                    // Reset pratinjau gambar jika file dihapus
                    $('#imagePreview').attr('src', '#').css('display', 'none');
                }
            });
        });

        // JavaScript untuk Efek Hover (Opsional)
        document.querySelector('.tooltip-container').addEventListener('mouseenter', function() {
            const tooltip = this.querySelector('.tooltip');
            tooltip.style.opacity = '1';
            tooltip.style.visibility = 'visible';
        });

        document.querySelector('.tooltip-container').addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.tooltip');
            tooltip.style.opacity = '0';
            tooltip.style.visibility = 'hidden';
        });

        $(document).ready(function() {
            let currentPage = 1;
            let perPage = 6; // Bisa disesuaikan jumlah berita per halaman

            function loadBerita(page = 1) {
                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita?page=${page}&per_page=${perPage}`,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            let beritaHtml = "";

                            response.data.forEach(function(berita, index) {
                                // Filter: Skip berita jika status tidak aktif
                                if (berita.status_berita === "Tidak Aktif") {
                                    return;
                                }
                                let fotoUtama = berita.foto ?
                                    `https://berbagipendidikan.org${berita.foto}` :
                                    "assets/img/no-image.jpg";
                                let foto2 = berita.foto2 ?
                                    `https://berbagipendidikan.org${berita.foto2}` : fotoUtama;
                                let foto3 = berita.foto3 ?
                                    `https://berbagipendidikan.org${berita.foto3}` : fotoUtama;
                                let beritaDetailUrl =
                                    `/berita/${berita.judul.replace(/\s+/g, '-')}`; // Ganti spasi dengan tanda hubung

                                beritaHtml += `
                                    <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch wow fadeIn" data-wow-delay="${0.1 * (index + 1)}s">
                                        <article class="card w-100 h-100" style="border-radius: 0.80rem !important; overflow: hidden; display: flex; flex-direction: column;">

                                        <div class="post-img card-img-top" style="height: 250px; overflow: hidden;">
                                            <div id="carouselBerita${berita.id}" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                <div class="carousel-item active">
                                                <img src="${fotoUtama}" alt="Foto Berita" class="d-block w-100" 
                                                    style="object-fit: cover; height: 250px; 
                                                        border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem; 
                                                        border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                                                </div>
                                                <div class="carousel-item">
                                                <img src="${foto2}" alt="Foto Berita" class="d-block w-100" 
                                                    style="object-fit: cover; height: 250px; 
                                                        border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem; 
                                                        border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                                                </div>
                                                <div class="carousel-item">
                                                <img src="${foto3}" alt="Foto Berita" class="d-block w-100" 
                                                    style="object-fit: cover; height: 250px; 
                                                        border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem; 
                                                        border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                                                </div>
                                            </div>
                                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselBerita${berita.id}" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#carouselBerita${berita.id}" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                                            <h2 class="title card-title">
                                            <a href="${beritaDetailUrl}" class="text-decoration-none">${berita.judul}</a>
                                            </h2>
                                            <div class="d-flex align-items-center mb-3">
                                            <div class="post-meta">
                                                <p class="post-author mb-0">Kilau Indonesia</p>
                                                <p class="post-date mb-0">
                                                <time datetime="${berita.tanggal}">${formatTanggal(berita.tanggal)}</time>
                                                </p>
                                            </div>
                                            </div>
                                        </div>
                                        </article>
                                    </div>
                                    `;

                            });

                            $("#berita-container").html(beritaHtml);
                            generatePagination(response.pagination);
                        } else {
                            $("#berita-container").html(
                                '<p class="text-center">Belum ada berita yang tersedia.</p>');
                        }
                    },
                    error: function() {
                        $("#berita-container").html(
                            '<p class="text-center text-danger">Gagal memuat berita.</p>');
                    }
                });
            }

            function generatePagination(pagination) {
                let paginationHtml = '';
                let maxVisiblePages = 5;

                if (pagination.current_page > 1) {
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="1">First</a></li>`;
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page - 1}">«</a></li>`;
                }

                let startPage = Math.max(1, pagination.current_page - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(pagination.last_page, startPage + maxVisiblePages - 1);

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
                }

                if (pagination.current_page < pagination.last_page) {
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page + 1}">»</a></li>`;
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.last_page}">Last</a></li>`;
                }

                $('#pagination').html(paginationHtml);
            }

            function formatTanggal(dateString) {
                let options = {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                };
                return new Date(dateString).toLocaleDateString("id-ID", options);
            }

            function loadJumlahDonatur() {
                $.ajax({
                    url: "https://kilauindonesia.org/api/donitung?kon=1",
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.message === "success") {
                            $("#jumlahDonatur").text(response.data);
                        } else {
                            $("#jumlahDonatur").text("Gagal Memuat");
                        }
                    },
                    error: function() {
                        $("#jumlahDonatur").text("Error");
                    }
                });
            }

            $(document).on('click', '#pagination a', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                loadBerita(page);
            });

            loadBerita();
            loadJumlahDonatur();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\LandingPageKilau\index.blade.php ENDPATH**/ ?>