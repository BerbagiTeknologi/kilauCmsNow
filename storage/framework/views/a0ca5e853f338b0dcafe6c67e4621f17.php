<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">FAQ</h4>
                                <button class="btn btn-primary btn-round ms-auto" data-toggle="modal"
                                    data-target="#createModal">
                                    <i class="fa fa-plus"></i> Tambah FAQ
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="faq-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Pertanyaan</th>
                                            <th>Jawaban</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($loop->iteration); ?></td>
                                                <td><?php echo e($faq->question); ?></td>
                                                <td><?php echo e($faq->answer); ?></td>
                                                <td>
                                                    <span
                                                        class="badge <?php echo e($faq->status_faqs === 'Aktif' ? 'badge-success' : 'badge-danger'); ?>">
                                                        <?php echo e($faq->status_faqs); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group" aria-label="Basic example">
                                                        <!-- Tombol Edit -->
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal" data-target="#editModal<?php echo e($faq->id); ?>"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <!-- Tombol Delete -->
                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteModal<?php echo e($faq->id); ?>" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>

                                                        <!-- Tombol Toggle Status -->
                                                        <button class="btn btn-info btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#statusModal<?php echo e($faq->id); ?>"
                                                            title="Toggle Status">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Toggle Status -->
                                            <div class="modal fade" id="statusModal<?php echo e($faq->id); ?>" tabindex="-1"
                                                role="dialog" aria-labelledby="statusModalLabel<?php echo e($faq->id); ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('faq.toggleStatus', $faq->id)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="statusModalLabel<?php echo e($faq->id); ?>">
                                                                    Ubah Status FAQ
                                                                </h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Pilih status untuk FAQ:
                                                                    <strong><?php echo e($faq->question); ?></strong>.
                                                                </p>
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="status_faqs" class="form-control">
                                                                        <option value="Aktif"
                                                                            <?php echo e($faq->status_faqs === 'Aktif' ? 'selected' : ''); ?>>
                                                                            Aktif</option>
                                                                        <option value="Tidak Aktif"
                                                                            <?php echo e($faq->status_faqs === 'Tidak Aktif' ? 'selected' : ''); ?>>
                                                                            Tidak Aktif</option>
                                                                    </select>
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

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal<?php echo e($faq->id); ?>" tabindex="-1"
                                                role="dialog" aria-labelledby="editModalLabel<?php echo e($faq->id); ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('faq.edit', $faq->id)); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editModalLabel<?php echo e($faq->id); ?>">Edit FAQ</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Pertanyaan</label>
                                                                    <input type="text" name="question"
                                                                        class="form-control" value="<?php echo e($faq->question); ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Jawaban</label>
                                                                    <textarea name="answer" class="form-control"
                                                                        required><?php echo e($faq->answer); ?></textarea>
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

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal<?php echo e($faq->id); ?>" tabindex="-1"
                                                role="dialog" aria-labelledby="deleteModalLabel<?php echo e($faq->id); ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo e(route('faq.delete', $faq->id)); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteModalLabel<?php echo e($faq->id); ?>">Hapus FAQ</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menghapus FAQ ini
                                                                    <strong><?php echo e($faq->question); ?></strong>?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
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

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?php echo e(route('faq.create')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah FAQ</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <input type="text" name="question" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Jawaban</label>
                            <textarea name="answer" class="form-control" required></textarea>
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
            $('#faq-table').DataTable();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('AdminPage.App.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\xxxxx\Documents\sso-kilau\kilauCms\resources\views\AdminPage\Faq\index.blade.php ENDPATH**/ ?>