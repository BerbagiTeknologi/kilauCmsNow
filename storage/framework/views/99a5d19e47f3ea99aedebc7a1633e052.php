<?php $__env->startSection('style'); ?>
    <style>
        .sejarah-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            text-align: justify;
        }

        .text-section {
            text-align: center;
        }

        .btn-sejarah {
            background-color: #1363c6;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-sejarah:hover {
            background-color: #0e4a9e;
        }

        .visi-misi-list {
            list-style: none;
            /* Hilangkan default bullet point */
            padding-left: 0;
            /* Hilangkan padding kiri default */
        }

        .visi-misi-list li {
            position: relative;
            padding-left: 25px;
            /* Beri ruang untuk ikon atau custom bullet */
            margin-bottom: 10px;
            /* Beri jarak antar misi */
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .about-img {
                width: 100%;
                height: auto;
            }

            .content {
                text-align: center;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Hero Start -->
    <div class="container-fluid pt-5 bg-primary hero-header">
        <div class="container pt-5">
            <div class="row g-5 pt-5">
                <div class="col-12 text-center" style="margin-top: 100px !important;">
                    <h1 class="display-4 text-white mb-4">Tentang Kami</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Tentang Kami</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    
    <?php if($tentangMenu && $tentangMenu->status == 'Aktif'): ?>
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <div class="tooltip-container">
                            <h1 class="display-5 fw-bold mb-2 hover-text"><?php echo e($tentangMenu->judul); ?></h1>
                        </div>
                        <p class="lead mb-4">
                            <?php echo e($tentangMenu->subjudul); ?>

                        </p>
                    </div>
                </div>

                <div class="row gx-4 gy-4 py-3">
                    <?php $__currentLoopData = $tentangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tentang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <!-- Bagian Teks -->
                        <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up"
                            data-aos-delay="200">
                            <div class="content">
                                <h2 class="fw-bold"><?php echo e($tentang->judul_tentang_kami); ?></h2>
                                <p><?php echo e($tentang->deskripsi); ?></p>
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
    

    <!-- Sejarah Yayasan Start -->
    <?php if($sejarahMenu && $sejarahMenu->status == 'Aktif'): ?>
        <div class="container-fluid py-2">
            <div class="container py-2">
                <div class="row g-5">
                    <div class="col-12 text-section wow fadeIn" data-wow-delay="0.1s">
                        
                        <h1 class="mb-4"><?php echo e($sejarahMenu->judul); ?></h1>
                        <p class="sejarah-description mb-4 text-center">
                            <?php echo e($sejarahMenu->subjudul); ?>

                        </p>
                        <p class="sejarah-description mb-4">
                            <?php echo e($sejarahs->first() ? strip_tags($sejarahs->first()->deskripsi_sejarah) : 'Sejarah Tidak Tersedia'); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- Sejarah Yayasan End -->

    <!-- Visi dan Misi Start -->
    <?php if($visimisiMenu && $visimisiMenu->status == 'Aktif'): ?>
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-12 text-section wow fadeIn" data-wow-delay="0.1s">
                        
                        <h1 class="mb-4"><?php echo e($visimisiMenu->judul); ?></h1>
                        <p class="sejarah-description mb-4 text-center">
                            <?php echo e($visimisiMenu->subjudul); ?>

                        </p>

                        <h2 class="mt-5 mb-3">Visi</h2>
                        <p class="visi-misi-description mb-4">
                            <?php echo e(isset($visimisis) && $visimisis->isNotEmpty() ? strip_tags($visimisis->first()->visi) : 'Visi tidak tersedia'); ?>

                        </p>

                        <h2 class="mt-5 mb-3">Misi</h2>
                        <ul class="visi-misi-list">
                            <?php if(isset($visimisis) && $visimisis->isNotEmpty() && !empty($visimisis->first()->misi)): ?>
                                <?php $__currentLoopData = explode("\n", preg_replace('/\.\.\./', '', strip_tags($visimisis->first()->misi))); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $misi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!empty(trim($misi))): ?>
                                        
                                        <li class="visi-misi-description"><?php echo e(trim($misi)); ?></li>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <li class="visi-misi-description">Misi tidak tersedia</li>
                            <?php endif; ?>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        AOS.init();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\LandingPageKilau\tentang.blade.php ENDPATH**/ ?>