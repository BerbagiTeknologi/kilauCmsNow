<style>
    /* Styling Timeline */
    .timeline {
        position: relative;
        max-width: 900px;
        margin: auto;
        padding: 20px 0;
    }

    .timeline::after {
        content: "";
        position: absolute;
        width: 4px;
        background-color: #007bff;
        top: 0;
        bottom: 0;
        left: 50%;
        margin-left: -2px;
    }

    .timeline-item {
        position: relative;
        width: 50%;
        padding: 20px;
        box-sizing: border-box;
        cursor: pointer;
    }

    .timeline-item.left {
        left: 0;
        text-align: right;
    }

    .timeline-item.right {
        left: 50%;
        text-align: left;
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        background-color: #fff;
        border: 4px solid #007bff;
        border-radius: 50%;
        top: 15px;
        left: 50%;
        transform: translateX(-50%);
        cursor: pointer;
    }

    .timeline-content {
        padding: 15px;
        background: #fff;
        position: relative;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: start;
        /* Added */
    }

    .timeline-icon {
        font-size: 20px;
        /* Adjusted size */
        color: #1363c6;
        margin-right: 10px;
        /* Added margin */
    }

    .timeline-text {
        flex: 1;
    }

    .timeline-item.left .timeline-icon {
        margin-right: 10px;
        /* For left-aligned items */
    }

    .timeline-item.right .timeline-icon {
        margin-left: 10px;
        /* For right-aligned items */
    }

    /* Responsive */
    @media screen and (max-width: 768px) {
        .timeline::after {
            left: 20px;
        }

        .timeline-item {
            width: 100%;
            padding-left: 50px;
            text-align: left;
        }

        .timeline-item.right {
            left: 0;
        }

        .timeline-item::after {
            left: 20px;
        }
    }
</style>

<!-- Timeline Start -->
<div class="container-fluid bg-light py-5">
    <div class="container py-5">
        <!-- Judul Section -->
        <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 500px">
            <h1 class="mb-4"><?php echo e($timelineMenu->judul); ?></h1>
            <p class="lead"><?php echo e($timelineMenu->subjudul); ?></p>
        </div>

        <!-- Timeline -->
        <div class="timeline">
            <?php $__currentLoopData = $timelins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timeline): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="timeline-item <?php if($loop->index % 2 == 0): ?> left <?php else: ?> right <?php endif; ?> wow fadeInUp" data-wow-delay="0.1s" data-bs-toggle="modal" data-bs-target="#modal<?php echo e($timeline->id); ?>">
                    <div class="timeline-content">
                        <!-- Menampilkan ikon berdasarkan nama yang ada di database -->
                        <i class="fa <?php echo e($timeline->icon_timeline); ?> timeline-icon"></i>
                        <div class="timeline-text">
                            <h5 class="fw-bold"><?php echo e($timeline->judul_timline); ?></h5>
                            <p class="mb-0"><small class="text-muted"><?php echo e($timeline->subjudul_timline); ?></small></p>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modal<?php echo e($timeline->id); ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo e($timeline->id); ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel<?php echo e($timeline->id); ?>"><?php echo e($timeline->judul_timline); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><?php echo e($timeline->deskripsi_timeline); ?></p> <!-- Menampilkan deskripsi lengkap -->
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<!-- Timeline End -->

<?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views/LandingPageKilau/timline_interaktif.blade.php ENDPATH**/ ?>