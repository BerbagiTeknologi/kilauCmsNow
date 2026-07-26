<?php $__env->startSection('style'); ?>
    <style>
        #editor-container {
            min-height: 150px;
            max-height: 500px;
            overflow-y: auto;
        }

        .table img {
            max-width: 100px;
            height: auto;
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
                                <h4 class="card-title">Data Pimpinan</h4>
                                <button class="btn btn-primary btn-round ms-auto" data-toggle="modal"
                                    data-target="#createPimpinanModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="pimpinan-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Sequence No</th>
                                            <th>Jabatan</th>
                                            <th>Pendidikan</th>
                                            <th>Deskripsi</th>
                                            <th>Foto</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $pimpinan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($loop->iteration); ?></td>
                                                <td><?php echo e($item->nama); ?></td>
                                                <td><?php echo e($item->sequence_tempat); ?></td>
                                                <td><?php echo e($item->jabatan); ?></td>
                                                <td><?php echo e($item->pendidikan); ?></td>
                                                <td><?php echo e(Str::limit($item->deskripsi_diri, 50)); ?></td>
                                                <td>
                                                    <?php if($item->file_pimpinan): ?>
                                                        <img src="<?php echo e(Storage::url($item->file_pimpinan)); ?>"
                                                            alt="Foto Pimpinan">
                                                    <?php else: ?>
                                                        No Image
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge <?php echo e($item->status_pimpinan === 'Aktif' ? 'badge-success' : 'badge-danger'); ?>">
                                                        <?php echo e($item->status_pimpinan); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                            data-target="#editPimpinanModal<?php echo e($item->id); ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                            data-target="#deletePimpinanModal<?php echo e($item->id); ?>">
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
                                            <div class="modal fade" id="editPimpinanModal<?php echo e($item->id); ?>"
                                                tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.pimpinanEdit', $item->id)); ?>"
                                                            method="POST" enctype="multipart/form-data">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Pimpinan</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Nama</label>
                                                                    <input type="text" name="nama"
                                                                        class="form-control" value="<?php echo e($item->nama); ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Sequence No</label>
                                                                    <input type="number" name="sequence_tempat"
                                                                        class="form-control"
                                                                        value="<?php echo e($item->sequence_tempat); ?>" required
                                                                        min="1">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Jabatan</label>
                                                                    <input type="text" name="jabatan"
                                                                        class="form-control" value="<?php echo e($item->jabatan); ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Pendidikan</label>
                                                                    <input type="text" name="pendidikan"
                                                                        class="form-control" value="<?php echo e($item->pendidikan); ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Deskripsi</label>
                                                                    <textarea name="deskripsi_diri" class="form-control"><?php echo e($item->deskripsi_diri); ?></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Foto</label>
                                                                    <input type="file" name="file_pimpinan"
                                                                        class="form-control file-input" accept="image/*"
                                                                        data-preview="#file-preview-edit-<?php echo e($item->id); ?>">
                                                                </div>
                                                                <div class="form-group text-center">
                                                                    <?php if($item->file_pimpinan): ?>
                                                                        <img id="file-preview-edit-<?php echo e($item->id); ?>"
                                                                            src="<?php echo e(Storage::url($item->file_pimpinan)); ?>"
                                                                            alt="Preview"
                                                                            style="width: 200px; height: auto; object-fit: cover; border-radius: 10px;">
                                                                    <?php else: ?>
                                                                        <img id="file-preview-edit-<?php echo e($item->id); ?>"
                                                                            src="#" alt="Preview"
                                                                            style="display: none; width: 200px; height: auto; object-fit: cover; border-radius: 10px;">
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Tutup</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Hapus -->
                                            <div class="modal fade" id="deletePimpinanModal<?php echo e($item->id); ?>"
                                                tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.pimpinanDelete', $item->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus Pimpinan</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menghapus data ini?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Batal</button>
                                                                <button type="submit"
                                                                    class="btn btn-danger">Hapus</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Status -->
                                            <div class="modal fade" id="statusModal<?php echo e($item->id); ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form
                                                            action="<?php echo e(route('profil.pimpinanToggleStatus', $item->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Ubah Status Pimpinan</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <select name="status_pimpinan" class="form-control">
                                                                    <option value="1"
                                                                        <?php echo e($item->status_pimpinan == 1 ? 'selected' : ''); ?>>
                                                                        Aktif</option>
                                                                    <option value="2"
                                                                        <?php echo e($item->status_pimpinan == 2 ? 'selected' : ''); ?>>
                                                                        Tidak Aktif</option>
                                                                </select>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-primary">Ubah
                                                                    Status</button>
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

    <!-- Modal Create -->
    <div class="modal fade" id="createPimpinanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('profil.pimpinanCreate')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pimpinan</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Sequence No</label>
                            <input type="number" name="sequence_tempat" class="form-control" required min="1">
                        </div>
                        <div class="form-group">
                            <label>Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Pendidikan</label>
                            <input type="text" name="pendidikan" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi_diri" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Foto</label>
                            <input type="file" name="file_pimpinan" class="form-control file-input" accept="image/*"
                                data-preview="#file-preview-create">
                        </div>
                        <div class="form-group text-center">
                            <img id="file-preview-create" src="#" alt="Preview"
                                style="display:none; width: 200px; height: auto; object-fit: cover; border-radius: 10px;">
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        $(document).ready(function() {
            $('#pimpinan-table').DataTable();

            $('.file-input').on('change', function() {
                const file = this.files[0];
                const previewSelector = $(this).data('preview');

                if (file) {
                    if (!file.type.startsWith('image/')) {
                        alert("Harap unggah file gambar yang valid (JPEG, PNG, GIF).");
                        $(this).val('');
                        return;
                    }

                    const img = new Image();
                    img.src = URL.createObjectURL(file);
                    img.onload = function() {
                        if (img.width < img.height) {
                            // Jika gambar lebih tinggi dari lebarnya (portrait)
                            $(previewSelector).attr('src', img.src).css({
                                'display': 'block',
                                'width': '150px',
                                'height': '200px',
                                'object-fit': 'cover',
                                'border-radius': '10px'
                            });
                        } else {
                            alert("Harap unggah gambar dalam orientasi **potrait**.");
                            $(previewSelector).attr('src', '#').css('display', 'none');
                            $('.file-input').val(''); // Reset input file
                        }
                    };
                } else {
                    $(previewSelector).attr('src', '#').css('display', 'none');
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\Profile\Pimpinan\index.blade.php ENDPATH**/ ?>