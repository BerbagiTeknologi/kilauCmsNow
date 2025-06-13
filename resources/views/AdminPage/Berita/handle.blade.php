@extends('AdminPage.App.master')

@section('style')
    <style>
        #createBeritaModal .modal-dialog,
        #editBeritaModal .modal-dialog {
            max-width: 800px;
            width: 90%;
        }

        .modal-header {
            color: black;
            border-radius: 5px 5px 0 0;
        }

        .modal-title {
            font-size: 18px;
        }

        .modal-footer .btn {
            padding: 10px 15px;
            font-size: 14px;
        }

        .img-thumbnail {
            max-width: 120px;
            max-height: 120px;
            border-radius: 5px;
        }
    </style>
@endsection


@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Data Berita</h4>

                                <!-- Form Pencarian -->
                                <form id="search-form" class="ms-auto d-flex">
                                    <input type="text" id="search" name="search" class="form-control"
                                        placeholder="Cari berita...">
                                </form>

                                <!-- Tombol Tambah Berita -->
                                <button class="btn btn-primary btn-round ms-2" data-toggle="modal"
                                    data-target="#createBeritaModal">
                                    <i class="fa fa-plus"></i> Tambah Berita
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="berita-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul</th>
                                            <th>Konten</th>
                                            <th>Tanggal</th>
                                            <th>Foto 1</th>
                                            <th>Foto 2</th>
                                            <th>Foto 3</th>
                                            <th>Status Berita</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="berita-body">
                                        <!-- Data berita akan dimuat dengan AJAX -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="pagination-container">
                                <ul id="pagination" class="pagination justify-content-end"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH BERITA -->
    <div class="modal fade" id="createBeritaModal" tabindex="-1" role="dialog" aria-labelledby="createBeritaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"> <!-- Ditambahkan class modal-lg agar lebih besar -->
            <div class="modal-content">
                <form id="create-berita-form" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Berita</h5>
                        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Konten</label>
                            <textarea name="konten" id="konten-create" class="form-control ckeditor" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Foto 1</label>
                            <input type="file" name="foto" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Foto 2</label>
                            <input type="file" name="foto2" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Foto 3</label>
                            <input type="file" name="foto3" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT BERITA -->
    <div class="modal fade" id="editBeritaModal" tabindex="-1" role="dialog" aria-labelledby="editBeritaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"> <!-- Ditambahkan class modal-lg agar lebih besar -->
            <div class="modal-content">
                <form id="edit-berita-form" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Berita</h5>
                        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" id="edit-judul" name="judul" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Konten</label>
                            <textarea id="edit-konten" name="konten" class="form-control ckeditor" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" id="edit-tanggal" name="tanggal" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Foto 1</label>
                            <input type="file" id="edit-foto" name="foto" class="form-control">
                            <img id="preview-foto" src="" class="img-thumbnail mt-2" style="display: none;">
                        </div>
                        <div class="form-group">
                            <label>Foto 2</label>
                            <input type="file" id="edit-foto2" name="foto2" class="form-control">
                            <img id="preview-foto2" src="" class="img-thumbnail mt-2" style="display: none;">
                        </div>
                        <div class="form-group">
                            <label>Foto 3</label>
                            <input type="file" id="edit-foto3" name="foto3" class="form-control">
                            <img id="preview-foto3" src="" class="img-thumbnail mt-2" style="display: none;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets_admin/ckeditor/ckeditor.js') }}"></script>
    {{-- <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> --}}
    <script>
        $(document).ready(function() {
            function initCKEditor(selector) {
                $(selector).each(function() {
                    const editorId = $(this).attr('id');

                    // Jika CKEditor sudah diinisialisasi pada elemen, hapus terlebih dahulu
                    if (CKEDITOR.instances[editorId]) {
                        CKEDITOR.instances[editorId].destroy(true);
                    }

                    // Inisialisasi CKEditor pada elemen
                    CKEDITOR.replace(editorId);
                });
            }

            // Inisialisasi CKEditor untuk textarea dengan class 'ckeditor'
            initCKEditor('.ckeditor');

            // Re-inisialisasi saat modal dibuka (untuk modal edit)
            $('.modal').on('shown.bs.modal', function() {
                initCKEditor('.ckeditor');
            });

            let currentPage = 1;
            let perPage = 10;

            // Fungsi untuk memuat berita dengan AJAX
            function loadBerita(page = 1, search = '') {
                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita?page=${page}&per_page=${perPage}&search=${search}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        let beritaHtml = '';
                        let no = (page - 1) * perPage + 1;
                        let defaultImage = "{{ asset('assets_admin/img/noimage.jpg') }}";

                        $.each(response.data, function(index, berita) {
                            let foto1 = berita.foto ?
                                `https://berbagipendidikan.org${berita.foto}` : defaultImage;
                            let foto2 = berita.foto2 ?
                                `https://berbagipendidikan.org${berita.foto2}` : defaultImage;
                            let foto3 = berita.foto3 ?
                                `https://berbagipendidikan.org${berita.foto3}` : defaultImage;

                            let statusClass = berita.status_berita === "Aktif" ? "btn-success" : "btn-danger";
                            let statusText = berita.status_berita;

                            beritaHtml += `
                                <tr id="row-${berita.id}">
                                    <td>${no++}</td>
                                    <td>${berita.judul}</td>
                                    <td>${berita.konten.substring(0, 100)}...</td>
                                    <td>${berita.tanggal}</td>
                                    <td><img src="${foto1}" class="img-thumbnail" style="width: 90px; height: auto;"></td>
                                    <td><img src="${foto2}" class="img-thumbnail" style="width: 90px; height: auto;"></td>
                                    <td><img src="${foto3}" class="img-thumbnail" style="width: 90px; height: auto;"></td>
                                      <td>
                                            <button class="btn ${statusClass} btn-sm text-white toggle-status"
                                                data-id="${berita.id}" title="Ubah Status">
                                                ${statusText}
                                            </button>
                                      </td>
                                    <td>
                                        <div class="btn-group gap-2">
                                            <!-- Tombol Edit -->
                                            <button class="btn btn-warning btn-sm rounded-circle p-2 edit-berita" 
                                                data-id="${berita.id}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <button class="btn btn-info btn-sm rounded-circle p-2 toggle-status" data-id="${berita.id}" title="Ubah Status">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>

                                            <!-- Tombol Delete -->
                                            <button class="btn btn-danger btn-sm rounded-circle p-2 delete-berita" 
                                                data-id="${berita.id}" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });

                        $('#berita-body').html(beritaHtml);
                        generatePagination(response.pagination);
                    },
                    error: function() {
                        alert('Gagal mengambil data berita.');
                    }
                });
            }

            // Fungsi untuk pagination
            function generatePagination(pagination) {
                let paginationHtml = '';
                let maxVisiblePages = 5;

                if (pagination.current_page > 1) {
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="1">First</a></li>`;
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page - 1}">«</a></li>`;
                }

                let startPage = Math.max(1, pagination.current_page - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(pagination.last_page, startPage + maxVisiblePages - 1);

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
                }

                if (pagination.current_page < pagination.last_page) {
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page + 1}">»</a></li>`;
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.last_page}">Last</a></li>`;
                }

                $('#pagination').html(paginationHtml);
            }

            // Event listener untuk pencarian
            $('#search').on('keyup', function() {
                let searchValue = $(this).val();
                loadBerita(1, searchValue);
            });

            // Event listener untuk pagination
            $(document).on('click', '#pagination a', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                let searchValue = $('#search').val();
                loadBerita(page, searchValue);
            });

             // Event listener untuk tombol Ubah Status
        $(document).on('click', '.toggle-status', function() {
            let beritaId = $(this).data('id');

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Status berita akan diubah!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Ubah Status!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `https://berbagipendidikan.org/api/berita-toggle-status/${beritaId}`,
                        type: 'POST',
                        success: function(response) {
                            Swal.fire("Sukses!", "Status berita berhasil diperbarui.", "success");
                            loadBerita(); // Refresh daftar berita
                        },
                        error: function() {
                            Swal.fire("Gagal!", "Terjadi kesalahan saat memperbarui status berita.", "error");
                        }
                    });
                }
            });
        });

            // Event listener untuk tombol hapus
            $(document).on('click', '.delete-berita', function() {
                let beritaId = $(this).data('id');

                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `https://berbagipendidikan.org/api/berita-delete/${beritaId}`,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire("Terhapus!", "Data berita berhasil dihapus.",
                                    "success");
                                $(`#row-${beritaId}`)
                                    .remove(); // Hapus baris berita dari tabel
                            },
                            error: function() {
                                Swal.fire("Gagal!",
                                    "Terjadi kesalahan saat menghapus data.",
                                    "error");
                            }
                        });
                    }
                });
            });

            // Menampilkan modal edit dengan data berita
            $(document).on('click', '.edit-berita', function() {
                let beritaId = $(this).data('id');

                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita/${beritaId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response); // Debugging: Pastikan response muncul di console

                        if (response.success) {
                            let berita = response.data;

                            // **Reset form sebelum mengisi data baru**
                            $('#edit-berita-form')[0].reset();
                            $('#preview-foto, #preview-foto2, #preview-foto3')
                                .hide(); // Sembunyikan preview gambar

                            // Isi form dengan data berita
                            $('#edit-id').val(berita.id);
                            $('#edit-judul').val(berita.judul);
                            $('#edit-konten').val(berita.konten);
                            $('#edit-tanggal').val(berita.tanggal);

                            if (CKEDITOR.instances['edit-konten']) {
                                CKEDITOR.instances['edit-konten'].setData(berita.konten);
                            } else {
                                $('#edit-konten').val(berita.konten);
                            }

                            // Menampilkan gambar jika ada
                            let baseUrl = "https://berbagipendidikan.org";

                            if (berita.foto) {
                                $('#preview-foto').attr('src', baseUrl + berita.foto).show();
                            }
                            if (berita.foto2) {
                                $('#preview-foto2').attr('src', baseUrl + berita.foto2).show();
                            }
                            if (berita.foto3) {
                                $('#preview-foto3').attr('src', baseUrl + berita.foto3).show();
                            }

                            // Tampilkan modal edit
                            $('#editBeritaModal').modal('show');
                        } else {
                            Swal.fire("Gagal!", "Data berita tidak ditemukan.", "error");
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire("Gagal!", "Tidak dapat mengambil data berita.", "error");
                    }
                });
            });

            // **Tambahkan event agar tombol Close (X) dan tombol Batal bisa menutup modal**
            $('#editBeritaModal').on('hidden.bs.modal', function() {
                $('#edit-berita-form')[0].reset(); // Reset form saat modal ditutup
                $('#preview-foto, #preview-foto2, #preview-foto3').hide(); // Sembunyikan preview gambar
            });

            // Event listener untuk tombol Close (X) dan tombol Batal
            $('.close, .close-modal').on('click', function() {
                $('#editBeritaModal').modal('hide');
            });

            // Handle form update berita
            $('#edit-berita-form').on('submit', function(e) {
                e.preventDefault();
                let beritaId = $('#edit-id').val();
                let formData = new FormData(this);

                // Update konten dari CKEditor sebelum submit
                if (CKEDITOR.instances['edit-konten']) {
                    formData.set('konten', CKEDITOR.instances['edit-konten'].getData());
                }

                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita-update/${beritaId}`,
                    type: 'POST', // Sesuaikan dengan metode API
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire("Sukses!", "Berita berhasil diperbarui.", "success");
                        $('#editBeritaModal').modal('hide');
                        loadBerita(); // Refresh daftar berita
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire("Gagal!", "Terjadi kesalahan saat memperbarui berita.",
                            "error");
                    }
                });
            });

            // Handle Form Submit (AJAX)
            $('#create-berita-form').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: 'https://berbagipendidikan.org/api/berita-create',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire("Sukses!", "Berita berhasil ditambahkan.", "success");
                        $('#createBeritaModal').modal('hide');
                        loadBerita();
                    },
                    error: function(xhr) {
                        Swal.fire("Gagal!", "Terjadi kesalahan saat menambah berita.", "error");
                    }
                });
            });

            // Load berita pertama kali
            loadBerita();
        });
    </script>
@endsection
