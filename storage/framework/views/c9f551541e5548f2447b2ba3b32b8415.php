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
                                <h4 class="card-title">Data Sejarah</h4>
                                <button class="btn btn-primary btn-round ms-auto" id="tambahDataBtn" data-toggle="modal"
                                    data-target="#createSejarahModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="sejarah-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Deskripsi</th>
                                            <th>File</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $sejarah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo Str::limit($item->deskripsi_sejarah, 100); ?></td>
                                                <td>
                                                    <?php if($item->nama_file): ?>
                                                        <img src="<?php echo e(Storage::url($item->nama_file)); ?>" alt="Tentang Kami File"
                                                            style="width: 100px; height: auto;">
                                                    <?php else: ?>
                                                        No Image
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge <?php echo e($item->status_sejarah === 'Aktif' ? 'badge-success' : 'badge-danger'); ?>">
                                                        <?php echo e($item->status_sejarah); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editSejarahModal<?php echo e($item->id); ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteSejarahModal<?php echo e($item->id); ?>">
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
                                            <div class="modal fade" id="editSejarahModal<?php echo e($item->id); ?>" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.sejarahEdit', $item->id)); ?>"
                                                            method="POST" enctype="multipart/form-data">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Sejarah</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Deskripsi</label>
                                                                    <textarea name="deskripsi_sejarah" class="form-control ckeditor" required><?php echo e($item->deskripsi_sejarah); ?></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>File (Optional)</label>
                                                                    <input type="file" name="nama_file"
                                                                        class="form-control">
                                                                    <?php if($item->nama_file): ?>
                                                                        <p>File saat ini: <a
                                                                                href="<?php echo e(Storage::url($item->nama_file)); ?>"
                                                                                target="_blank"><?php echo e(basename($item->nama_file)); ?></a>
                                                                        </p>
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

                                            <!-- Modal Status -->
                                            <div class="modal fade" id="statusModal<?php echo e($item->id); ?>" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.sejarahToggleStatus', $item->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Ubah Status</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="status_sejarah" class="form-control"
                                                                        required>
                                                                        <option value="1"
                                                                            <?php echo e($item->status_sejarah == '1' ? 'selected' : ''); ?>>
                                                                            Aktif</option>
                                                                        <option value="2"
                                                                            <?php echo e($item->status_sejarah == '2' ? 'selected' : ''); ?>>
                                                                            Tidak Aktif</option>
                                                                    </select>
                                                                </div>
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

                                            <!-- Modal Delete -->
                                            <div class="modal fade" id="deleteSejarahModal<?php echo e($item->id); ?>"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.sejarahDelete', $item->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus Sejarah</h5>
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
    <div class="modal fade" id="createSejarahModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('profil.sejarahCreate')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Sejarah</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi_sejarah" class="form-control ckeditor" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>File (Optional)</label>
                            <input type="file" name="nama_file" class="form-control file-input"
                                data-preview="#file-preview">
                        </div>
                        <div class="form-group">
                            <img id="file-preview" src="#" alt="File Preview"
                                style="display:none; width: 100px; height: auto;">
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
    <script src="<?php echo e(asset('assets_admin/ckeditor/ckeditor.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            $('#sejarah-table').DataTable();

            // Inisialisasi CKEditor untuk deskripsi sejarah
            function initCKEditor(selector) {
                $(selector).each(function() {
                    const editorId = $(this).attr('id');

                    // Jika CKEditor sudah diinisialisasi pada elemen, hapus terlebih dahulu
                    if (CKEDITOR.instances[editorId]) {
                        CKEDITOR.instances[editorId].destroy(true);
                    }

                    // Inisialisasi CKEditor pada elemen
                    CKEDITOR.replace(editorId, {
                        toolbar: [{
                                name: 'basicstyles',
                                items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat']
                            },
                            {
                                name: 'paragraph',
                                items: ['NumberedList', 'BulletedList', '-', 'Outdent',
                                    'Indent', '-', 'Blockquote'
                                ]
                            },
                            {
                                name: 'links',
                                items: ['Link', 'Unlink']
                            },
                            {
                                name: 'insert',
                                items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar']
                            },
                            {
                                name: 'styles',
                                items: ['Styles', 'Format', 'Font', 'FontSize']
                            },
                            {
                                name: 'colors',
                                items: ['TextColor', 'BGColor']
                            },
                            {
                                name: 'tools',
                                items: ['Maximize', 'ShowBlocks']
                            },
                            {
                                name: 'document',
                                items: ['Source']
                            }
                        ],
                        height: 200,
                        removePlugins: 'notification'
                    });
                });
            }

            // Inisialisasi CKEditor untuk textarea dengan class 'ckeditor'
            initCKEditor('.ckeditor');

            // Re-inisialisasi saat modal dibuka (untuk modal edit)
            $('.modal').on('shown.bs.modal', function() {
                initCKEditor('.ckeditor');
            });

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

<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\Profile\Sejarah\index.blade.php ENDPATH**/ ?>