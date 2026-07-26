<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <strong>Validasi gagal.</strong>
                            <ul class="mb-0 mt-2">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">User Management</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="user-management-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Terdaftar</th>
                                            <th>Login Terakhir</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($loop->iteration); ?></td>
                                                <td><?php echo e($user->name); ?></td>
                                                <td><?php echo e($user->email); ?></td>
                                                <td>
                                                    <span class="badge <?php echo e($user->role === 'admin' ? 'badge-primary' : 'badge-secondary'); ?>">
                                                        <?php echo e($user->role ?? 'user'); ?>

                                                    </span>
                                                </td>
                                                <td><?php echo e(optional($user->created_at)->format('d M Y') ?? '-'); ?></td>
                                                <td><?php echo e(optional($user->last_login_at)->format('d M Y H:i') ?? '-'); ?></td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                        data-toggle="modal"
                                                        data-target="#resetPasswordModal<?php echo e($user->id); ?>"
                                                        title="Reset Password">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="resetPasswordModal<?php echo e($user->id); ?>" tabindex="-1"
                                                role="dialog" aria-labelledby="resetPasswordModalLabel<?php echo e($user->id); ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('admin.users.password.update', $user->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PUT'); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="resetPasswordModalLabel<?php echo e($user->id); ?>">
                                                                    Reset Password User
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="mb-3">
                                                                    User:
                                                                    <strong><?php echo e($user->name); ?></strong>
                                                                    <br>
                                                                    <small><?php echo e($user->email); ?></small>
                                                                </p>
                                                                <div class="form-group">
                                                                    <label>Password Baru</label>
                                                                    <input type="password" name="password"
                                                                        class="form-control" minlength="8" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Konfirmasi Password Baru</label>
                                                                    <input type="password" name="password_confirmation"
                                                                        class="form-control" minlength="8" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        $(document).ready(function() {
            $('#user-management-table').DataTable();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\UserManagement\index.blade.php ENDPATH**/ ?>