<?php $__env->startSection('style'); ?>
    <style>
        .login-page {
            display: flex;
            height: 100vh;
            width: 100vw;
            background-color: #f8f9fa;
            overflow: hidden;
        }
        .login-background {
            flex: 1.5;
            background-image: url('<?php echo e(asset('assets/img/loginpageke2.png')); ?>');
            background-size: cover;
            background-position: center;
            height: 100%;
            position: relative;
        }
        .login-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.95);
            height: 100vh;
        }
        .login-form .card {
            width: 100%;
            max-width: 700px;
            border-radius: 15px;
            padding: 3rem 2.5rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            background: #ffffff;
            position: relative;
            margin-top: 5rem;
        }
        .login-logo {
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            background-color: white;
            padding: 10px;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        .login-logo img {
            height: 100px;
            width: 100px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-control {
            padding: 0.75rem;
            border-radius: 8px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #1363c6;
        }
        .account-check {
            font-size: 0.95rem;
            text-align: center;
            margin-top: 1rem;
        }
        .account-check a {
            color: #1363c6;
            text-decoration: none;
            font-weight: bold;
        }
        .account-check a:hover {
            text-decoration: underline;
        }
        .login-footer {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2rem;
        }
        .login-footer p {
            margin-bottom: 0;
            font-size: 0.9rem;
            color: #777;
        }
        @media (max-width: 768px) {
            .login-background {
                display: none;
            }
            .login-page {
                justify-content: center;
            }
            .login-form {
                flex: 1;
                padding: 2rem;
                max-width: 100%;
            }
            .login-logo {
                top: -50px;
            }
            .login-logo img {
                height: 60px;
                width: 60px;
            }
        }
        @media (max-width: 576px) {
            .login-form .card {
                padding: 1.5rem;
                max-width: 100%;
            }
            .login-footer p {
                font-size: 0.8rem;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="login-page">
        <div class="login-background"></div>
        <div class="login-form">
            <div class="card">
                <div class="login-logo">
                    <img src="<?php echo e(asset('assets/img/LogoKilau2.png')); ?>" alt="Kilau Logo">
                </div>

                <h2 class="text-center mt-5" style="font-weight:500;">Sign Up</h2>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('register.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?php echo e(old('name')); ?>" placeholder="Masukkan Nama" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?php echo e(old('email')); ?>" placeholder="Masukkan Alamat Email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Minimal 8 karakter" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" placeholder="Ulangi Password" required>
                    </div>
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn" style="background-color: #1363c6; color:white;">
                            Daftar
                        </button>
                    </div>
                </form>

                <p class="account-check">
                    Sudah punya akun? <a href="<?php echo e(route('login')); ?>">Sign In</a>.
                </p>
                <div class="login-footer mt-4">
                    <p>© 2025 Berbagi Teknologi. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Auth.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\Auth\register.blade.php ENDPATH**/ ?>