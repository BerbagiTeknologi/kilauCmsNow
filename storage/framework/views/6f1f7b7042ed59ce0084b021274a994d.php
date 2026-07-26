<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Kilau App Shalter</title>
    <link href="<?php echo e(asset('assets/img/LogoKilau2.png')); ?>" rel="icon" />
 
     <!-- CSS Files -->
     <link rel="stylesheet" href="<?php echo e(asset('assets_admin/css/bootstrap.min.css')); ?>" />
     <link rel="stylesheet" href="<?php echo e(asset('assets_admin/css/plugins.min.css')); ?>" />
     <link rel="stylesheet" href="<?php echo e(asset('assets_admin/css/kaiadmin.min.css')); ?>" />
 
    
     <link
     href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css"
     rel="stylesheet"
   />

     <!-- CSS Just for demo purpose, don't include it in your project -->
     <link rel="stylesheet" href="<?php echo e(asset('assets_admin/css/demo.css')); ?>" />

     <?php echo $__env->yieldContent('style'); ?>

</head>
<body>
   <?php echo $__env->yieldContent('content'); ?>

</body>
   <!--   Core JS Files   -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script src="<?php echo e(asset('assets_admin/js/core/jquery-3.7.1.min.js')); ?>"></script>
   <script src="<?php echo e(asset('assets_admin/js/core/popper.min.js')); ?>"></script>
   <script src="<?php echo e(asset('assets_admin/js/core/bootstrap.min.js')); ?>"></script>

   <!-- jQuery Scrollbar -->
   <script src="<?php echo e(asset('assets_admin/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js')); ?>"></script>

   <?php if($message = Session::get('success')): ?>
   <script>
       Swal.fire({
           icon: "success",
           title: "Berhasil",
           text: "<?php echo e($message); ?>",
       });
   </script>
   <?php endif; ?>

   <?php if($message = Session::get('error')): ?>
   <script>
       Swal.fire({
           icon: "error",
           title: "Gagal",
           text: "<?php echo e($message); ?>",
       });
   </script>
   <?php endif; ?>
   
   <?php echo $__env->yieldContent('scripts'); ?>
</html>
<?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views/Auth/App/master.blade.php ENDPATH**/ ?>