{{-- resources/views/LandingPageKilau/Components/article-users.blade.php --}}
@extends('App.master')

@section('style')
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
    <style>
        #artikel-container {
            display: flex;
            gap: 1.25rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding-bottom: .75rem;
            margin-bottom: 1rem;
        }

        #artikel-container::-webkit-scrollbar {
            height: .35rem
        }

        #artikel-container::-webkit-scrollbar-thumb {
            background: #0d6efd;
            border-radius: 5px
        }

        .art-card {
            flex: 0 0 320px;
            background: #fff;
            border-radius: .8rem;
            overflow: hidden;
            box-shadow: 0 0 .75rem rgba(0, 0, 0, .08);
            display: flex;
            flex-direction: column;
        }

        .art-card img {
            object-fit: cover;
            height: 220px
        }

        .art-card .title {
            font-size: 1.05rem;
            font-weight: 600;
            min-height: 48px
        }

        .art-card .meta {
            font-size: .8rem;
            color: #6c757d
        }

        .art-card {
            position: relative;
        }
    </style>
@endsection


@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Artikel Kilau Indonesia</h4>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createArticleModal">
                <i class="fas fa-plus me-1"></i> Tambah Artikel
            </button>
        </div>

        <input type="text" id="search" class="form-control mb-4" placeholder="Cari judul…">

        <div id="artikel-container">
            <div class="w-100 text-center py-4">Memuat data…</div>
        </div>

        <nav aria-label="page">
            <ul id="pagination" class="pagination justify-content-center"></ul>
        </nav>
    </div>

    {{-- — MODAL TAMBAH — --}}
    <div class="modal fade" id="createArticleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="create-article-form" enctype="multipart/form-data">@csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Artikel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" name="judul" id="judul-input" class="form-control" required>
                            <small id="seo-title-analysis" class="form-text"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Penulis</label>
                            <input type="text" id="author-input" name="author" class="form-control">
                        </div>

                        <div class="mb-3 d-none">
                            <label class="form-label">Foto Penulis</label>
                            <input type="text" id="photo-author-input" name="photo_author" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konten</label>
                            <textarea name="konten" id="konten-create" class="form-control d-none"></textarea>
                            <small id="seo-content-analysis" class="form-text"></small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="kategori_article_id" id="kategori_article_id" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <div id="tags-container">
                                <div class="row tag-item g-2 mb-2">
                                    <div class="col-md-6"><input name="tags[0][nama]" class="form-control"
                                            placeholder="Nama Tag"></div>
                                    <div class="col-md-5"><input name="tags[0][link]" class="form-control"
                                            placeholder="Link Tag"></div>
                                    <div class="col-md-1"></div>
                                </div>
                            </div>
                            <button type="button" id="add-tag-btn" class="btn btn-outline-secondary btn-sm">Tambah
                                Tag</button>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-4"><label class="form-label">Foto 1</label><input type="file"
                                    name="photo[]" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label">Foto 2</label><input type="file"
                                    name="photo[]" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label">Foto 3</label><input type="file"
                                    name="photo[]" class="form-control"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- — MODAL DETAIL — --}}
    <div class="modal fade" id="detailArticleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="dt-title" class="modal-title">Detail Artikel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <span id="dt-status" class="badge"></span>
                    </div>

                    <div class="mb-2 text-muted small">
                        <span id="dt-author"></span> · <span id="dt-date"></span> ·
                        <span>Kategori: <strong id="dt-kategori"></strong></span>
                    </div>

                    <div id="dt-feedback" class="alert alert-warning small d-none"></div>

                    <div class="mb-3" id="dt-photos" class="d-flex gap-2 flex-wrap"></div>

                    <div id="dt-tags" class="mt-2"></div>

                    <div class="mt-3">
                        <div id="dt-content"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    {{-- — MODAL EDIT — --}}
    <div class="modal fade" id="editArticleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="edit-article-form" enctype="multipart/form-data">@csrf
                    @method('PUT')
                    <input type="hidden" id="edit-id">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Artikel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        {{-- Info status + feedback (readonly) --}}
                        <div id="edit-status-row" class="mb-3 d-none">
                            <span id="edit-status-badge" class="badge"></span>
                        </div>
                        <div id="edit-feedback-row" class="alert alert-warning small d-none"></div>

                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input id="edit-judul-input" name="judul" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Penulis</label>
                            <input id="edit-author-input" name="author" class="form-control">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="edit-tanggal-input" name="tanggal" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori</label>
                                <select id="edit-kategori_article_id" name="kategori_article_id" class="form-select"
                                    required>
                                    <option value="">Memuat…</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konten</label>
                            <textarea id="edit-konten" name="konten" class="form-control d-none"></textarea>
                            {{-- Quill editor dibuat via JS --}}
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <div id="edit-tags-container"></div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Tambah Foto (opsional)</label>
                            <input type="file" name="photo[]" class="form-control mb-2" accept="image/*" multiple>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal" type="button">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {

            /* — CONSTANTS — */
            // const LIST_URL = "{{ route('lp.article.external.list') }}";
            const LIST_URL = "{{ route('lp.article.external.mylist') }}"; // ← ganti ke route baru
            const STORE_URL = "{{ route('lp.article.external.store') }}";
            const CAT_URL = "{{ route('lp.article.external.kategori') }}";
            const IMG_URL = "{{ route('lp.article.external.uploadImage') }}";
            const AUTHOR_KEY = 'user_name';
            const PHOTO_KEY = 'user_photo';
            const perPage = 6;
            let page = 1;

            /* -- helper untuk domain Kilau yang kadang double-slash dll. -- */
            function fixKilauUrl(u) {
                if (!u) return '';
                if (u.startsWith('http')) return u;
                return 'https://kilauindonesia.id' + (u.startsWith('/') ? u : '/' + u);
            }

            /* — QUILL — */
            $('#konten-create').after('<div id="konten-create-editor" style="height:200px"></div>').hide();
            const ql = new Quill('#konten-create-editor', {
                theme: 'snow',
                placeholder: 'Tulis konten di sini…',
                modules: {
                    toolbar: {
                        container: [
                            ['bold', 'italic', 'underline'],
                            ['link', 'image'],
                            [{
                                list: 'bullet'
                            }],
                            ['clean']
                        ],
                        handlers: {
                            image: () => uploadImage(ql) // ← pasang handler custom
                        }
                    }
                }
            });

            /* —— UPLOAD dari toolbar —— */
            function uploadImage(q) {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';
                input.click();
                input.onchange = () => {
                    const file = input.files[0];
                    if (!file) return;
                    if (file.size > 2 * 1024 * 1024) return Swal.fire('Ukuran maksimal 2 MB');
                    if (!['image/jpeg', 'image/png', 'image/jpg', 'image/gif'].includes(file.type))
                        return Swal.fire('Format harus JPG/PNG/GIF');

                    const fd = new FormData();
                    fd.append('image', file);
                    fetch(IMG_URL, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: fd
                        })
                        .then(r => r.json())
                        .then(r => {
                            if (r.success) {
                                const range = q.getSelection(true);
                                q.insertEmbed(range.index, 'image', r.image_url, 'user');
                            } else Swal.fire('Gagal upload');
                        })
                        .catch(() => Swal.fire('Gagal upload'));
                };
            }

            /* —— isi author & foto otomatis saat modal buka —— */
            $('#createArticleModal').on('shown.bs.modal', () => {
                const author = localStorage.getItem(AUTHOR_KEY) || '';
                const raw = localStorage.getItem(PHOTO_KEY) || '';
                const photo = fixKilauUrl(raw);
                if (photo !== raw) localStorage.setItem(PHOTO_KEY, photo);

                $('#author-input').val(author);
                $('#photo-author-input').val(photo);

                loadKategori(); // muat dropdown setiap kali modal dibuka
            });

            /* SEO */
            /* ---------- helper warna ---------- */
            function setHint($el, msg, status) {
                $el.text(msg)
                    .removeClass('text-danger text-success text-warning')
                    .addClass(
                        status === 'good' ? 'text-success' :
                        status === 'warn' ? 'text-warning' :
                        'text-danger'
                    );
            }

            /* ---------- Hint Judul ---------- */
            $('#judul-input').on('input', function() {
                const len = this.value.trim().length;
                if (len < 50) setHint($('#seo-title-analysis'),
                    `Terlalu pendek (${len}/50 karakter)`, 'bad');
                else if (len <= 70) setHint($('#seo-title-analysis'),
                    `Judul optimal (${len} karakter)`, 'good');
                else setHint($('#seo-title-analysis'),
                    `Terlalu panjang (${len} karakter)`, 'warn');
            });

            /* ---------- Hint Konten ---------- */
            function updateCreateContentHint() {
                const words = ql.getText().trim().split(/\s+/).filter(Boolean).length;
                if (words < 300) setHint($('#seo-content-analysis'),
                    `Terlalu pendek (${words}/300 kata)`, 'bad');
                else if (words <= 700) setHint($('#seo-content-analysis'),
                    `Konten optimal (${words} kata)`, 'good');
                else setHint($('#seo-content-analysis'),
                    `Terlalu panjang (${words} kata)`, 'warn');
            }
            ql.on('text-change', updateCreateContentHint);


            /* — Tag dinamis — */
            let tagIdx = 1;
            $('#add-tag-btn').on('click', () => {
                $('#tags-container').append(`
                <div class="row tag-item g-2 mb-2">
                  <div class="col-md-6"><input name="tags[${tagIdx}][nama]" class="form-control" placeholder="Nama Tag"></div>
                  <div class="col-md-5"><input name="tags[${tagIdx}][link]" class="form-control" placeholder="Link Tag"></div>
                  <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm remove-tag">&times;</button></div>
                </div>`);
                tagIdx++;
            });
            $('#tags-container').on('click', '.remove-tag', function() {
                $(this).closest('.tag-item').remove();
            });

            /* — Dropdown kategori (AJAX local / API) — */
            function loadKategori() {
                $('#kategori_article_id').html('<option>Memuat…</option>');
                $.get(CAT_URL) // ← gunakan helper
                    .done(r => {
                        let opt = '<option value="">Pilih Kategori</option>';
                        (r.data || []).forEach(k => opt +=
                            `<option value="${k.id}">${k.name_kategori}</option>`);
                        $('#kategori_article_id').html(opt);
                    })
                    .fail(x => {
                        console.warn('Kategori error', x);
                        $('#kategori_article_id').html('<option>Gagal memuat</option>');
                    });
            }
            $('#createArticleModal').on('shown.bs.modal', loadKategori);

            /* — Submit — */
            $('#create-article-form').on('submit', function(e) {
                e.preventDefault();
                $('#konten-create').val(ql.root.innerHTML.trim());
                $.ajax({
                    url: STORE_URL,
                    method: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: () => {
                        Swal.fire("Berhasil", "Artikel ditambahkan", "success");
                        $('#createArticleModal').modal('hide');
                        this.reset();
                        ql.setContents([]);
                        loadArtikel(page);
                    },
                    error: x => {
                        const msg = x.status === 422 ? Object.values(x.responseJSON.errors)[0][
                            0
                        ] : "Gagal menyimpan";
                        Swal.fire("Error", msg, "error");
                    }
                });
            });

            /* — Helpers — */
            function formatTgl(s) {
                return new Date(s).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            /* — Live search — */
            $('#search').on('keyup', () => loadArtikel(1, $('#search').val()));

            /* — Render list — */
            /* — Render list milik user login — */
            function loadArtikel(page = 1, search = '') {
                const $container = $('#artikel-container');
                $container.html('<div class="w-100 text-center py-4">Memuat…</div>');

                $.get(LIST_URL, {
                        page,
                        per_page: perPage,
                        search
                    })
                    .done(res => {
                        const list = res.data || [];
                        const noImg = "{{ asset('assets_admin/img/noimage.jpg') }}";

                        if (!list.length) {
                            return $container.html(
                                '<div class="w-100 text-center py-4">Belum ada artikel.</div>');
                        }

                        // helper escape untuk hindari XSS
                        const esc = s => $('<div/>').text(s ?? '').html();

                        let html = '';
                        list.forEach((a, i) => {
                            // sembunyikan non-aktif
                            if (a.status_artikel === 'Tidak Aktif') return;

                            const imgs = (a.photos && a.photos.length) ? a.photos : [noImg];
                            const slug = `/artikel/${a.slug}`;

                            const needFix = a.status_artikel === 'Perlu Diperbaiki';

                            // Badge status (muncul hanya jika Perlu Diperbaiki)
                            const statusBadge = needFix ?
                                `<span class="badge bg-warning text-dark"
                       style="position:absolute;top:.5rem;left:.5rem;z-index:2">
                   <i class="fas fa-wrench me-1"></i> Perlu Diperbaiki
                 </span>` :
                                '';

                            // Box feedback admin (muncul hanya jika ada feedback & status perlu diperbaiki)
                            const feedbackBox = (needFix && a.feedback) ?
                                `<div class="border rounded p-2 bg-warning bg-opacity-10 text-dark small mt-2">
                   <div class="fw-semibold mb-1">
                     <i class="fas fa-comment-dots me-1"></i> Feedback Admin
                   </div>
                   <div>${esc(a.feedback)}</div>
                 </div>` :
                                '';

                            html += `
            <article class="art-card wow fadeIn" style="position:relative" data-wow-delay="${(i + 1) * .1}s">
              ${statusBadge}

              <!-- GALERI -->
              <div id="c${a.id}" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                  ${imgs.map((x, j) =>
                    `<div class="carousel-item ${!j ? 'active' : ''}">
                                       <img src="${x}" class="d-block w-100" alt="Foto artikel">
                                     </div>`).join('')}
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#c${a.id}" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#c${a.id}" data-bs-slide="next">
                  <span class="carousel-control-next-icon"></span>
                </button>
              </div>

              <!-- INFO + TOMBOL -->
              <div class="p-3 d-flex flex-column h-100">
                <a href="${slug}" class="title text-dark text-decoration-none mb-2">${esc(a.title)}</a>

                ${feedbackBox}

                <div class="d-flex justify-content-between align-items-center mt-auto">
                  <span class="meta">${esc(a.author) || 'Anon'} · ${formatTgl(a.tanggal)}</span>
                  <button class="btn btn-sm btn-warning edit-btn" data-id="${a.id}">
                    <i class="fas fa-edit"></i>
                  </button>
                </div>
              </div>
            </article>`;
                        });

                        $container.html(html);
                        renderPag(res.pagination);
                    })
                    .fail(() => {
                        $container.html('<div class="w-100 text-center text-danger py-4">Gagal memuat.</div>');
                    });
            }


            /* — Pagination UI — */
            function renderPag(p) {
                if (!p) return $('#pagination').html('');
                let h = '';
                const btn = (pg, l, a) =>
                    `<li class="page-item ${a?'active':''}"><a class="page-link" href="#" data-p="${pg}">${l}</a></li>`;
                if (p.current_page > 1) h += btn(p.current_page - 1, '«');
                let s = Math.max(1, p.current_page - 2),
                    e = Math.min(p.last_page, s + 4);
                for (let i = s; i <= e; i++) h += btn(i, i, i === p.current_page);
                if (p.current_page < p.last_page) h += btn(p.current_page + 1, '»');
                $('#pagination').html(h);
            }
            $(document).on('click', '#pagination a', e => {
                e.preventDefault();
                page = $(e.target).data('p');
                loadArtikel(page, $('#search').val());
            });

            /* — First load — */
            loadArtikel();

        });
    </script>

    <script>
        (() => {
            const SHOW_URL = id => `/artikel-external/${id}`;
            const EDIT_URL = id => `/artikel-external/${id}/edit`;
            const UPDATE_URL = id => `/artikel-external/${id}`;
            const CAT_URL = "{{ route('lp.article.external.kategori') }}";

            let qlEdit;

            // ===== Utils =====
            function esc(s) {
                return $('<div/>').text(s ?? '').html();
            }

            function badgeClass(status) {
                if (status === 'Perlu Diperbaiki') return 'bg-warning text-dark';
                if (status === 'Aktif') return 'bg-success';
                if (status === 'Tidak Aktif') return 'bg-secondary';
                return 'bg-light text-dark';
            }

            function loadEditKategori(selectedId) {
                $('#edit-kategori_article_id').html('<option value="">Memuat…</option>');
                $.get(CAT_URL)
                    .done(r => {
                        let opt = '<option value="">Pilih Kategori</option>';
                        (r.data || []).forEach(k => {
                            const sel = Number(selectedId) === Number(k.id) ? 'selected' : '';
                            opt += `<option value="${k.id}" ${sel}>${esc(k.name_kategori)}</option>`;
                        });
                        $('#edit-kategori_article_id').html(opt);
                    })
                    .fail(() => $('#edit-kategori_article_id').html('<option value="">Gagal memuat</option>'));
            }

            function initQuillEdit() {
                if (qlEdit) return;
                $('#edit-konten').after('<div id="edit-konten-editor" style="height:200px"></div>').hide();
                qlEdit = new Quill('#edit-konten-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            ['link'],
                            [{
                                list: 'bullet'
                            }],
                            ['clean']
                        ]
                    }
                });
            }

            function renderEditTags(tags = []) {
                let html = '';
                tags.forEach((t, i) => html += `
      <div class="row g-2 mb-2">
        <div class="col-md-6"><input class="form-control" name="tags[${i}][nama]" value="${esc(t.nama)}" placeholder="Nama Tag"></div>
        <div class="col-md-5"><input class="form-control" name="tags[${i}][link]" value="${esc(t.link)}" placeholder="Link"></div>
        <div class="col-md-1"></div>
      </div>`);
                $('#edit-tags-container').html(html);
            }

            // ===== OPEN EDIT MODAL =====
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                $.get(EDIT_URL(id))
                    .done(r => {
                        $('#edit-id').val(r.id);
                        $('#edit-judul-input').val(r.title);
                        $('#edit-author-input').val(r.author);
                        $('#edit-tanggal-input').val(r.tanggal);

                        initQuillEdit();
                        qlEdit.root.innerHTML = r.content;

                        // status + feedback (readonly)
                        if (r.status_artikel) {
                            $('#edit-status-row').removeClass('d-none');
                            $('#edit-status-badge')
                                .attr('class', 'badge ' + badgeClass(r.status_artikel))
                                .text(r.status_artikel);
                        } else {
                            $('#edit-status-row').addClass('d-none');
                        }
                        if (r.feedback) {
                            $('#edit-feedback-row').removeClass('d-none').html(
                                `<i class="fas fa-comment-dots me-1"></i>${esc(r.feedback)}`);
                        } else {
                            $('#edit-feedback-row').addClass('d-none').html('');
                        }

                        loadEditKategori(r.kategori);
                        renderEditTags(r.tags || []);

                        $('#editArticleModal').modal('show');
                    })
                    .fail(() => Swal.fire('Error', 'Data tidak ditemukan', 'error'));
            });

            // ===== SUBMIT EDIT =====
            $('#edit-article-form').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit-id').val();
                $('#edit-konten').val(qlEdit.root.innerHTML.trim());

                $.ajax({
                    url: UPDATE_URL(id),
                    method: 'POST',
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: () => {
                        Swal.fire('Berhasil', 'Artikel diperbarui', 'success');
                        $('#editArticleModal').modal('hide');
                        window.loadArtikel && loadArtikel(); // refresh list
                    },
                    error: x => {
                        const msg = (x.status === 422) ?
                            Object.values(x.responseJSON.errors)[0][0] :
                            'Update gagal';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // ===== OPEN DETAIL MODAL =====
            // Tambahkan tombol "Detail" di kartu (atau pakai title link) dengan class .detail-btn data-id
            $(document).on('click', '.detail-btn, .open-detail', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                $.get(SHOW_URL(id))
                    .done(r => {
                        $('#dt-title').text(r.title || 'Detail Artikel');
                        $('#dt-author').text(r.author || 'Anon');
                        $('#dt-date').text(r.tanggal || '-');
                        $('#dt-kategori').text(r.kategori || '-');

                        $('#dt-status')
                            .attr('class', 'badge ' + badgeClass(r.status_artikel))
                            .text(r.status_artikel || '-');

                        if (r.feedback) {
                            $('#dt-feedback').removeClass('d-none').html(
                                `<i class="fas fa-comment-dots me-1"></i>${esc(r.feedback)}`);
                        } else {
                            $('#dt-feedback').addClass('d-none').html('');
                        }

                        // Photos
                        const photos = r.photos || [];
                        let phtml = '';
                        photos.forEach(p => phtml +=
                            `<img src="${p.url}" alt="foto" class="img-thumbnail me-2 mb-2" style="max-height:120px">`
                            );
                        $('#dt-photos').html(phtml);

                        // Tags
                        const tags = r.tags || [];
                        $('#dt-tags').html(tags.length ?
                            tags.map(t =>
                                `<a href="${esc(t.link)}" target="_blank" class="badge bg-light text-dark me-1">${esc(t.nama_tags ?? t.nama)}</a>`
                                ).join('') :
                            '');

                        // Content preview
                        $('#dt-content').html(r.content || '');

                        $('#detailArticleModal').modal('show');
                    })
                    .fail(() => Swal.fire('Error', 'Gagal memuat detail', 'error'));
            });
        })();
    </script>
@endsection
