@extends('AdminPage.App.master')

@section('style')
    <style>
        #createKategoriModal .modal-dialog,
        #editKategoriModal .modal-dialog {
            max-width: 600px;
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
                                <h4 class="card-title">Data Kategori Berita</h4>

                                <!-- Form Pencarian -->
                                <form id="search-form" class="ms-auto d-flex">
                                    <input type="text" id="search" name="search" class="form-control"
                                        placeholder="Cari kategori...">
                                </form>

                                <!-- Tombol Tambah Kategori -->
                                <button class="btn btn-primary btn-round ms-2" data-toggle="modal"
                                    data-target="#createKategoriModal">
                                    <i class="fa fa-plus"></i> Tambah Kategori
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="kategori-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Kategori</th>
                                            <th>Status Kategori Berita</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kategori-body">
                                        <!-- Data kategori akan dimuat dengan AJAX -->
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

    <!-- MODAL TAMBAH KATEGORI -->
    <div class="modal fade" id="createKategoriModal" tabindex="-1" role="dialog"
        aria-labelledby="createKategoriModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="create-kategori-form">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kategori</h5>
                        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Kategori</label>
                            <input type="text" name="name_kategori" class="form-control" required>
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

    <!-- MODAL SHOW KATEGORI -->
    <div class="modal fade" id="showKategoriModal" tabindex="-1" role="dialog" aria-labelledby="showKategoriModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Kategori</h5>
                    <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Nama Kategori:</strong></label>
                        <p id="show-name_kategori"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Status Kategori:</strong></label>
                        <p id="show-status_kategori"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Dibuat Pada:</strong></label>
                        <p id="show-created_at"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Terakhir Diperbarui:</strong></label>
                        <p id="show-updated_at"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT KATEGORI -->
    <div class="modal fade" id="editKategoriModal" tabindex="-1" role="dialog" aria-labelledby="editKategoriModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="edit-kategori-form">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kategori</h5>
                        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="form-group">
                            <label>Nama Kategori</label>
                            <input type="text" id="edit-name_kategori" name="name_kategori" class="form-control"
                                required>
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
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let perPage = 5;

            function loadKategori(page = 1, search = '') {
                $.ajax({
                    url: `https://berbagipendidikan.org/api/kategori-berita?page=${page}&per_page=${perPage}&search=${search}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        let kategoriHtml = '';
                        let no = (page - 1) * perPage + 1;

                        if (response.success && response.data && response.data.data) {
                            $.each(response.data.data, function(index, kategori) {
                                let statusClass = kategori.status_kategori_berita === "Aktif" ?
                                    "badge-success" : "badge-danger";
                                let statusText = kategori.status_kategori_berita;

                                kategoriHtml += `
                        <tr id="row-${kategori.id}">
                            <td>${no++}</td>
                            <td>${kategori.name_kategori}</td>
                            <td>
                                <span class="badge ${statusClass}" 
                                      style="display: inline-block; width: 100px; height: 30px; 
                                      line-height: 30px; text-align: center; font-size: 0.875rem;">
                                    ${statusText}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group gap-2">
                                    <!-- Tombol Show -->
                                    <button class="btn btn-primary btn-sm rounded-circle p-2 show-kategori"
                                        data-id="${kategori.id}" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- Tombol Edit -->
                                    <button class="btn btn-warning btn-sm rounded-circle p-2 edit-kategori"
                                        data-id="${kategori.id}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Tombol Toggle Status -->
                                    <button class="btn btn-info btn-sm rounded-circle p-2 toggle-status"
                                        data-id="${kategori.id}" title="Ubah Status">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>

                                    <!-- Tombol Delete -->
                                    <button class="btn btn-danger btn-sm rounded-circle p-2 delete-kategori"
                                        data-id="${kategori.id}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        `;
                            });

                            $('#kategori-body').html(kategoriHtml);
                        } else {
                            $('#kategori-body').html(
                                '<tr><td colspan="4" class="text-center">Tidak ada data kategori</td></tr>'
                            );
                        }

                        // Panggil fungsi untuk pagination jika ada data
                        if (response.pagination) {
                            generatePagination(response.pagination);
                        }
                    },
                    error: function(xhr) {
                        console.error("Error loading kategori:", xhr.responseText);
                        $('#kategori-body').html(
                            '<tr><td colspan="4" class="text-center text-danger">Gagal memuat data kategori</td></tr>'
                        );
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
                <a class="page-link ${i === pagination.current_page ? 'text-white bg-primary' : ''}" href="#" data-page="${i}">${i}</a>
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

            // Event listener untuk pagination
            $(document).on('click', '#pagination a', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                let searchValue = $('#search').val();
                loadKategori(page, searchValue);
            });

            $('#search').on('keyup', function() {
                let searchValue = $(this).val();
                loadKategori(1, searchValue);
            });

            // Event listener untuk tombol Ubah Status
            $(document).on('click', '.toggle-status', function() {
                let kategoriId = $(this).data('id');

                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Status kategori akan diubah!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Ubah Status!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `https://berbagipendidikan.org/api/kategoriberita-toggle-status/${kategoriId}`,
                            type: 'POST',
                            success: function(response) {
                                Swal.fire("Sukses!",
                                    "Status kategori berhasil diperbarui.",
                                    "success");
                                loadKategori();
                            },
                            error: function() {
                                Swal.fire("Gagal!",
                                    "Terjadi kesalahan saat memperbarui status kategori.",
                                    "error");
                            }
                        });
                    }
                });
            });

            // Load kategori saat halaman dimuat
            loadKategori();

            // Event listener untuk tombol Show Kategori
            $(document).on('click', '.show-kategori', function() {
                let kategoriId = $(this).data('id');

                $.ajax({
                    url: `https://berbagipendidikan.org/api/kategori/${kategoriId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let kategori = response.data;

                            // Isi modal dengan data kategori
                            $('#show-name_kategori').text(kategori.name_kategori);
                            $('#show-status_kategori').text(kategori.status_kategori_berita);
                            $('#show-created_at').text(new Date(kategori.created_at)
                                .toLocaleString('id-ID'));
                            $('#show-updated_at').text(new Date(kategori.updated_at)
                                .toLocaleString('id-ID'));

                            // Tampilkan modal show
                            $('#showKategoriModal').modal('show');
                        } else {
                            Swal.fire("Gagal!", "Data kategori tidak ditemukan.", "error");
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire("Gagal!", "Tidak dapat mengambil data kategori.", "error");
                    }
                });
            });

            $('#create-kategori-form').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: 'https://berbagipendidikan.org/api/kategori-create',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#createKategoriModal').modal('hide');

                        // Reset form setelah berhasil
                        $('#create-kategori-form')[0].reset();

                        // SweetAlert sukses
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Kategori berhasil ditambahkan.",
                            icon: "success",
                            confirmButtonText: "OK"
                        });

                        loadKategori();
                    },
                    error: function(xhr) {
                        // Jika error validasi
                        let errorMessage = "Terjadi kesalahan!";
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).join("\n");
                        }

                        // SweetAlert error
                        Swal.fire({
                            title: "Gagal!",
                            text: errorMessage,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                });
            });


            $(document).on('click', '.edit-kategori', function() {
                let kategoriId = $(this).data('id');

                $.get(`https://berbagipendidikan.org/api/kategori/${kategoriId}`, function(response) {
                    $('#edit-id').val(response.data.id);
                    $('#edit-name_kategori').val(response.data.name_kategori);
                    $('#editKategoriModal').modal('show');
                });
            });

            $('#edit-kategori-form').on('submit', function(e) {
                e.preventDefault();
                let kategoriId = $('#edit-id').val();
                let formData = $(this).serialize();

                $.ajax({
                    url: `https://berbagipendidikan.org/api/kategori-update/${kategoriId}`,
                    type: 'PUT',
                    data: formData,
                    success: function(response) {
                        $('#editKategoriModal').modal('hide');

                        // SweetAlert untuk sukses
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Kategori berhasil diperbarui.",
                            icon: "success",
                            confirmButtonText: "OK"
                        });

                        loadKategori();
                    },
                    error: function(xhr) {
                        // SweetAlert untuk error
                        Swal.fire({
                            title: "Gagal!",
                            text: "Terjadi kesalahan saat memperbarui kategori.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                });
            });

            $(document).on('click', '.delete-kategori', function() {
                let kategoriId = $(this).data('id');

                // Konfirmasi SweetAlert sebelum menghapus
                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Kategori yang dihapus tidak dapat dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `https://berbagipendidikan.org/api/kategori-delete/${kategoriId}`,
                            type: 'DELETE',
                            success: function(response) {
                                // SweetAlert untuk sukses
                                Swal.fire({
                                    title: "Terhapus!",
                                    text: "Kategori berhasil dihapus.",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                });

                                loadKategori();
                            },
                            error: function(xhr) {
                                // SweetAlert untuk error
                                Swal.fire({
                                    title: "Gagal!",
                                    text: "Terjadi kesalahan saat menghapus kategori.",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });


            $(document).ready(function() {
                // Menutup modal saat tombol Close (X) atau "Tutup" diklik
                $(document).on('click', '.close-modal', function() {
                    $(this).closest('.modal').modal('hide');
                });

                // Menutup modal saat area di luar modal diklik
                $(document).on('click', function(e) {
                    if ($(e.target).hasClass('modal')) {
                        $(e.target).modal('hide');
                    }
                });

                // Debugging: Cek apakah event listener bekerja
                $('.modal').on('hidden.bs.modal', function() {
                    console.log("Modal tertutup: " + $(this).attr('id'));
                });

                // Debugging: Cek apakah modal benar-benar terbuka
                $('.modal').on('shown.bs.modal', function() {
                    console.log("Modal terbuka: " + $(this).attr('id'));
                });
            });


        });
    </script>
@endsection
