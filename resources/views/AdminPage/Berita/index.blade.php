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

        .ql-editor img {
            display: block;
            width: 100%;
            height: auto;
        }

        /* Style untuk masing-masing tag input (bisa disesuaikan agar mirip tombol kecil) */
        .edit-tag-input {
            display: inline-block;
            width: 100%;
        }

    </style>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
                                            <th>Tanggal</th>
                                            <th>Kategori Berita</th>
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
                                @include('AdminPage.Berita.show')
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
                            <input type="text" name="judul" id="judul-input" class="form-control" required>
                            <small id="seo-title-analysis" class="form-text text-muted"></small>
                        </div>
                        <div class="form-group">
                            <label>Konten</label>
                            <textarea name="konten" id="konten-create" class="form-control ckeditor" rows="5" style="display: none;"></textarea>
                            <small id="seo-content-analysis" class="form-text text-muted"></small>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori Berita</label>
                            <select name="id_kategori_berita" id="id_kategori_berita" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tags (Bisa lebih dari satu)</label>
                            <div id="tags-container">
                                <div class="row tag-item mb-2">
                                    <div class="col-md-6">
                                        <input type="text" name="tags[0][nama]" class="form-control" placeholder="Nama Tag">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="tags[0][link]" class="form-control" placeholder="Link Tag">
                                    </div>
                                    <div class="col-md-1">
                                        <!-- Tombol hapus tag, disembunyikan untuk input pertama -->
                                        <button type="button" class="btn btn-danger btn-sm remove-tag d-none">&times;</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-tag-btn" class="btn btn-secondary btn-sm">Tambah Tag</button>
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
                            <small id="edit-seo-title-analysis" class="form-text text-muted"></small>
                        </div>
                        <div class="form-group">
                            <label>Konten</label>
                            <div id="edit-konten-editor" style="height: 200px;"></div> <!-- Quill Editor -->
                            <input type="hidden" id="edit-konten" name="konten"> 
                            <small id="edit-seo-content-analysis" class="form-text text-muted"></small>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" id="edit-tanggal" name="tanggal" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori Berita</label>
                            <select name="id_kategori_berita" id="edit-id_kategori_berita" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tags (Bisa lebih dari satu)</label>
                            <div id="edit-tags-container">
                                <!-- Input tag akan diisi secara dinamis, baik dari data existing maupun default kosong -->
                            </div>
                            <button type="button" id="add-edit-tag-btn" class="btn btn-secondary btn-sm">Tambah Tag</button>
                        </div>
                        <!-- Foto 1 -->
                        <div class="form-group">
                            <label>Foto 1</label>
                            <input type="file" id="edit-foto" name="foto" class="form-control">
                            <input type="hidden" id="old-foto" name="old_foto"> <!-- Hidden input untuk foto lama -->
                            <img id="preview-foto" src="" class="img-thumbnail mt-2"
                                style="display: none; width: 150px;">
                        </div>

                        <!-- Foto 2 -->
                        <div class="form-group">
                            <label>Foto 2</label>
                            <input type="file" id="edit-foto2" name="foto2" class="form-control">
                            <input type="hidden" id="old-foto2" name="old_foto2"> <!-- Hidden input untuk foto lama -->
                            <img id="preview-foto2" src="" class="img-thumbnail mt-2"
                                style="display: none; width: 150px;">
                        </div>

                        <!-- Foto 3 -->
                        <div class="form-group">
                            <label>Foto 3</label>
                            <input type="file" id="edit-foto3" name="foto3" class="form-control">
                            <input type="hidden" id="old-foto3" name="old_foto3"> <!-- Hidden input untuk foto lama -->
                            <img id="preview-foto3" src="" class="img-thumbnail mt-2"
                                style="display: none; width: 150px;">
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
    {{-- <script src="{{ asset('assets_admin/ckeditor/ckeditor.js') }}"></script> --}}
    {{-- <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> --}}
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        $(document).ready(function() {

            function initQuill(selector) {
                $(selector).each(function() {
                    const editorId = $(this).attr('id');

                    // Jika editor sudah ada, hapus terlebih dahulu
                    if (window[editorId]) {
                        window[editorId] = null;
                    }

                    // Inisialisasi Quill pada elemen dengan ID yang sesuai
                    window[editorId] = new Quill('#' + editorId + '-editor', {
                        theme: 'snow',
                        placeholder: 'Tulis konten di sini...',
                        modules: {
                            toolbar: {
                                container: [
                                    [{
                                        'header': [1, 2, false]
                                    }],
                                    ['bold', 'italic', 'underline'],
                                    ['link', 'blockquote', 'code-block',
                                        'image'
                                    ], // Tambahkan tombol gambar
                                    [{
                                        'list': 'ordered'
                                    }, {
                                        'list': 'bullet'
                                    }],
                                    [{
                                        'align': []
                                    }],
                                    ['clean']
                                ],
                                handlers: {
                                    image: function() {
                                        imageHandler(editorId);
                                    }
                                }
                            }
                        }
                    });

                    // Update hidden textarea dengan konten dari Quill saat form dikirim
                    $('#' + editorId).closest('form').on('submit', function(e) {
                        let konten = window[editorId].root.innerHTML.trim();

                        if (konten === '<p><br></p>' || konten === '') {
                            Swal.fire("Peringatan", "Konten berita tidak boleh kosong!", "warning");
                            e.preventDefault();
                            return false;
                        }

                        $('#' + editorId).val(konten);
                    });
                });
            }

            function imageHandler(editorId) {
                let input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = function() {
                    let file = input.files[0];
                    if (file) {
                        // Validasi ukuran maksimal 2MB
                        if (file.size > 2 * 1024 * 1024) {
                            alert("Ukuran gambar terlalu besar! Maksimal 2MB.");
                            return;
                        }

                        // Validasi format file
                        let allowedFormats = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
                        if (!allowedFormats.includes(file.type)) {
                            alert("Format gambar tidak didukung! Gunakan JPEG, PNG, JPG, atau GIF.");
                            return;
                        }

                        // **Upload Gambar ke Server**
                        let formData = new FormData();
                        formData.append("image", file);

                        fetch('/upload-image', {
                                method: "POST",
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(result => {
                                console.log("Response dari server:", result); // Debugging

                                if (result.success) {
                                    let imageUrl = result.image_url;
                                    let range = window[editorId].getSelection();
                                    window[editorId].insertEmbed(range.index, 'image', imageUrl);
                                } else {
                                    console.error("Gagal mengunggah gambar:", result);
                                    alert("Gagal mengunggah gambar! Periksa log.");
                                }
                            })
                            .catch(error => {
                                console.error("Error uploading image:", error);
                                alert("Terjadi kesalahan saat mengunggah gambar.");
                            });
                    }
                };
            }

            // Inisialisasi Quill untuk textarea dengan class 'ckeditor'
            $('.ckeditor').each(function() {
                const editorId = $(this).attr('id');
                $(this).after('<div id="' + editorId + '-editor" style="height: 200px;"></div>');
                $(this).hide();
            });

            initQuill('.ckeditor');

            // Re-inisialisasi saat modal dibuka (untuk modal edit)
            $('.modal').on('shown.bs.modal', function() {
                $('.ckeditor').each(function() {
                    const editorId = $(this).attr('id');
                    if (window[editorId]) {
                        window[editorId].focus();
                    }
                });
            });

            let currentPage = 1;
            let perPage = 10;

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

                            let statusClass = berita.status_berita === "Aktif" ? "btn-success" :
                                "btn-danger";
                            let statusText = berita.status_berita;

                            let kategoriNama = berita.kategori ? berita.kategori.name_kategori :
                                '-';

                            // 🔹 Perbaiki konten agar gambar tidak selalu di tengah
                            let formattedKonten = berita.konten
                                .replace(/<img /g,
                                    '<img style="max-width:100%; height:auto;" '
                                ) // Hapus auto center
                                .replace(/margin:\s?auto;/g,
                                    ''); // Hilangkan margin auto jika ada

                            beritaHtml += `
                    <tr id="row-${berita.id}">
                        <td>${no++}</td>
                        <td>${berita.judul}</td>
                        <td>${berita.tanggal}</td>
                        <td>${kategoriNama}</td>
                        <td><img src="${foto1}" class="img-thumbnail" style="width: 90px; height: auto;"></td>
                        <td><img src="${foto2}" class="img-thumbnail" style="width: 90px; height: auto;"></td>
                        <td><img src="${foto3}" class="img-thumbnail" style="width: 90px; height: auto;"></td>
                       <td>
                            <span class="badge ${statusClass == 'btn-success' ? 'badge-success' : 'badge-danger'}"
                                style="display: inline-block; width: 100px; height: 30px; line-height: 30px; text-align: center; font-size: 0.875rem;">
                                ${statusText}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group gap-2">
                                <!-- Tombol Show -->
                                <button class="btn btn-primary btn-sm rounded-circle p-2 show-berita"
                                    data-id="${berita.id}" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- Tombol Edit -->
                                <button class="btn btn-warning btn-sm rounded-circle p-2 edit-berita"
                                    data-id="${berita.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Tombol Toggle Status -->
                                <button class="btn btn-info btn-sm rounded-circle p-2 toggle-status"
                                    data-id="${berita.id}" title="Ubah Status">
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

            // Fungsi untuk mengambil daftar kategori berita dan mengisi dropdown
            function loadKategoriDropdown() {
                $.ajax({
                    url: 'https://berbagipendidikan.org/api/kategori-berita',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data && response.data.data) {
                            let kategoriHtml = '<option value="">Pilih Kategori</option>';
                            $.each(response.data.data, function(index, kategori) {
                                kategoriHtml +=
                                    `<option value="${kategori.id}">${kategori.name_kategori}</option>`;
                            });
                            $('#id_kategori_berita').html(kategoriHtml);
                        } else {
                            $('#id_kategori_berita').html(
                                '<option value="">Kategori tidak tersedia</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error("Error loading kategori:", xhr.responseText);
                        $('#id_kategori_berita').html(
                            '<option value="">Gagal memuat kategori</option>');
                    }
                });
            }

             // SEO Analysis untuk Judul - Pastikan input judul memiliki id="judul-input"
             $('#judul-input').on('input', function(){
                let judul = $(this).val().trim();
                let length = judul.length;
                let message = "";
                
                // Rekomendasi SEO untuk judul: idealnya antara 50 dan 70 karakter.
                if(length < 50) {
                    let percent = Math.round((length / 50) * 100);
                    message = "Judul terlalu pendek (" + length + " karakter, " + percent + "% optimal). Disarankan minimal 50 karakter.";
                } else if(length <= 70) {
                    message = "Judul optimal (" + length + " karakter).";
                } else {
                    // Untuk judul lebih dari 70 karakter, sebaiknya dipersingkat.
                    let excess = length - 70;
                    let percent = Math.round(100 - (excess / 70) * 100);
                    if(percent < 0) percent = 0;
                    message = "Judul terlalu panjang (" + length + " karakter, " + percent + "% optimal). Disarankan maksimal 70 karakter.";
                }
                $('#seo-title-analysis').text(message);
            });

            // Pastikan instance Quill untuk konten sudah terdefinisi dengan id "konten-create"
            if(window['konten-create']) {
                window['konten-create'].on('text-change', function() {
                    let text = window['konten-create'].getText().trim();
                    // Menghitung jumlah kata dengan cara sederhana
                    let wordCount = text.split(/\s+/).filter(word => word.length > 0).length;
                    let message = "";
                    // Rekomendasi SEO untuk konten: minimal 300 kata agar lebih SEO friendly.
                    if(wordCount < 300) {
                        let percent = Math.round((wordCount / 300) * 100);
                        message = "Konten kurang (hanya " + wordCount + " kata, " + percent + "% optimal). Disarankan minimal 300 kata.";
                    } else {
                        message = "Konten cukup (" + wordCount + " kata).";
                    }
                    $('#seo-content-analysis').text(message);
                });
            } else {
                console.error("Quill instance untuk konten-create tidak ditemukan. Pastikan id konten-create sudah benar.");
            }

            // Panggil fungsi saat modal dibuka
            $('#createBeritaModal').on('shown.bs.modal', function() {
                loadKategoriDropdown();
            });

            // Handle Form Submit (AJAX) untuk menambahkan berita
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
                        if (xhr.status === 422) {
                            let res = xhr.responseJSON;
                            let errors = res.errors;

                            // Tampilkan error validasi khusus
                            if (errors.judul) {
                                Swal.fire("Gagal!", errors.judul[0], "error");
                            } else {
                                Swal.fire("Gagal!", "Validasi gagal. Periksa isian Anda.", "warning");
                            }

                            // Bisa juga tampilkan kesalahan untuk semua field lain jika mau
                            // Contoh: console.log(errors);
                        } else {
                            Swal.fire("Gagal!", "Terjadi kesalahan saat menambah berita.", "error");
                        }
                    }
                });
            });


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

            // Event listener untuk pencarian
            $('#search').on('keyup', function() {
                let searchValue = $(this).val();
                loadBerita(1, searchValue);
            });

            $(document).ready(function() {
                // Inisialisasi counter untuk tags, mulai dari 1 karena index ke-0 sudah ada
                let tagIndex = 1;

                // Event handler untuk tombol Tambah Tag
                $('#add-tag-btn').on('click', function() {
                    // Buat HTML baru untuk tag input
                    let newTagHtml = `
                        <div class="row tag-item mb-2">
                            <div class="col-md-6">
                                <input type="text" name="tags[${tagIndex}][nama]" class="form-control" placeholder="Nama Tag">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="tags[${tagIndex}][link]" class="form-control" placeholder="Link Tag">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-tag">&times;</button>
                            </div>
                        </div>
                    `;
                    // Tambahkan HTML baru ke container tags
                    $('#tags-container').append(newTagHtml);
                    tagIndex++;
                });

                // Event delegation untuk tombol hapus tag
                $('#tags-container').on('click', '.remove-tag', function() {
                    $(this).closest('.tag-item').remove();
                });

                // Inisialisasi input lainnya (misalnya, inisialisasi dropdown, quill editor, dll.)
                // ... kode yang sudah ada ...
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
                                Swal.fire("Sukses!",
                                    "Status berita berhasil diperbarui.", "success");
                                loadBerita(); // Refresh daftar berita
                            },
                            error: function() {
                                Swal.fire("Gagal!",
                                    "Terjadi kesalahan saat memperbarui status berita.",
                                    "error");
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.show-berita', function() {
                let beritaId = $(this).data('id');

                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita/${beritaId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let berita = response.data;

                            // Format dan set judul, tanggal, dan konten berita
                            let formattedDate = berita.tanggal ?
                                new Date(berita.tanggal).toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'long',
                                    year: 'numeric'
                                }) :
                                new Date().toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'long',
                                    year: 'numeric'
                                });
                            $('#modal-tanggal').text(formattedDate);
                            $('#modal-views').text(berita.views_berita || 0);
                            $('#modal-judul').text(berita.judul);
                            document.getElementById('modal-konten').innerHTML = berita.konten;
                            $('#modal-konten img').each(function() {
                                $(this).attr('style', 'max-width: 100%; height: auto; display: block;');
                            });

                            // Buat carousel gambar
                            let imagesHtml = '';
                            let baseUrl = "https://berbagipendidikan.org";
                            let imageIndex = 0;
                            [berita.foto, berita.foto2, berita.foto3].forEach((foto) => {
                                if (foto) {
                                    imagesHtml += `
                                        <div class="carousel-item ${imageIndex === 0 ? 'active' : ''}">
                                            <img src="${baseUrl + foto}" class="d-block w-100" alt="Gambar Berita">
                                        </div>`;
                                    imageIndex++;
                                }
                            });
                            $('#modal-images').html(imagesHtml);

                            // Proses tampilkan data tags (jika ada)
                            if (berita.tags && berita.tags.length > 0) {
                                let tagsHtml = '';
                                berita.tags.forEach(function(tag) {
                                    tagsHtml += `<a href="${tag.link}" target="_blank" class="tag-link">${tag.nama}</a>`;
                                });
                                $('#modal-tags').html(tagsHtml);
                            } else {
                                $('#modal-tags').html('');
                            }

                            // Tampilkan Modal
                            $('#showBeritaModal').modal('show');
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

            // Event listener untuk tombol Close (X) dan tombol Batal
            $('.close, .close-modal').on('click', function() {
                $('#editBeritaModal').modal('hide');
            });

            // Load berita pertama kali
            loadBerita();
        });

        /* Form Edit */
        $(document).ready(function() {
            // Fungsi untuk mengisi dropdown kategori pada form edit
            function loadKategoriDropdownEdit(selectedKategoriId = null) {
                $.ajax({
                    url: 'https://berbagipendidikan.org/api/kategori-berita',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data && response.data.data) {
                            let kategoriHtml = '<option value="">Pilih Kategori</option>';

                            $.each(response.data.data, function(index, kategori) {
                                let isSelected = (selectedKategoriId == kategori.id) ?
                                    'selected' : '';
                                kategoriHtml +=
                                    `<option value="${kategori.id}" ${isSelected}>${kategori.name_kategori}</option>`;
                            });

                            $('#edit-id_kategori_berita').html(kategoriHtml);
                        } else {
                            $('#edit-id_kategori_berita').html(
                                '<option value="">Kategori tidak tersedia</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error("Error loading kategori:", xhr.responseText);
                        $('#edit-id_kategori_berita').html(
                            '<option value="">Gagal memuat kategori</option>');
                    }
                });
            }

            function imageHandler(editor) {
                let input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = function() {
                    let file = input.files[0];
                    if (file) {
                        // Validasi ukuran maksimal 2MB
                        if (file.size > 2 * 1024 * 1024) {
                            alert("Ukuran gambar terlalu besar! Maksimal 2MB.");
                            return;
                        }

                        // Validasi format file
                        let allowedFormats = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
                        if (!allowedFormats.includes(file.type)) {
                            alert("Format gambar tidak didukung! Gunakan JPEG, PNG, JPG, atau GIF.");
                            return;
                        }

                        // **Upload Gambar ke Server**
                        let formData = new FormData();
                        formData.append("image", file);

                        fetch('/upload-image', {
                                method: "POST",
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(result => {
                                console.log("Response dari server:", result); // Debugging

                                if (result.success) {
                                    let imageUrl = result.image_url;
                                    let range = editor.getSelection();

                                    // **Tambahkan gambar tanpa atribut "margin:auto;"**
                                    editor.clipboard.dangerouslyPasteHTML(range.index,
                                        `<img src="${imageUrl}" style="max-width:100%; height:auto;">`);
                                } else {
                                    console.error("Gagal mengunggah gambar:", result);
                                    alert("Gagal mengunggah gambar! Periksa log.");
                                }
                            })
                            .catch(error => {
                                console.error("Error uploading image:", error);
                                alert("Terjadi kesalahan saat mengunggah gambar.");
                            });
                    }
                };
            }

            // Analisis SEO untuk Judul Form Edit
            $('#edit-judul').on('input', function() {
                let judul = $(this).val().trim();
                let length = judul.length;
                let message = "";

                if (length < 50) {
                    let percent = Math.round((length / 50) * 100);
                    message = "Judul terlalu pendek (" + length + " karakter, " + percent + "% optimal). Disarankan minimal 50 karakter.";
                } else if (length <= 70) {
                    message = "Judul optimal (" + length + " karakter).";
                } else {
                    let excess = length - 70;
                    let percent = Math.round(100 - (excess / 70) * 100);
                    if (percent < 0) percent = 0;
                    message = "Judul terlalu panjang (" + length + " karakter, " + percent + "% optimal). Disarankan maksimal 70 karakter.";
                }

                $('#edit-seo-title-analysis').text(message);
            });

            // Inisialisasi Quill untuk form Edit
            var quillEdit = new Quill('#edit-konten-editor', {
                theme: 'snow',
                placeholder: 'Tulis konten berita di sini...',
                modules: {
                    toolbar: {
                        container: [
                            [{'header': [1, 2, false]}],
                            ['bold', 'italic', 'underline'],
                            ['link', 'blockquote', 'code-block', 'image'],
                            [{'list': 'ordered'}, {'list': 'bullet'}],
                            [{'align': []}],
                            ['clean']
                        ],
                        handlers: {
                            image: function() {
                                imageHandler(quillEdit);
                            }
                        }
                    }
                }
            });

            // Barulah setelah inisialisasi Quill, pasang listener untuk analisis SEO
            quillEdit.on('text-change', function() {
                let text = quillEdit.getText().trim();
                let wordCount = text.split(/\s+/).filter(word => word.length > 0).length;
                let message = "";

                if (wordCount < 300) {
                    let percent = Math.round((wordCount / 300) * 100);
                    message = "Konten kurang (hanya " + wordCount + " kata, " + percent + "% optimal). Disarankan minimal 300 kata.";
                } else {
                    message = "Konten cukup (" + wordCount + " kata).";
                }

                $('#edit-seo-content-analysis').text(message);
            });

            // Variabel global untuk edit modal tag index
            let editTagIndex = 0;

            function populateEditTags(existingTags) {
                // Jika data tags ada, buat row untuk setiap tag dengan index sesuai
                let tagsHtml = '';
                if (existingTags && existingTags.length > 0) {
                    existingTags.forEach(function(tag, index) {
                        tagsHtml += `
                        <div class="row tag-item mb-2">
                            <div class="col-md-6">
                                <input type="text" name="tags[${index}][nama]" class="form-control" placeholder="Nama Tag" value="${tag.nama}">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="tags[${index}][link]" class="form-control" placeholder="Link Tag" value="${tag.link}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-edit-tag">&times;</button>
                            </div>
                        </div>
                        `;
                    });
                    editTagIndex = existingTags.length;
                } else {
                    // Jika tidak ada tag, sediakan satu baris default
                    tagsHtml = `
                        <div class="row tag-item mb-2">
                            <div class="col-md-6">
                                <input type="text" name="tags[0][nama]" class="form-control" placeholder="Nama Tag">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="tags[0][link]" class="form-control" placeholder="Link Tag">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-edit-tag d-none">&times;</button>
                            </div>
                        </div>
                    `;
                    editTagIndex = 1;
                }
                $('#edit-tags-container').html(tagsHtml);
            }

            // Event handler untuk tombol Tambah Tag pada modal edit
            $('#add-edit-tag-btn').on('click', function() {
                let newEditTagHtml = `
                    <div class="row tag-item mb-2">
                        <div class="col-md-6">
                            <input type="text" name="tags[${editTagIndex}][nama]" class="form-control" placeholder="Nama Tag">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="tags[${editTagIndex}][link]" class="form-control" placeholder="Link Tag">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-edit-tag">&times;</button>
                        </div>
                    </div>
                `;
                $('#edit-tags-container').append(newEditTagHtml);
                editTagIndex++;
            });

            // Event delegation untuk tombol hapus tag di modal edit
            $('#edit-tags-container').on('click', '.remove-edit-tag', function() {
                $(this).closest('.tag-item').remove();
            });

            // Saat modal edit dibuka, lakukan AJAX untuk mengambil data berita termasuk tags dan isi form edit
            $(document).on('click', '.edit-berita', function() {
                let beritaId = $(this).data('id');

                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita/${beritaId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response); // Debugging
                        if (response.success) {
                            let berita = response.data;
                            let baseUrl = "https://berbagipendidikan.org";

                            // Reset form edit
                            $('#edit-berita-form')[0].reset();
                            $('#preview-foto, #preview-foto2, #preview-foto3').hide();

                            // Isi form dengan data berita
                            $('#edit-id').val(berita.id);
                            $('#edit-judul').val(berita.judul);
                            $('#edit-judul').trigger('input');
                            let formattedDate = new Date(berita.tanggal).toISOString().split('T')[0];
                            $('#edit-tanggal').val(formattedDate);
                            loadKategoriDropdownEdit(berita.kategori ? berita.kategori.id : null);

                            // Set konten ke Quill editor
                            quillEdit.root.innerHTML = berita.konten.replace(/<img /g, '<img style="max-width:100%; height:auto;" ');

                            // Tampilkan preview foto dan simpan nilai lama
                            if (berita.foto) {
                                $('#preview-foto').attr('src', baseUrl + berita.foto).show();
                                $('#old-foto').val(berita.foto);
                            }
                            if (berita.foto2) {
                                $('#preview-foto2').attr('src', baseUrl + berita.foto2).show();
                                $('#old-foto2').val(berita.foto2);
                            }
                            if (berita.foto3) {
                                $('#preview-foto3').attr('src', baseUrl + berita.foto3).show();
                                $('#old-foto3').val(berita.foto3);
                            }

                            // Populate dynamic tags di modal edit dengan data tags dari berita
                            populateEditTags(berita.tags);

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

           // Handle form submit untuk update berita
            $('#edit-berita-form').on('submit', function(e) {
                e.preventDefault();
                let beritaId = $('#edit-id').val();

                // Simpan konten Quill ke input hidden
                let konten = quillEdit.root.innerHTML.trim();
                if (konten === '<p><br></p>' || konten === '') {
                    Swal.fire("Peringatan", "Konten berita tidak boleh kosong!", "warning");
                    return;
                }
                $('#edit-konten').val(konten);

                let formData = new FormData(this);

                // Tambahkan foto lama jika foto baru tidak dipilih
                if (!$('#edit-foto')[0].files.length) {
                    formData.append('old_foto', $('#old-foto').val());
                }
                if (!$('#edit-foto2')[0].files.length) {
                    formData.append('old_foto2', $('#old-foto2').val());
                }
                if (!$('#edit-foto3')[0].files.length) {
                    formData.append('old_foto3', $('#old-foto3').val());
                }

                $.ajax({
                    url: `https://berbagipendidikan.org/api/berita-update/${beritaId}`,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire("Sukses!", "Berita berhasil diperbarui.", "success");
                        $('#editBeritaModal').modal('hide');
                        loadBerita(); // Refresh daftar berita
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let response = xhr.responseJSON;
                            if (response && response.errors) {
                                let pesanError = '';
                                $.each(response.errors, function(field, messages) {
                                    pesanError += `${messages.join(', ')}<br>`;
                                });

                                Swal.fire({
                                    title: 'Validasi Gagal',
                                    html: pesanError,
                                    icon: 'warning'
                                });
                            } else {
                                Swal.fire("Gagal!", "Validasi gagal, tetapi tidak ada pesan error yang dikembalikan.", "error");
                            }
                        } else {
                            Swal.fire("Gagal!", "Terjadi kesalahan saat memperbarui berita.", "error");
                        }
                    }
                });
            });


            // Reset form edit saat modal ditutup
            $('#editBeritaModal').on('hidden.bs.modal', function() {
                $('#edit-berita-form')[0].reset();
                $('#preview-foto, #preview-foto2, #preview-foto3').hide();
                quillEdit.root.innerHTML = '';
                $('#edit-tags-container').html(''); // Reset tags container
            });

        });
    </script>
@endsection
