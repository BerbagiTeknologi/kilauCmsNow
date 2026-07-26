<?php
    $groupedGaleri = $galeri->groupBy('judul_kegiatan');
?>

<?php $__currentLoopData = $groupedGaleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $judul => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $firstItem = $items->first();
        $imageUrls = $items->flatMap(function($item) {
            return collect($item->file_galeri)->map(function($image) {
                return asset('storage/' . $image);
            });
        })->toArray();
    ?>

    <div class="gallery-item" data-title="<?php echo e($judul); ?>"
        data-images="<?php echo e(json_encode($imageUrls)); ?>"
        data-description="<?php echo e($firstItem->deskripsi_kegiatan); ?>"
        data-cabang="<?php echo e($firstItem->nama_kantor_cabang); ?>">
        <img src="<?php echo e($imageUrls[0]); ?>" alt="<?php echo e($judul); ?>" />
        <div class="gallery-caption"><?php echo e($judul); ?></div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\LandingPageKilau\galeri-items.blade.php ENDPATH**/ ?>