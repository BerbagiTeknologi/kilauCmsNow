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
                                <h4 class="card-title">Data Donasi Iklan</h4>
                                <button class="btn btn-primary btn-round ms-auto" id="tambahDataBtn" data-toggle="modal"
                                    data-target="#createStrukturModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="struktur-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama Iklan Donasi</th>
                                            <th>File Iklan</th>
                                            <th>Icon Donasi</th>
                                            <th>Nama Button Donasi</th>
                                            <th>Link</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $donasiiklan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($item->nama); ?></td>
                                                <td>
                                                    <?php if($item->file): ?>
                                                        <img src="<?php echo e(Storage::url($item->file)); ?>" alt="Struktur File"
                                                            style="width: 100px; height: auto;">
                                                    <?php else: ?>
                                                        No Image
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <!-- Menampilkan ikon berdasarkan nama yang ada di database -->
                                                    <?php if($item->icon_iklan): ?>
                                                        <i class="fa <?php echo e($item->icon_iklan); ?>"
                                                            style="font-size: 30px; color: #1363c6;"></i>
                                                    <?php else: ?>
                                                        No Icon
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($item->name_button_iklan ?? "No Name"); ?></td>
                                                <td><?php echo e($item->link); ?></td>
                                                <td>
                                                    <span class="badge <?php echo e($item->status_donasi_iklan === 'Aktif' ? 'badge-success' : 'badge-danger'); ?>">
                                                        <?php echo e($item->status_donasi_iklan); ?>

                                                    </span>
                                                </td>  
                                                                                              
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editStrukturModal<?php echo e($item->id); ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteStrukturModal<?php echo e($item->id); ?>">
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
                                            <div class="modal fade" id="editStrukturModal<?php echo e($item->id); ?>"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.iklandonasiEdit', $item->id)); ?>"
                                                            method="POST" enctype="multipart/form-data">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Donasi Iklan</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Nama Donasi</label>
                                                                    <input type="text" name="nama"
                                                                        class="form-control"
                                                                        value="<?php echo e($item->nama); ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="text-danger">File (Optional) (Hanya PNG yang diperbolehkan)</label>
                                                                    <input type="file" name="file" class="form-control file-input" accept="image/png,image/jpeg,image/jpg" data-preview="#file-preview-edit-<?php echo e($item->id); ?>">
                                                                    
                                                                    <?php if($item->file): ?>
                                                                        <img id="file-preview-edit-<?php echo e($item->id); ?>" src="<?php echo e(Storage::url($item->file)); ?>" alt="Preview" style="width: 100px; height: auto; display:block;">
                                                                    <?php else: ?>
                                                                        <img id="file-preview-edit-<?php echo e($item->id); ?>" src="#" alt="Preview" style="width: 100px; height: auto; display:none;">
                                                                    <?php endif; ?>
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label>Nama Tombol (Opsional)</label>
                                                                    <input type="text" name="name_button_iklan" class="form-control" value="<?php echo e($item->name_button_iklan); ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Link (Opsional)</label>
                                                                    <input type="text" name="link" class="form-control" value="<?php echo e($item->link); ?>" placeholder="https://contoh.com">
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label>Pilih Ikon</label>
                                                                    <div id="icons-container-edit-<?php echo e($item->id); ?>" class="d-flex flex-wrap gap-2">
                                                                        <?php
                                                                            $icons = ['fa-home', 'fa-cog', 'fa-heart', 'fa-star', 'fa-book', 'fa-user', 'fa-flag', 'fa-rocket', 'fa-cloud', 'fa-check-circle', 'fa-envelope', 'fa-comment', 'fa-thumbs-up', 'fa-camera', 'fa-bell', 'fa-music', 'fa-paint-brush', 'fa-phone', 'fa-calendar', 'fa-laptop', 'fa-desktop', 'fa-sun', 'fa-moon', 'fa-snowflake', 'fa-shield-alt', 'fa-gift', 'fa-map', 'fa-users', 'fa-money-bill-wave', 'fa-puzzle-piece', 'fa-lightbulb'];
                                                                        ?>
                                                                        <?php $__currentLoopData = $icons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $icon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <div class="icon-select-edit m-1 p-2 border" style="cursor:pointer;">
                                                                                <i class="fa <?php echo e($icon); ?> fa-lg <?php echo e($item->icon_iklan == $icon ? 'text-primary' : ''); ?>"></i>
                                                                            </div>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </div>
                                                                    <input type="hidden" id="selected-icon-value-edit-<?php echo e($item->id); ?>" name="icon_iklan" value="<?php echo e($item->icon_iklan); ?>">
                                                                    <div class="mt-2">Ikon dipilih: <i id="selected-icon-edit-<?php echo e($item->id); ?>" class="fa <?php echo e($item->icon_iklan); ?>"></i></div>
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
                                                        <form
                                                            action="<?php echo e(route('profil.iklandonasiToggleStatus', $item->id)); ?>"
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
                                                                    <select name="statuskilauiklan" class="form-control"
                                                                        required>
                                                                        <option value="1"
                                                                            <?php echo e($item->statuskilauiklan == 1 ? 'selected' : ''); ?>>
                                                                            Aktif</option>
                                                                        <option value="2"
                                                                            <?php echo e($item->statuskilauiklan == 2 ? 'selected' : ''); ?>>
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
                                            <div class="modal fade" id="deleteStrukturModal<?php echo e($item->id); ?>"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('profil.iklandonasiDelete', $item->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus Struktur</h5>
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
    <div class="modal fade" id="createStrukturModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('profil.iklandonasiCreate')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Donasi Iklan</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Donasi Iklan</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="text-danger">File (Optional) (Hanya PNG yang diperbolehkan)</label>
                            <input type="file" name="file" class="form-control file-input" accept="" data-preview="#file-preview-create">
                        </div>                        
                        <div class="form-group">
                            <img id="file-preview-create" src="#" alt="Preview" style="display:none; width: 100px; height: auto;">
                        </div>
                        <div class="form-group">
                            <label>Nama Tombol (Opsional)</label>
                            <input type="text" name="name_button_iklan" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Link (Opsional)</label>
                            <input type="text" name="link" class="form-control" placeholder="https://contoh.com">
                        </div>
                        
                        
                        <div class="form-group">
                            <label>Pilih Ikon</label>
                            <div id="icons-container-create" class="d-flex flex-wrap gap-2">
                                <?php
                                    $icons = ['fa-home', 'fa-cog', 'fa-heart', 'fa-star', 'fa-book', 'fa-user', 'fa-flag', 'fa-rocket', 'fa-cloud', 'fa-check-circle', 'fa-envelope', 'fa-comment', 'fa-thumbs-up', 'fa-camera', 'fa-bell', 'fa-music', 'fa-paint-brush', 'fa-phone', 'fa-calendar', 'fa-laptop', 'fa-desktop', 'fa-sun', 'fa-moon', 'fa-snowflake', 'fa-shield-alt', 'fa-gift', 'fa-map', 'fa-users', 'fa-money-bill-wave', 'fa-puzzle-piece', 'fa-lightbulb'];
                                ?>
                                <?php $__currentLoopData = $icons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $icon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="icon-select-create m-1 p-2 border" style="cursor:pointer;">
                                        <i class="fa <?php echo e($icon); ?> fa-lg"></i>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <input type="hidden" id="selected-icon-value-create" name="icon_iklan">
                            <div class="mt-2">Ikon dipilih: <i id="selected-icon-create" class="fa"></i></div>
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
            $('#struktur-table').DataTable();

            // Fungsi Preview Gambar untuk Modal Create & Edit
            $('.file-input').on('change', function () {
                const file = this.files[0];
                const previewSelector = $(this).data('preview');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $(previewSelector).attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $(previewSelector).hide();
                }
            });


            // Event klik pada icon di modal create
            $('#icons-container-create .icon-select-create').on('click', function () {
                var selectedIcon = $(this).find('i').attr('class').split(' ')[1]; // Ambil nama icon
                
                // Tampilkan di preview
                $('#selected-icon-create').attr('class', 'fa ' + selectedIcon);
                $('#selected-icon-create').css('color', '#1363c6');

                // Simpan nilai icon ke input hidden
                $('#selected-icon-value-create').val(selectedIcon);
            });

             // EDIT: Klik icon per modal edit
            <?php $__currentLoopData = $donasiiklan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                $('#icons-container-edit-<?php echo e($item->id); ?> .icon-select-edit').on('click', function () {
                    let selectedIcon = $(this).find('i').attr('class').split(' ')[1];
                    $('#selected-icon-edit-<?php echo e($item->id); ?>').attr('class', 'fa ' + selectedIcon).css('color', '#1363c6');
                    $('#selected-icon-value-edit-<?php echo e($item->id); ?>').val(selectedIcon);
                    $('#icons-container-edit-<?php echo e($item->id); ?> .icon-select-edit i').removeClass('text-primary');
                    $(this).find('i').addClass('text-primary');
                });
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\Profile\DonasiIklan\index.blade.php ENDPATH**/ ?>