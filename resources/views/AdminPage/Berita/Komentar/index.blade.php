@extends('AdminPage.App.master')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="card-title">Daftar Semua Komentar Berita</h4>
                            <div class="d-flex">
                                <input type="text" id="search-komentar" class="form-control"
                                    placeholder="Cari nama pengirim">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pengirim</th>
                                            <th>Komentar</th>
                                            <th>Judul Berita</th>
                                            <th>Status Komentar</th>
                                            <th>Likes</th>
                                            <th>Waktu</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="komentar-body">
                                        <!-- Data komentar akan dimuat via JS -->
                                    </tbody>
                                </table>
                            </div>
                            <ul id="pagination" class="pagination justify-content-end mt-3" style="display: none;"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            const perPage = 10;
            let searchKeyword = '';

            function loadKomentar(page = 1, search = '') {
                $.ajax({
                    url: `https://berbagipendidikan.org/api/komentar?page=${page}&per_page=${perPage}&search=${search}`,
                    type: 'GET',
                    success: function(response) {
                        if (response.status && response.data.length > 0) {
                            let html = '';
                            let no = (page - 1) * perPage + 1;

                            response.data.forEach(komentar => {
                                html += generateRow(komentar, no++);
                                if (komentar.replies && komentar.replies.length > 0) {
                                    html += generateReplies(komentar.replies, komentar.berita
                                        ?.judul);
                                }
                            });

                            $('#komentar-body').html(html);
                            $('#pagination').show(); // tampilkan pagination
                            generatePagination(response.pagination);
                        } else {
                            $('#komentar-body').html(`
                    <tr>
                        <td colspan="8" class="text-center text-muted">No comments found.</td>
                    </tr>
                `);
                            $('#pagination').hide(); // sembunyikan pagination jika kosong
                        }
                    },
                    error: function() {
                        $('#komentar-body').html(`
                <tr><td colspan="8" class="text-danger text-center">Failed to load data.</td></tr>
            `);
                        $('#pagination').hide(); // sembunyikan juga saat error
                    }
                });
            }

            // Generate 1 baris komentar
            function generateRow(komentar, no = '', isReply = false, parentJudul = '-') {
                const indent = isReply ? ' style="padding-left: 30px;"' : '';
                const label = isReply ? '<span class="badge badge-info">Reply</span> ' : '';
                const statusClass = komentar.status_komentar === 'Aktif' ? 'btn-success' : 'btn-danger';
                const judul = komentar.berita?.judul ?? parentJudul ?? '-';
                const badgeReply = (!isReply && komentar.replies?.length) ?
                    `<span class="badge badge-info ms-2">${komentar.replies.length} Balasan</span>` :
                    '';

                return `
        <tr>
            <td>${no}</td>
            <td${indent}>${label}${komentar.nama_pengirim ?? '-'} ${badgeReply}</td>
            <td>${komentar.isi_komentar}</td>
            <td>${judul}</td>
            <td><span class="btn btn-sm ${statusClass}" style="width:100px; pointer-events:none;">${komentar.status_komentar}</span></td>
            <td><span class="badge badge-primary">${komentar.likes_komentar}</span></td>
            <td>${komentar.created_at}</td>
            <td>
                <div class="btn-group gap-2">
                    <button class="btn btn-info btn-sm rounded-circle toggle-status-komentar" data-id="${komentar.id_komentar}" title="Ubah Status">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                    <button class="btn btn-danger btn-sm rounded-circle delete-komentar" data-id="${komentar.id_komentar}" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
            }


            // Recursive reply render
            function generateReplies(replies, parentJudul) {
                let html = '';
                replies.forEach(reply => {
                    html += generateRow(reply, '-', true,
                    parentJudul); // gunakan '-' atau kosong di kolom nomor
                    if (reply.replies && reply.replies.length > 0) {
                        html += generateReplies(reply.replies, parentJudul);
                    }
                });
                return html;
            }


            // Pagination
            function generatePagination(pagination) {
                let html = '';
                const current = pagination.current_page;
                const last = pagination.last_page;

                if (current > 1) {
                    html += `<li class="page-item"><a class="page-link" data-page="1">First</a></li>`;
                    html +=
                        `<li class="page-item"><a class="page-link" data-page="${current - 1}">&laquo;</a></li>`;
                }

                for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                    const activeClass = i === current ? 'active' : '';
                    const textClass = i === current ? 'text-white bg-primary' : '';
                    html += `<li class="page-item ${activeClass}">
                            <a class="page-link ${textClass}" data-page="${i}">${i}</a>
                        </li>`;
                }

                if (current < last) {
                    html +=
                        `<li class="page-item"><a class="page-link" data-page="${current + 1}">&raquo;</a></li>`;
                    html += `<li class="page-item"><a class="page-link" data-page="${last}">Last</a></li>`;
                }

                $('#pagination').html(html);
            }

            // Pagination click
            $(document).on('click', '#pagination .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                currentPage = page;
                loadKomentar(page, searchKeyword);
            });

            // Search real-time (ajax debounce 500ms)
            let debounceTimeout = null;
            $('#search-komentar').on('keyup', function() {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    searchKeyword = $(this).val();
                    loadKomentar(1, searchKeyword);
                }, 500);
            });

            // Toggle status komentar
            $(document).on('click', '.toggle-status-komentar', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: "Ubah status komentar?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Ya",
                }).then(result => {
                    if (result.isConfirmed) {
                        $.post(`https://berbagipendidikan.org/api/komentar-toggle-status/${id}`,
                            function(res) {
                                Swal.fire("Berhasil!", res.message, "success");
                                loadKomentar(currentPage, searchKeyword);
                            });
                    }
                });
            });

            // Delete komentar
            $(document).on('click', '.delete-komentar', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: "Hapus komentar?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Hapus",
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `https://berbagipendidikan.org/api/komentar-delete/${id}`,
                            type: 'DELETE',
                            success: function(res) {
                                Swal.fire("Dihapus!", res.message, "success");
                                loadKomentar(currentPage, searchKeyword);
                            }
                        });
                    }
                });
            });

            // Initial load
            loadKomentar();
        });
    </script>
@endsection
