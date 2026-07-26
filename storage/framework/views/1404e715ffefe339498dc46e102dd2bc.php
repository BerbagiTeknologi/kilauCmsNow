<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Pengajuan Kerjasama</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="colaborasi-table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul Program</th>
                                            <th>Kategori Mitra</th>
                                            <th>Nama Mitra</th>
                                            <th>Email</th>
                                            <th>Balasan</th>
                                            <th>Status Progress</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $colaborasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($loop->iteration); ?></td>
                                                <td><?php echo e($data->program->judul); ?></td>
                                                
                                                <td><?php echo e($data->kategori_mitra); ?></td>
                                                <td><?php echo e($data->nama_lengkap); ?></td>
                                                <td><?php echo e($data->alamat_email); ?></td>
                                                <td>
                                                    <span
                                                        class="badge <?php echo e($data->status_progress_kerjasama == 'Pending' ? 'badge-warning' : 'badge-success'); ?>">
                                                        <?php echo e($data->status_progress_kerjasama); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge <?php echo e($data->status_kerjasama === 'Aktif' ? 'badge-success' : 'badge-danger'); ?>">
                                                        <?php echo e($data->status_kerjasama); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <button class="btn btn-secondary btn-sm rounded-circle p-2"
                                                            data-toggle="modal" data-target="#showModal<?php echo e($data->id); ?>"
                                                            title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </button>

                                                        <!-- Tombol Kirim Balasan -->
                                                        <button class="btn btn-primary btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#balasanModal<?php echo e($data->id); ?>"
                                                            title="Kirim Balasan">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>

                                                        <!-- Tombol Ubah Status -->
                                                        <button class="btn btn-info btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#statusModal<?php echo e($data->id); ?>"
                                                            title="Ubah Status">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>

                                                        <!-- Tombol Hapus -->
                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#hapusModal<?php echo e($data->id); ?>" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                            </tr>

                                            <!-- Modal Kirim Balasan -->
                                            <div class="modal fade" id="balasanModal<?php echo e($data->id); ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('colaborasi.update', $data->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Kirim Balasan ke
                                                                    <?php echo e($data->nama_lengkap); ?></h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Balasan</label>
                                                                    <textarea name="balasan" class="form-control" rows="4" required><?php echo e(old('balasan')); ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Batal</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Kirim</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Ubah Status -->
                                            <div class="modal fade" id="statusModal<?php echo e($data->id); ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('colaborasi.toggleStatus', $data->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Ubah Status Kerjasama</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <label>Status</label>
                                                                <select name="status_kerjasama" class="form-control">
                                                                    <option value="1"
                                                                        <?php echo e($data->status_kerjasama == 1 ? 'selected' : ''); ?>>
                                                                        Aktif</option>
                                                                    <option value="2"
                                                                        <?php echo e($data->status_kerjasama == 2 ? 'selected' : ''); ?>>
                                                                        Tidak Aktif</option>
                                                                </select>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Batal</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Hapus -->
                                            <div class="modal fade" id="hapusModal<?php echo e($data->id); ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('colaborasi.delete', $data->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menghapus data kolaborasi
                                                                    <strong><?php echo e($data->nama_lengkap); ?></strong>?
                                                                </p>
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

    <?php $__currentLoopData = $colaborasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="showModal<?php echo e($data->id); ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Data Pengajuan Kerjasama</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td><?php echo e($data->id); ?></td>
                            </tr>
                            <tr>
                                <th>Judul Program</th>
                                <td><?php echo e($data->program->judul); ?></td>
                            </tr>
                            <tr>
                                <th>Jenis Kerjasama</th>
                                <td><?php echo e($data->jenis_kerjasama); ?></td>
                            </tr>
                            <tr>
                                <th>Kategori Mitra</th>
                                <td><?php echo e($data->kategori_mitra); ?></td>
                            </tr>
                            <?php if($data->kategori_mitra === 'Perusahaan' || $data->kategori_mitra === 'Instansi/Lembaga/Komunitas'): ?>
                                <tr>
                                    <th>Nama Perusahaan/Instansi</th>
                                    <td><?php echo e($data->nama_perusahaan ?? 'Tidak Diisi'); ?></td>
                                </tr>
                                <tr>
                                    <th>Jabatan</th>
                                    <td><?php echo e($data->jabatan ?? 'Tidak Diisi'); ?></td>
                                </tr>
                                <tr>
                                    <th>No. HP Perusahaan/Instansi</th>
                                    <td><?php echo e($data->nomor_hp_organisasi ?? 'Tidak Diisi'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th>Nama Mitra</th>
                                <td><?php echo e($data->nama_lengkap); ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo e($data->alamat_email); ?></td>
                            </tr>
                            <tr>
                                <th>No. HP</th>
                                <td><?php echo e($data->nomor_hp); ?></td>
                            </tr>
                            <tr>
                                <th>Deskripsi Pengajuan</th>
                                <td><?php echo e($data->deskripsi_pengajuan_kerjasama ?? 'Tidak Ada Deskripsi'); ?></td>
                            </tr>
                            <tr>
                                <th>Balasan</th>
                                <td><?php echo e($data->balasan ? $data->balasan : 'Belum ada balasan'); ?></td>
                            </tr>
                            <tr>
                                <th>Status Progress</th>
                                <td>
                                    <span
                                        class="badge <?php echo e($data->status_progress_kerjasama === 'Pending' ? 'badge-warning' : 'badge-success'); ?>">
                                        <?php echo e($data->status_progress_kerjasama); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Status Kerjasama</th>
                                <td>
                                    <span
                                        class="badge <?php echo e($data->status_kerjasama === 'Aktif' ? 'badge-success' : 'badge-danger'); ?>">
                                        <?php echo e($data->status_kerjasama); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td><?php echo e($data->created_at); ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Diperbarui</th>
                                <td><?php echo e($data->updated_at); ?></td>
                            </tr>
                            <?php if($data->npwp_file): ?>
                                <tr>
                                    <th>Dokumen NPWP</th>
                                    <td>
                                        <a href="<?php echo e(Storage::url($data->npwp_file)); ?>" target="_blank">Lihat Data NPWP</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if($data->foto_orang_npwp): ?>
                                <tr>
                                    <th>Selfie dengan NPWP</th>
                                    <td>
                                        <a href="<?php echo e(Storage::url($data->foto_orang_npwp)); ?>" target="_blank">Lihat Data Orang NPWP</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        $(document).ready(function() {
            $('#colaborasi-table').DataTable();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\Colaborasi\index.blade.php ENDPATH**/ ?>