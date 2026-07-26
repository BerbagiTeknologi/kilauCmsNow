<?php $__env->startSection('style'); ?>
    <style>
        .visi-misi-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            text-align: justify;
        }

        .text-section {
            text-align: center;
        }

        .btn-visi-misi {
            background-color: #1363c6;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-visi-misi:hover {
            background-color: #0e4a9e;
        }

        .visi-misi-list {
            list-style-type: none;
            padding-left: 0;
        }

        .visi-misi-list li {
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
        }

        .visi-misi-list li::before {
            content: "•";
            color: #1363c6;
            font-size: 1.5rem;
            position: absolute;
            left: 0;
            top: -5px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Hero Start -->
    <div class="container-fluid pt-5 bg-primary hero-header">
        <div class="container pt-5">
            <div class="row g-5 pt-5">
                <div class="col-12 text-center" style="margin-top: 100px !important;">
                    <h1 class="display-4 text-white mb-4">Visi dan Misi</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Profil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Visi dan Misi</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- Visi dan Misi Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row g-5">
                <!-- Teks Penjelasan Visi dan Misi -->
                <div class="col-12 text-section wow fadeIn" data-wow-delay="0.1s">
                    <div class="btn btn-sm border rounded-pill text-primary px-3 mb-3">Visi Dan Misi</div>
                    <h1 class="mb-4"><?php echo e($visimisiMenu->judul); ?></h1>
                    <p class="sejarah-description mb-4 text-center">
                        <?php echo e($visimisiMenu->subjudul); ?>

                    </p>

                    <!-- Visi -->
                    <h2 class="mt-5 mb-3">Visi</h2>
                    <p class="visi-misi-description mb-4">
                        <?php if($visimisis && $visimisis->isNotEmpty()): ?>
                            <?php echo e(strip_tags($visimisis->first()->visi)); ?>

                        <?php else: ?>
                            Visi tidak tersedia
                        <?php endif; ?>
                    </p>

                    <!-- Misi -->
                    <h2 class="mt-5 mb-3">Misi</h2>
                    <ul class="visi-misi-list">
                        <?php if($visimisis && $visimisis->isNotEmpty() && !empty($visimisis->first()->misi)): ?>
                            <?php $__currentLoopData = explode("\n", $visimisis->first()->misi); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $misi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="visi-misi-description"><?php echo e(strip_tags(trim($misi))); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <li class="visi-misi-description">Misi tidak tersedia</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Visi dan Misi End -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        // Tambahkan script khusus jika diperlukan
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\LandingPageKilau\Profile\profilVisiMisi.blade.php ENDPATH**/ ?>