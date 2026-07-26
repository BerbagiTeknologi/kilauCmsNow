<?php $__env->startSection('style'); ?>
    <style>
        #editor-container {
            min-height: 150px;
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Data Home Kilau</h4>
                                <button class="btn btn-primary btn-round ms-auto" data-toggle="modal"
                                    data-target="#createTentangKamiModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="home-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul</th>
                                            <th>File</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $homeKilau; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($loop->iteration); ?></td>
                                                <td><?php echo e($item->judul_home); ?></td>
                                                <td>
                                                    <?php if($item->file_home): ?>
                                                        <img src="<?php echo e(Storage::url($item->file_home)); ?>" alt="Tentang Kami File"
                                                            style="width: 100px; height: auto;">
                                                    <?php else: ?>
                                                        No Image
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge <?php echo e($item->status_home_kilau === 'Aktif' ? 'badge-success' : 'badge-danger'); ?>">
                                                        <?php echo e($item->status_home_kilau); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editTentangKamiModal<?php echo e($item->id); ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteTentangKamiModal<?php echo e($item->id); ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>

                                                        <button class="btn btn-info btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#statusModal<?php echo e($item->id); ?>">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Edit -->
                                            <div class="modal fade" id="editTentangKamiModal<?php echo e($item->id); ?>" tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.homekilauEdit', $item->id)); ?>" method="POST" enctype="multipart/form-data">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Tentang Kami</h5>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Judul</label>
                                                                    <input type="text" name="judul_home" class="form-control" value="<?php echo e($item->judul_home); ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>File (Optional)</label>
                                                                    <input type="file" name="file_home" class="form-control">
                                                                    <?php if($item->file_home): ?>
                                                                        <p>Current File: <a href="<?php echo e(Storage::url($item->file_home)); ?>" target="_blank"><?php echo e(basename($item->file_home)); ?></a></p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Status -->
                                            <div class="modal fade" id="statusModal<?php echo e($item->id); ?>" tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.homekilauToggleStatus', $item->id)); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Ubah Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="status_home_kilau" class="form-control">
                                                                        <option value="1" <?php echo e($item->status_home_kilau == 1 ? 'selected' : ''); ?>>Aktif</option>
                                                                        <option value="2" <?php echo e($item->status_home_kilau == 2 ? 'selected' : ''); ?>>Tidak Aktif</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Delete -->
                                            <div class="modal fade" id="deleteTentangKamiModal<?php echo e($item->id); ?>" tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.homekilauDelete', $item->id)); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus Data Tentang Kami</h5>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menghapus data ini?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-danger">Hapus</button>
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

        <!-- Modal Create -->
        <div class="modal fade" id="createTentangKamiModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="<?php echo e(route('profil.homekilauCreate')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Data Tentang Kami</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" name="judul_home" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>File (Optional)</label>
                                <input type="file" name="file_home" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>
    <script>
        $(document).ready(function() {
            $('#home-table').DataTable();

            // Fungsi Preview Gambar
            $('.file-input').on('change', function() {
                const file = this.files[0];
                const previewSelector = $(this).data('preview');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewSelector).attr('src', e.target.result).css('display', 'block');
                    };
                    reader.readAsDataURL(file);
                } else {
                    $(previewSelector).attr('src', '#').css('display', 'none');
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\Profile\HomeKilau\index.blade.php ENDPATH**/ ?>