<?php $__env->startSection('style'); ?>
    <style>
        .text-section {
            text-align: center;
            margin-bottom: 10px;
        }

        .pimpinan-container {
            text-align: center;
        }

        .pimpinan-img-large {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .pimpinan-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .pimpinan-img:hover {
            transform: scale(1.1);
        }

        .pimpinan-name {
            font-size: 1.3rem;
            font-weight: bold;
        }

        .pimpinan-jabatan {
            font-size: 1rem;
            color: #666;
        }

        /* Mengurangi padding top dan bottom agar tidak terlalu jauh */
        .struktur-section {
            padding-top: 40px;
            padding-bottom: 20px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Hero Start -->
    <div class="container-fluid pt-5 bg-primary hero-header">
        <div class="container pt-5">
            <div class="row g-5 pt-5">
                <div class="col-12 text-center" style="margin-top: 100px !important;">
                    <h1 class="display-4 text-white mb-4">Pimpinan Kilau Indonesia</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Profil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Pimpinan Kilau Indonesia</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Struktur Pimpinan -->
    <div class="container-fluid struktur-section">
        <div class="container">
            <div class="row g-5">
                <div class="col-12 text-section wow fadeIn" data-wow-delay="0.1s">
                    <div class="btn btn-sm border rounded-pill text-primary px-3 mb-3">Pimpinan Kilau Indonesia</div>
                    <h1 class="mb-4"><?php echo e($pimpinanMenu->judul); ?></h1>
                    <p class="struktur-description mb-4"><?php echo e($pimpinanMenu->subjudul); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pimpinan Section Start -->
    <div class="container-fluid">
        <div class="container">
            <div class="row justify-content-center mb-3">
                <?php $__currentLoopData = $pimpinans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pimpinan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($pimpinan->sequence_tempat == 1): ?>
                        <div class="col-12 text-center">
                            <img src="<?php echo e($pimpinan->file_pimpinan ? Storage::url($pimpinan->file_pimpinan) : asset('assets/img/profile.jpg')); ?>"
                                class="pimpinan-img-large" 
                                alt="<?php echo e($pimpinan->nama); ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#modalPimpinan<?php echo e($pimpinan->id); ?>">
                            <div class="pimpinan-name"><?php echo e($pimpinan->nama); ?></div>
                            <div class="pimpinan-jabatan"><?php echo e($pimpinan->jabatan); ?></div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="row justify-content-center mb-5">
                <?php $__currentLoopData = $pimpinans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pimpinan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(in_array($pimpinan->sequence_tempat, [2, 3])): ?>
                        <div class="col-md-4 text-center">
                            <img src="<?php echo e($pimpinan->file_pimpinan ? Storage::url($pimpinan->file_pimpinan) : asset('assets/img/profile.jpg')); ?>"
                                class="pimpinan-img" 
                                alt="<?php echo e($pimpinan->nama); ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#modalPimpinan<?php echo e($pimpinan->id); ?>">
                            <div class="pimpinan-name"><?php echo e($pimpinan->nama); ?></div>
                            <div class="pimpinan-jabatan"><?php echo e($pimpinan->jabatan); ?></div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="row justify-content-center">
                <?php $__currentLoopData = $pimpinans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pimpinan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(in_array($pimpinan->sequence_tempat, [4, 5, 6, 7])): ?>
                        <div class="col-md-3 col-sm-6 text-center mb-4">
                            <img src="<?php echo e($pimpinan->file_pimpinan ? Storage::url($pimpinan->file_pimpinan) : asset('assets/img/profile.jpg')); ?>"
                                class="pimpinan-img" 
                                alt="<?php echo e($pimpinan->nama); ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#modalPimpinan<?php echo e($pimpinan->id); ?>">
                            <div class="pimpinan-name"><?php echo e($pimpinan->nama); ?></div>
                            <div class="pimpinan-jabatan"><?php echo e($pimpinan->jabatan); ?></div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <div class="py-5"></div>

    <?php $__currentLoopData = $pimpinans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pimpinan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="modalPimpinan<?php echo e($pimpinan->id); ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo e($pimpinan->id); ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel<?php echo e($pimpinan->id); ?>"><?php echo e($pimpinan->nama); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="<?php echo e($pimpinan->file_pimpinan ? Storage::url($pimpinan->file_pimpinan) : asset('assets/img/profile.jpg')); ?>"
                            class="img-fluid mb-3" 
                            alt="<?php echo e($pimpinan->nama); ?>">
                        <p><strong>Jabatan:</strong> <?php echo e($pimpinan->jabatan); ?></p>
                        <p><strong>Deskripsi:</strong> <?php echo e($pimpinan->deskripsi_diri); ?></p>
                        <p><strong>Pendidikan:</strong> <?php echo e($pimpinan->pendidikan); ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\LandingPageKilau\Profile\profilePimpinan.blade.php ENDPATH**/ ?>