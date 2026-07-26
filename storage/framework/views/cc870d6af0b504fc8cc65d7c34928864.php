<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Settings Menu</h4>
                                <button class="btn btn-primary btn-round ms-auto" data-toggle="modal"
                                    data-target="#createModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="settings-menu-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul</th>
                                            <th>Subjudul</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $settingsMenus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($loop->iteration); ?></td>
                                                <td><?php echo e($menu->judul); ?></td>
                                                <td><?php echo e($menu->subjudul); ?></td>
                                                <td>
                                                    <span
                                                        class="badge <?php echo e($menu->status == 'Aktif' ? 'badge-success' : 'badge-danger'); ?>">
                                                        <?php echo e($menu->status); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group" aria-label="Basic example">
                                                        <!-- Tombol Edit -->
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal" data-target="#editModal<?php echo e($menu->id); ?>"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <!-- Tombol Delete -->
                                                       

                                                        <!-- Tombol Toggle Status -->
                                                        <button class="btn btn-info btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#statusModal<?php echo e($menu->id); ?>"
                                                            title="Toggle Status">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                             <!-- Modal Status -->
                                             <div class="modal fade" id="statusModal<?php echo e($menu->id); ?>" tabindex="-1"
                                                role="dialog" aria-labelledby="statusModalLabel<?php echo e($menu->id); ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('settingsmenu.toggleStatus', $menu->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="statusModalLabel<?php echo e($menu->id); ?>">
                                                                    Ubah Status Settings Menu
                                                                </h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Pilih status untuk Settings Menu:
                                                                    <strong><?php echo e($menu->judul); ?></strong>.
                                                                </p>
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="status" class="form-control">
                                                                        <option value="1"
                                                                            <?php echo e($menu->status == 1 ? 'selected' : ''); ?>>
                                                                            Aktif</option>
                                                                        <option value="2"
                                                                            <?php echo e($menu->status == 2 ? 'selected' : ''); ?>>
                                                                            Tidak Aktif</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal<?php echo e($menu->id); ?>" tabindex="-1"
                                                role="dialog" aria-labelledby="editModalLabel<?php echo e($menu->id); ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('settingsmenu.edit', $menu->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editModalLabel<?php echo e($menu->id); ?>">Edit Settings
                                                                    Menu</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Judul</label>
                                                                    <input type="text" name="judul"
                                                                        class="form-control" value="<?php echo e($menu->judul); ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Subjudul</label>
                                                                    <input type="text" name="subjudul"
                                                                        class="form-control" value="<?php echo e($menu->subjudul); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save
                                                                    changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Delete Modal -->
                                           

                                           
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

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?php echo e(route('settingsmenu.create')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Settings Menu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Subjudul</label>
                            <input type="text" name="subjudul" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        $(document).ready(function() {
            $('#settings-menu-table').DataTable();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\SettingsMenu\index.blade.php ENDPATH**/ ?>