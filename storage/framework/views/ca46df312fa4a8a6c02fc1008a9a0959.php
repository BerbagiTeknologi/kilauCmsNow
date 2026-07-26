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
                                <h4 class="card-title">Data Kontak</h4>
                                <button class="btn btn-primary btn-round ms-auto" data-toggle="modal"
                                    data-target="#createKontakModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="kontak-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Kacab</th>
                                            <th>Alamat</th>
                                            <th>Telephone</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $kontaks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kontak): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($loop->iteration); ?></td>
                                                <td><?php echo e($kontak->nama_kacab); ?></td>
                                                <td><?php echo e($kontak->alamat); ?></td>
                                                <td><?php echo e($kontak->telephone); ?></td>
                                                <td><?php echo e($kontak->email); ?></td>
                                                <td>
                                                    <span
                                                        class="badge <?php echo e($kontak->status_kontak === 'Aktif' ? 'badge-success' : 'badge-danger'); ?>">
                                                        <?php echo e($kontak->status_kontak); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <!-- Tombol Edit -->
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editKontakModal<?php echo e($kontak->id); ?>"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <!-- Tombol Delete -->
                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteKontakModal<?php echo e($kontak->id); ?>"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>

                                                        <!-- Tombol Ubah Status -->
                                                        <button class="btn btn-info btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#statusModal<?php echo e($kontak->id); ?>"
                                                            title="Ubah Status">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Toggle Status -->
                                            <div class="modal fade" id="statusModal<?php echo e($kontak->id); ?>" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('kontak.toggleStatus', $kontak->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Ubah Status Kontak</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Pilih status untuk Kontak:
                                                                    <strong><?php echo e($kontak->nama_kacab); ?></strong>.
                                                                </p>
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="status_kontak" class="form-control">
                                                                        <option value="1"
                                                                            <?php echo e($kontak->status_kontak == 1 ? 'selected' : ''); ?>>
                                                                            Aktif</option>
                                                                        <option value="2"
                                                                            <?php echo e($kontak->status_kontak == 2 ? 'selected' : ''); ?>>
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

                                            <!-- Modal Edit -->
                                            <div class="modal fade" id="editKontakModal<?php echo e($kontak->id); ?>" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('kontak.edit', $kontak->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Kontak</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Nama Kacab</label>
                                                                    <input type="text" name="nama_kacab"
                                                                        class="form-control"
                                                                        value="<?php echo e($kontak->nama_kacab); ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Alamat</label>
                                                                    <textarea name="alamat" id="alamat<?php echo e($kontak->id); ?>" class="form-control" rows="3" required><?php echo e($kontak->alamat); ?></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Telephone</label>
                                                                    <input type="text" name="telephone"
                                                                        class="form-control"
                                                                        value="<?php echo e($kontak->telephone); ?>" pattern="[0-9]+"
                                                                        maxlength="15" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Email</label>
                                                                    <input type="email" name="email"
                                                                        class="form-control" value="<?php echo e($kontak->email); ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Map Preview</label>
                                                                    <div id="map-preview<?php echo e($kontak->id); ?>"
                                                                        style="width: 100%; height: 300px; border: 1px solid #ccc;">
                                                                    </div>
                                                                    <input type="hidden" name="maplink"
                                                                        id="maplink<?php echo e($kontak->id); ?>"
                                                                        value="<?php echo e($kontak->maplink); ?>">
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

                                            <!-- Modal Delete -->
                                            <div class="modal fade" id="deleteKontakModal<?php echo e($kontak->id); ?>"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('kontak.delete', $kontak->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus Kontak</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menghapus kontak
                                                                    <strong><?php echo e($kontak->nama_kacab); ?></strong>?
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
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
    <div class="modal fade" id="createKontakModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('kontak.create')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kontak</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Kacab</label>
                            <input type="text" name="nama_kacab" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" id="alamat" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Telephone</label>
                            <input type="text" name="telephone" class="form-control" pattern="[0-9]+" maxlength="15"
                                placeholder="Contoh: 089321XXXXXX" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Map Preview</label>
                            <div id="map-preview" style="width: 100%; height: 300px; border: 1px solid #ccc;"></div>
                            <input type="hidden" name="maplink" id="maplink">
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
    <!-- Include Google Maps JavaScript API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(config('services.google_maps.api_key')); ?>&region=ID&language=id&libraries=places"></script>

    <script>
        $(document).ready(function() {
            $('#kontak-table').DataTable();
            var map; // Variabel untuk menyimpan instance peta
            var marker; // Variabel untuk menyimpan marker

            // Function untuk load preview peta berdasarkan alamat
            function loadMapPreview(alamat, mapPreviewId, maplinkId) {
                if (alamat.length > 5) { // Cek panjang alamat agar tidak terlalu sering request
                    var geocoder = new google.maps.Geocoder();

                    geocoder.geocode({ 'address': alamat }, function(results, status) {
                        if (status === 'OK') {
                            var latitude = results[0].geometry.location.lat();
                            var longitude = results[0].geometry.location.lng();

                            // Hapus peta lama jika ada
                            if (map) {
                                map = null;
                                marker = null;
                            }

                            // Initialize map
                            var mapContainer = document.getElementById(mapPreviewId);
                            map = new google.maps.Map(mapContainer, {
                                zoom: 14,
                                center: { lat: latitude, lng: longitude }
                            });

                            // Add marker at the location
                            marker = new google.maps.Marker({
                                position: { lat: latitude, lng: longitude },
                                map: map
                            });

                            console.log("Peta berhasil ditampilkan!"); // Debug: cek apakah peta berhasil ditampilkan

                            // Set maplink to hidden input
                            var mapUrl = 'https://www.google.com/maps?q=' + latitude + ',' + longitude;
                            $('#' + maplinkId).val(mapUrl); // Set URL ke input hidden

                        } else {
                            console.log("Alamat tidak ditemukan!"); // Debug: cek jika tidak ada hasil
                            $('#' + mapPreviewId).html("<p>Alamat tidak ditemukan!</p>");
                        }
                    });
                } else {
                    $('#' + mapPreviewId).html("<p>Masukkan alamat yang valid.</p>");
                }
            }

            // Event listener untuk input alamat di modal Create
            $('#alamat').on('input', function() {
                var alamat = $(this).val();
                loadMapPreview(alamat, 'map-preview', 'maplink');
            });

            // Event listener untuk input alamat di modal Edit
            <?php $__currentLoopData = $kontaks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kontak): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                $('#alamat<?php echo e($kontak->id); ?>').on('input', function() {
                    var alamat = $(this).val();
                    loadMapPreview(alamat, 'map-preview<?php echo e($kontak->id); ?>', 'maplink<?php echo e($kontak->id); ?>');
                });

                // Tambahkan event listener untuk ketika modal dibuka pada modal Edit
                $('#editKontakModal<?php echo e($kontak->id); ?>').on('shown.bs.modal', function() {
                    var alamat = $('#alamat<?php echo e($kontak->id); ?>').val();
                    if (alamat) {
                        loadMapPreview(alamat, 'map-preview<?php echo e($kontak->id); ?>', 'maplink<?php echo e($kontak->id); ?>');
                    }
                });
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            // Tambahkan event listener untuk ketika modal Create dibuka
            $('#createKontakModal').on('shown.bs.modal', function() {
                var alamat = $('#alamat').val();
                if (alamat) {
                    loadMapPreview(alamat, 'map-preview', 'maplink');
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\Kontak\index.blade.php ENDPATH**/ ?>