<?php $__env->startSection('style'); ?>
    <style>
        #editor-container {
            min-height: 150px;
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
    <!-- Flipbook StyleSheet -->
    <link href="<?php echo e(asset('assets_flipbox/css/dflip.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('assets_flipbox/css/themify-icons.min.css')); ?>" rel="stylesheet" type="text/css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Data Dokumen</h4>
                                <button class="btn btn-primary btn-round ms-auto" data-toggle="modal"
                                    data-target="#createDokumenModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="legalitas-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Legalitas Lembaga</th>
                                            <th>File</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $legalitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($loop->iteration); ?></td>
                                               <td> <?php echo e(strip_tags( $document->judul)); ?></td>
                                                <td>
                                                    <?php if($document->file_legalitas): ?>
                                                        <?php
                                                            $file_legalitas = json_decode($document->file_legalitas); // Mendecode JSON menjadi array
                                                        ?>

                                                        <?php $__currentLoopData = $file_legalitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                                                $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                                            ?>

                                                            <?php if($fileExtension == 'pdf'): ?>
                                                                <div class="flipbook">
                                                                    <div class="_df_book" height="400" webgl="true"
                                                                        source="<?php echo e(Storage::url($file)); ?>"></div>
                                                                </div>
                                                            <?php elseif(in_array($fileExtension, ['png', 'jpg', 'jpeg', 'gif'])): ?>
                                                                <!-- Gambar akan dikonversi ke PDF di controller -->
                                                                <div class="flipbook">
                                                                    <div class="_df_book" height="400" webgl="true"
                                                                        source="<?php echo e(Storage::url($file)); ?>"></div>
                                                                </div>
                                                            <?php else: ?>
                                                                <a href="<?php echo e(Storage::url($file)); ?>" target="_blank">
                                                                    Lihat File
                                                                </a>
                                                            <?php endif; ?>

                                                            <br> <!-- Menambahkan pemisah antara file -->
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php else: ?>
                                                        No File
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge <?php echo e($document->status_legalitas === 'Aktif' ? 'badge-success' : 'badge-danger'); ?>">
                                                        <?php echo e($document->status_legalitas); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group" aria-label="Basic example">
                                                        <!-- Tombol Edit -->
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editDokumenModal<?php echo e($document->id); ?>"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <!-- Tombol Delete -->
                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteDokumenModal<?php echo e($document->id); ?>"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>

                                                        <!-- Tombol Ubah Status -->
                                                        <button class="btn btn-info btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#statusModal<?php echo e($document->id); ?>"
                                                            title="Ubah Status">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Status -->
                                            <div class="modal fade" id="statusModal<?php echo e($document->id); ?>" tabindex="-1"
                                                role="dialog" aria-labelledby="statusModalLabel<?php echo e($document->id); ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.legalitaslembaga.toggleStatus', $document->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="statusModalLabel<?php echo e($document->id); ?>">
                                                                    Ubah Status Dokumen
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Pilih status untuk dokumen:
                                                                    <strong><?php echo e($document->id); ?></strong>.
                                                                </p>
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="status_legalitas" class="form-control">
                                                                        <option value="1"
                                                                            <?php echo e($document->status_legalitas == 1 ? 'selected' : ''); ?>>
                                                                            Aktif
                                                                        </option>
                                                                        <option value="2"
                                                                            <?php echo e($document->status_legalitas == 2 ? 'selected' : ''); ?>>
                                                                            Tidak Aktif
                                                                        </option>
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

                                            <div class="modal fade" id="editDokumenModal<?php echo e($document->id); ?>" tabindex="-1"
                                                role="dialog" aria-labelledby="editDokumenModalLabel<?php echo e($document->id); ?>" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.legalitaslembaga.edit', $document->id)); ?>" method="POST" enctype="multipart/form-data">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editDokumenModalLabel<?php echo e($document->id); ?>">Edit Dokumen</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Teks Dokumen -->
                                                                <div class="form-group">
                                                                    <label for="text_document_<?php echo e($document->id); ?>">Nama Dokumen</label>
                                                                    <input type="text" name="judul" class="form-control" id="text_document_<?php echo e($document->id); ?>" value="<?php echo e(old('judul', $document->judul)); ?>" required>
                                                                </div>
                                            
                                                                <!-- File Upload -->
                                                                <div class="form-group">
                                                                    <input type="file" name="file_legalitas[]" class="form-control" multiple>
                                                                    <small class="form-text text-muted">
                                                                        * Pastikan file yang diupload berformat PDF agar dapat dilihat di Flipbook.
                                                                    </small>
                                            
                                                                    <!-- Menampilkan file yang sudah ada sebelumnya -->
                                                                    <?php if($document->file_legalitas): ?>
                                                                        <div class="mt-3">
                                                                            <strong>Files sebelumnya:</strong>
                                                                            <ul>
                                                                                <?php $__currentLoopData = json_decode($document->file_legalitas, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                    <li>
                                                                                        <a href="<?php echo e(Storage::url($file)); ?>" target="_blank"><?php echo e(basename($file)); ?></a>
                                                                                    </li>
                                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                            </ul>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteDokumenModal<?php echo e($document->id); ?>"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="deleteDokumenModalLabel<?php echo e($document->id); ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.legalitaslembaga.delete', $document->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteDokumenModalLabel<?php echo e($document->id); ?>">
                                                                    Delete Dokumen</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menghapus dokumen ini?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit"
                                                                    class="btn btn-danger">Delete</button>
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

    <!-- Create Modal -->
    <div class="modal fade" id="createDokumenModal" tabindex="-1" role="dialog"
        aria-labelledby="createDokumenModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form action="<?php echo e(route('profil.legalitaslembaga.create')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="createDokumenModalLabel">Create Dokumen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Dokumen</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <!-- Tambahkan multiple pada input file -->
                            <input type="file" name="file_legalitas[]" class="form-control" multiple>
                            <small class="form-text text-muted">
                                * Pastikan file yang diupload berformat PDF agar dapat dilihat di Flipbook.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('assets_admin/ckeditor/ckeditor.js')); ?>"></script>
    <!-- Flipbook main Js file -->
    <script src="<?php echo e(asset('assets_flipbox/js/dflip.min.js')); ?>" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#legalitas-table').DataTable({
                "pageLength": 10,
                "searching": true,
                "paging": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
            });

            // Inisialisasi CKEditor hanya untuk textarea yang belum diinisialisasi
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('.ckeditor').each(function() {
                    // Cek apakah editor sudah ada
                    if (!CKEDITOR.instances[$(this).attr('id')]) {
                        CKEDITOR.replace(this, {
                            toolbar: [{
                                    name: 'basicstyles',
                                    items: ['Bold', 'Italic', 'Underline', 'Strike',
                                        'Subscript', 'Superscript', '-',
                                        'RemoveFormat'
                                    ]
                                },
                                {
                                    name: 'paragraph',
                                    items: ['NumberedList', 'BulletedList', '-',
                                        'Outdent', 'Indent', '-', 'Blockquote', '-',
                                        'JustifyLeft', 'JustifyCenter',
                                        'JustifyRight', 'JustifyBlock'
                                    ]
                                },
                                {
                                    name: 'links',
                                    items: ['Link', 'Unlink']
                                },
                                {
                                    name: 'insert',
                                    items: ['Image', 'Table', 'HorizontalRule',
                                        'SpecialChar'
                                    ]
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
                            removePlugins: 'notification' // Menghilangkan peringatan
                        });
                    }
                });
            });

            // Inisialisasi flipbook
            $('._df_book').dFlip({
                height: 400,
                webgl: true
            });
        });

        
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\Profile\LegalitasLembaga\index.blade.php ENDPATH**/ ?>