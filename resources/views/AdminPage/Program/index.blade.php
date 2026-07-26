@extends('AdminPage.App.master')

@section('style')
    <style>
        #editor-container {
            min-height: 150px;
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css" rel="stylesheet" /> --}}
@endsection

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Data Timeline</h4>
                                <button class="btn btn-primary btn-round ms-auto" data-toggle="modal"
                                    data-target="#createTimelineModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="progaram-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul Program</th>
                                            <th>Program Yang Berhasil Dijalankan</th>
                                            <th>Jumlah Target</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                            <th>Foto Program</th>
                                            <th>Foto Thumbnail</th>
                                            <th>Foto Mitra</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($program as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->judul }}</td>
                                                <td><strong><em>{{ $item->program_yang_berhasil_dijalankan }}</em></strong>
                                                    orang telah terdampak</td>
                                                <td><strong><em>{{ $item->jumlah_target_tercapai }}</em></strong> orang yang
                                                    ingin dicapai</td>
                                                <!--<td>{{ $item->deskripsi }}</td>-->
                                              <td>
                                                    {{ Str::limit(str_replace('&nbsp;', ' ', strip_tags($item->deskripsi)), 300, '...') }}
                                                </td>

                                                <td>
                                                    <span
                                                        class="badge {{ $item->status_program == 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $item->status_program }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($item->foto_image)
                                                        <div class="form-group">
                                                            <div>
                                                                @foreach ($item->foto_image as $image)
                                                                    <img src="{{ asset('storage/' . $image) }}"
                                                                        alt="Foto Program" width="100" height="auto"
                                                                        style="margin-right: 10px;">
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item->thumbnail_image)
                                                        <img src="{{ Storage::url($item->thumbnail_image) }}"
                                                            alt="Program Image" style="width: 100px; height: auto;">
                                                    @else
                                                        No Image
                                                    @endif
                                                </td>
                                                <td style="white-space: nowrap;">
                                                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px;">
                                                        @foreach ($item->mitras as $mitra)
                                                            <!-- Menampilkan foto mitra yang terkait -->
                                                            <div style="width: 100px; height: auto;">
                                                                @if ($mitra->file)
                                                                    <!-- Pastikan mitra memiliki file gambar -->
                                                                    <img src="{{ Storage::url($mitra->file) }}" alt="{{ $mitra->nama_mitra }}" width="100" height="auto">
                                                                @else
                                                                    No Image
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>                                                

                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <!-- Tombol Edit -->
                                                        <button
                                                            class="btn btn-warning btn-sm rounded-circle p-2 btnEditProgram"
                                                            data-toggle="modal" data-target="#editProgramModal"
                                                            data-id="{{ $item->id }}" data-judul="{{ $item->judul }}"
                                                            data-deskripsi="{{ $item->deskripsi }}"
                                                            data-presentase="{{ $item->program_yang_berhasil_dijalankan }}"
                                                            data-jumlah_target_tercapai="{{ $item->jumlah_target_tercapai }}"
                                                            data-thumbnail="{{ Storage::url($item->thumbnail_image) }}"
                                                            data-file_galeri="{{ json_encode($item->foto_image) }}"
                                                            data-mitra_ids="{{ json_encode($item->mitras->pluck('id')->toArray()) }}"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <!-- Tombol Delete -->
                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteProgramModal{{ $item->id }}"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>

                                                        <!-- Tombol Ubah Status -->
                                                        <button class="btn btn-info btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#statusModal{{ $item->id }}"
                                                            title="Ubah Status">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Delete Program -->
                                            <div class="modal fade" id="deleteProgramModal{{ $item->id }}"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="deleteProgramModalLabel{{ $item->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('program.delete', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteProgramModalLabel{{ $item->id }}">Hapus
                                                                    Galeri</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Apakah Anda yakin ingin menghapus galeri ini?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Toggle Status -->
                                            <div class="modal fade" id="statusModal{{ $item->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="statusModalLabel{{ $item->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('program.toggleStatus', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="statusModalLabel{{ $item->id }}">
                                                                    Ubah Status Program
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Pilih status untuk program:
                                                                    <strong>{{ $item->judul }}</strong>.
                                                                </p>
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="status_program" class="form-control">
                                                                        <option value="1"
                                                                            {{ $item->status_program == 1 ? 'selected' : '' }}>
                                                                            Aktif
                                                                        </option>
                                                                        <option value="2"
                                                                            {{ $item->status_program == 2 ? 'selected' : '' }}>
                                                                            Tidak Aktif
                                                                        </option>
                                                                    </select>
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
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Program -->
    <div class="modal fade" id="editProgramModal" tabindex="-1" role="dialog" aria-labelledby="editProgramModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editProgramForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProgramModalLabel">Edit Program</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- ID Program -->
                        <input type="hidden" id="editProgramId" name="id">

                        <!-- Judul Program -->
                        <div class="form-group">
                            <label for="judul">Judul Program</label>
                            <input type="text" class="form-control" id="editJudul" name="judul" required>
                        </div>

                        <!-- Deskripsi -->
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control ckeditor" id="editDeskripsi" name="deskripsi" required></textarea>
                        </div>

                        <!-- Presentase Program -->
                        {{-- <div class="form-group">
                            <label for="program_yang_berhasil_dijalankan">Program Yang Berhasil Dijalankan</label>
                            <input type="number" name="program_yang_berhasil_dijalankan" class="form-control"
                                min="0" step="1" id="editPresentase" required
                                placeholder="Masukkan persentase antara 0-100">
                        </div> --}}

                        <!-- Program Yang Berhasil Dijalankan -->
                        <div class="form-group">
                            <label for="program_yang_berhasil_dijalankan">Program Yang Berhasil Dijalankan</label>
                            <input type="number" name="program_yang_berhasil_dijalankan" class="form-control"
                                id="editPresentase" min="0" step="1" required
                                placeholder="Masukkan jumlah orang yang terdampak">
                        </div>

                        <!-- Jumlah Target Yang Ingin Dicapai -->
                        <div class="form-group">
                            <label for="jumlah_target_tercapai">Jumlah Target Yang Ingin Dicapai</label>
                            <input type="number" name="jumlah_target_tercapai" class="form-control" id="editTarget"
                                min="0" step="1" required
                                placeholder="Masukkan jumlah target yang ingin dicapai">
                        </div>

                        <div class="form-group">
                            <label>Mitras Program</label>
                            <select name="mitra_ids[]" class="form-control selectpicker" multiple required
                                data-live-search="true">
                                @foreach ($mitras as $mitra)
                                    <option value="{{ $mitra->id }}">
                                        {{ $mitra->nama_mitra }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <!-- Preview Gambar Mitra -->
                        <div id="editMitraImagePreview" class="mt-2"></div>

                        <!-- Thumbnail -->
                        <div class="form-group">
                            <label>Thumbnail Program</label>
                            <input type="file" name="thumbnail_image" id="thumbnail_image" class="form-control">
                            <div id="editImagePreviewNewThumbnail" class="mt-2">
                                <!-- Preview thumbnail baru -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Foto Program</label>
                            <input type="file" name="foto_image[]" class="form-control" id="foto_image" multiple>

                            <!-- Preview Foto Program Lama -->
                            <div id="editImagePreviewOld" class="mt-2">
                                <!-- Gambar lama akan dimasukkan di sini oleh JavaScript -->
                            </div>

                            <!-- Preview Foto Program Baru -->
                            <div id="editImagePreviewNew" class="mt-2">
                                <!-- Gambar baru yang dipilih akan muncul di sini -->
                            </div>
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

    <!-- Modal Create -->
    <div class="modal fade" id="createTimelineModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createProgramForm" action="{{ route('program.create') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Program</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Judul Program -->
                        <div class="form-group">
                            <label>Judul Program</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>

                        <!-- Deskripsi -->
                        <!--<div class="form-group">-->
                        <!--    <label>Deskripsi</label>-->
                        <!--    <textarea name="deskripsi" class="form-control" required></textarea>-->
                        <!--</div>-->
                        
                        <!-- Deskripsi -->
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control ckeditor" required></textarea>
                        </div>


                        <!-- Program Yang Berhasil Dijalankan -->
                        <div class="form-group">
                            <label>Program Yang Berhasil Dijalankan</label>
                            <input type="number" name="program_yang_berhasil_dijalankan" class="form-control"
                                min="0" step="1" required
                                placeholder="Masukkan jumlah orang yang terdampak">
                        </div>

                        <!-- Jumlah Target Yang Ingin Dicapai -->
                        <div class="form-group">
                            <label>Jumlah Target Yang Ingin Dicapai</label>
                            <input type="number" name="jumlah_target_tercapai" class="form-control" min="0"
                                step="1" required placeholder="Masukkan jumlah target yang ingin dicapai">
                        </div>

                        <!-- Pilih Mitra -->
                        <div class="form-group">
                            <label>Mitras Program</label>
                            <select name="mitra_ids[]" class="form-control selectpicker" multiple required
                                data-live-search="true">
                                @foreach ($mitras as $mitra)
                                    <option value="{{ $mitra->id }}" data-image="{{ Storage::url($mitra->file) }}">
                                        {{ $mitra->nama_mitra }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Preview Gambar Mitra -->
                        <div id="mitraImagePreview" class="mt-2"></div>

                        <!-- Thumbnail Program -->
                        <div class="form-group">
                            <label>Thumbnail Program</label>
                            <input type="file" name="thumbnail_image" class="form-control">
                        </div>

                        <!-- Foto Program -->
                        <div class="form-group">
                            <label>Foto Program</label>
                            <input type="file" name="foto_image[]" class="form-control" id="foto_image" multiple>
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
@endsection

@section('scripts')
    <script src="{{ asset('assets_admin/ckeditor/ckeditor.js') }}"></script>
    <script>
        $(document).ready(function() {
        $('#progaram-table').DataTable();

        // Fungsi untuk menginisialisasi CKEditor
        function initCKEditor(selector) {
            $(selector).each(function() {
                const editorId = $(this).attr('id');

                // Hapus instance CKEditor sebelumnya jika ada
                if (CKEDITOR.instances[editorId]) {
                    CKEDITOR.instances[editorId].destroy(true);
                }

                // Inisialisasi CKEditor
                CKEDITOR.replace(editorId, {
                    toolbar: [
                        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat'] },
                        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                        { name: 'links', items: ['Link', 'Unlink'] },
                        { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                        { name: 'colors', items: ['TextColor', 'BGColor'] },
                        { name: 'tools', items: ['Maximize', 'ShowBlocks'] },
                        { name: 'document', items: ['Source'] }
                    ],
                    height: 250,
                    removePlugins: 'notification'
                });
            });
        }

        // Inisialisasi CKEditor
        initCKEditor('.ckeditor');

        // Jika modal dibuka (untuk edit), re-inisialisasi CKEditor
        $('.modal').on('shown.bs.modal', function() {
            initCKEditor('.ckeditor');
        });
    });
    
        $(document).ready(function() {
            // Inisialisasi selectpicker ketika modal dibuka
            $('#createTimelineModal').on('shown.bs.modal', function() {
                $('.selectpicker').selectpicker('refresh'); // Refresh dropdown selectpicker
            });

            // Event listener untuk perubahan pilihan mitra (multiple select)
            $('select[name="mitra_ids[]"]').on('change', function() {
                var mitraIds = $(this).val(); // Ambil ID mitra yang dipilih (bisa lebih dari satu)

                // Kosongkan preview gambar mitra sebelumnya
                $('#mitraImagePreview').html('');

                // Cek apakah ada mitra yang dipilih
                if (mitraIds && mitraIds.length > 0) {
                    // Loop untuk setiap ID mitra yang dipilih
                    mitraIds.forEach(function(mitraId) {
                        // Kirim request Ajax untuk mendapatkan gambar mitra
                        $.ajax({
                            url: '/program/mitra/' + mitraId +
                                '/image', // Endpoint untuk mengambil gambar mitra
                            type: 'GET',
                            success: function(response) {
                                if (response.status === 'success') {
                                    // Jika gambar ditemukan, tampilkan di preview
                                    $('#mitraImagePreview').append(
                                        '<img src="' + response.image_url +
                                        '" alt="Mitra Image" class="img-thumbnail" width="100" style="margin-right: 10px;">'
                                    );
                                } else {
                                    // Jika tidak ada gambar, tampilkan pesan
                                    $('#mitraImagePreview').append(
                                        '<p>No Image Found</p>');
                                }
                            },
                            error: function() {
                                // Jika ada error dalam request, tampilkan pesan error
                                $('#mitraImagePreview').append(
                                    '<p>Error loading image</p>');
                            }
                        });
                    });
                }
            });
        });

        $(document).ready(function() {
            // Handle changes to the Mitra selection (multi-select)
            $('select[name="mitra_ids[]"]').on('change', function() {
                var mitraIds = $(this).val(); // Get selected mitra IDs

                // Empty the previous images
                $('#editMitraImagePreview').html(''); // Clear the previous preview images

                // If mitra IDs are selected
                if (mitraIds && mitraIds.length > 0) {
                    mitraIds.forEach(function(mitraId) {
                        $.ajax({
                            url: '/program/mitra/' + mitraId +
                            '/image', // Your backend endpoint for mitra image
                            type: 'GET',
                            success: function(response) {
                                if (response.status === 'success' && response
                                    .image_url) {
                                    // Check if image is already added
                                    if ($('#editMitraImagePreview img[src="' + response
                                            .image_url + '"]').length === 0) {
                                        // Append the mitra image if not already displayed
                                        $('#editMitraImagePreview').append(
                                            '<img src="' + response.image_url +
                                            '" class="img-thumbnail" width="100" style="margin-right: 10px;">'
                                        );
                                    }
                                } else {
                                    // Display a message if no image is found
                                    $('#editMitraImagePreview').append(
                                        '<p>No Image Found</p>');
                                }
                            },
                            error: function() {
                                $('#editMitraImagePreview').append(
                                    '<p>Error loading image</p>');
                            }
                        });
                    });
                }
            });

            // Initialize modal and refresh selectpicker when opened
            $('#editProgramModal').on('shown.bs.modal', function() {
                // Ensure that the selectpicker is refreshed
                $('.selectpicker').selectpicker('refresh');

                // Clear any existing previews of old and new images to avoid duplication
                $('#editImagePreviewOld').html(''); // Clear previous old image previews
                $('#editImagePreviewNew').html(''); // Clear previous new image previews
                $('#editMitraImagePreview').html(''); // Clear mitra images
            });

            // For the edit button to populate modal with data
            $('.btnEditProgram').click(function() {
                var id = $(this).data('id');
                var judul = $(this).data('judul');
                var deskripsi = $(this).data('deskripsi');
                var presentase = $(this).data('presentase');
                var target = $(this).data('jumlah_target_tercapai');
                var thumbnail = $(this).data('thumbnail');
                var fileGaleri = $(this).data('file_galeri');
                var mitraIds = $(this).data('mitra_ids');

                // Set the modal fields with the existing data
                $('#editProgramId').val(id);
                $('#editJudul').val(judul);
                $('#editDeskripsi').val(deskripsi);
                $('#editPresentase').val(presentase);
                $('#editTarget').val(target);

                // Set the action URL for the form
                var editUrl = "{{ route('program.edit', ':id') }}".replace(':id', id);
                $('#editProgramForm').attr('action', editUrl);

                // Set the selected mitra IDs
                if (mitraIds && mitraIds.length > 0) {
                    $('select[name="mitra_ids[]"]').val(mitraIds).trigger(
                    'change'); // Sync mitra selections
                }

                // Reset and display the old gallery images if any
                $('#editImagePreviewOld').html(''); // Clear any old images preview before adding new ones
                if (fileGaleri && fileGaleri !== "null") {
                    var images = JSON.parse(fileGaleri);
                    images.forEach(function(image) {
                        // Check if image is already displayed in preview
                        if ($('#editImagePreviewOld img[src="/storage/' + image + '"]').length ===
                            0) {
                            $('#editImagePreviewOld').append(
                                '<img src="/storage/' + image +
                                '" class="img-thumbnail" width="100" style="margin-right: 10px;">'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endsection
