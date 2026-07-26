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
                                <h4 class="card-title">Tambah Iklan Kilau</h4>
                                <button class="btn btn-primary btn-round ms-auto" id="tambahDataBtn" data-toggle="modal"
                                    data-target="#createIklanKilauModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="iklankilau-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Judul</th>
                                            <th>Jumlah Kantor Cabang</th>
                                            
                                            <th>Deskripsi</th>
                                            <th>File</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $iklanKilau; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($item->judul); ?></td>
                                                <td><?php echo e($item->jumlah_yayasan); ?></td>
                                                
                                                <td><?php echo e(Str::limit($item->deskripsi, 100)); ?></td>
                                                <td>
                                                    <?php if($item->file): ?>
                                                        <img src="<?php echo e(Storage::url($item->file)); ?>" alt="Iklan Kilau File"
                                                            style="width: 100px; height: auto;">
                                                    <?php else: ?>
                                                        No Image
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge <?php echo e($item->status_iklan_kilau === 'Aktif' ? 'badge-success' : 'badge-danger'); ?>">
                                                        <?php echo e($item->status_iklan_kilau); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editIklanKilauModal<?php echo e($item->id); ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteIklanKilauModal<?php echo e($item->id); ?>">
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

                                            <!-- Modal Status -->
                                            <div class="modal fade" id="statusModal<?php echo e($item->id); ?>" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form
                                                            action="<?php echo e(route('profil.iklankilauToggleStatus', $item->id)); ?>"
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
                                                                    <select name="status_kilau" class="form-control"
                                                                        required>
                                                                        <option value="1"
                                                                            <?php echo e($item->status_kilau == 1 ? 'selected' : ''); ?>>
                                                                            Aktif</option>
                                                                        <option value="2"
                                                                            <?php echo e($item->status_kilau == 2 ? 'selected' : ''); ?>>
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
                                            <div class="modal fade" id="deleteIklanKilauModal<?php echo e($item->id); ?>"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.iklankilauDelete', $item->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus Iklan Kilau</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menghapus data ini?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Batal</button>
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
    </div>

    <?php $__currentLoopData = $iklanKilau; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="editIklanKilauModal<?php echo e($item->id); ?>" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="<?php echo e(route('profil.iklankilauEdit', $item->id)); ?>" method="POST"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Iklan Kilau</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <!-- Fields for Judul and Deskripsi -->
                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" name="judul" class="form-control" value="<?php echo e($item->judul); ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" required><?php echo e($item->deskripsi); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Jumlah Kantor Cabang</label>
                                <input type="number" name="jumlah_yayasan" class="form-control" value="<?php echo e($item->jumlah_yayasan); ?>" required>
                            </div>
                           

                            <!-- File Upload (optional) -->
                            <div class="form-group">
                                <label>File (Optional)</label>
                                <input type="file" name="file" class="form-control">
                                <?php if($item->file): ?>
                                    <p>Current File: <a href="<?php echo e(Storage::url($item->file)); ?>"
                                            target="_blank"><?php echo e(basename($item->file)); ?></a></p>
                                <?php endif; ?>
                            </div>

                            <!-- Iklan Kilau List -->
                            <div class="form-group">
                                <label>Iklan Kilau List</label>
                                <div id="iklanKilauListFields">
                                    <?php $__currentLoopData = $item->iklanKilauLists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="iklanKilauListField" style="margin-bottom: 10px !important;">
                                            <input type="hidden" name="iklan_kilau_lists[<?php echo e($index); ?>][id]"
                                                value="<?php echo e($list->id); ?>">
                                            <input type="text" name="iklan_kilau_lists[<?php echo e($index); ?>][name]"
                                                class="form-control mb-2"
                                                value="<?php echo e(old('iklan_kilau_lists.' . $index . '.name', $list->name)); ?>"
                                                placeholder="Name" required>
                                            <select name="iklan_kilau_lists[<?php echo e($index); ?>][status]"
                                                class="form-control mb-2" required>
                                                <option value="Aktif"
                                                    <?php echo e(old("iklan_kilau_lists.$index.status", $list->status_iklan_kilau_list) == 'Aktif' ? 'selected' : ''); ?>>
                                                    Aktif</option>
                                                <option value="Tidak Aktif"
                                                    <?php echo e(old("iklan_kilau_lists.$index.status", $list->status_iklan_kilau_list) == 'Tidak Aktif' ? 'selected' : ''); ?>>
                                                    Tidak Aktif</option>
                                            </select>
                                            <button type="button"
                                                class="btn btn-danger removeIklanKilauList w-100 mt-2">Hapus</button>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <button type="button" class="btn btn-success w-100 mt-2" id="addIklanKilauList">Tambah
                                    Iklan Kilau List</button>
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
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- Modal Create -->
    <div class="modal fade" id="createIklanKilauModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('profil.iklankilauCreate')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Iklan Kilau</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Kantor Cabang</label>
                            <input type="number" name="jumlah_yayasan" class="form-control" required>
                        </div>
                       
                        <div class="form-group">
                            <label>File (Optional)</label>
                            <input type="file" name="file" class="form-control file-input"
                                data-preview="#file-preview">
                        </div>
                        <div class="form-group">
                            <img id="file-preview" src="#" alt="File Preview"
                                style="display:none; width:100%; height:auto;">
                        </div>

                        <!-- Input untuk iklan_kilau_lists -->
                        <div class="form-group">
                            <label>Iklan Kilau List</label>
                            <div id="iklanKilauListFields">
                                <div class="iklanKilauListField" style="margin-bottom: 10px !important;">
                                    <input type="text" name="iklan_kilau_lists[0][name]" class="form-control mb-3"
                                        placeholder="Name" required>
                                    <button type="button"
                                        class="btn btn-danger removeIklanKilauList w-100 mt-2">Hapus</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success w-100 mt-2" id="addIklanKilauList">Tambah Iklan
                                Kilau List</button>
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
            // Menambahkan Iklan Kilau List baru
            $('#addIklanKilauList').on('click', function() {
                var index = $('#iklanKilauListFields .iklanKilauListField').length;
                console.log("Menambahkan data baru dengan index: " + index);

                $('#iklanKilauListFields').append(`
                <div class="iklanKilauListField">
                    <input type="hidden" name="iklan_kilau_lists[${index}][id]" value=""> 
                    <input type="text" name="iklan_kilau_lists[${index}][name]" class="form-control mb-2" placeholder="Nama Iklan" required>
                    <select name="iklan_kilau_lists[${index}][status]" class="form-control mb-2" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                    <button type="button" class="btn btn-danger removeIklanKilauList w-100 mt-4">Hapus</button>
                </div>
            `);
            });

            // Fungsi untuk menghapus inputan Iklan Kilau List
            $(document).on('click', '.removeIklanKilauList', function() {
                $(this).closest('.iklanKilauListField').remove();
            });

            // Fungsi untuk menyembunyikan/tampilkan tombol tambah data berdasarkan jumlah data di tabel
            function toggleTambahDataBtn() {
                if ($('#iklankilau-table tbody tr').length > 0) {
                    $('#tambahDataBtn').hide(); // Sembunyikan tombol jika ada data
                } else {
                    $('#tambahDataBtn').show(); // Tampilkan tombol jika tidak ada data
                }
            }

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

            // Panggil fungsi toggleTambahDataBtn untuk kontrol visibilitas tombol "Tambah Data"
            toggleTambahDataBtn();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\Profile\Iklan\index.blade.php ENDPATH**/ ?>