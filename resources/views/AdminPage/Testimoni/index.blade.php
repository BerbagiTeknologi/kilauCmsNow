@extends('AdminPage.App.master')

@php
    // Fungsi untuk mendapatkan embed URL dari URL YouTube
    function getEmbedURL($url) {
        // Menghapus parameter query (seperti ?si=...) jika ada
        $url = preg_replace('/\?[^\/]+$/', '', $url);

        // Cek apakah URL dalam format 'youtu.be', 'youtube.com', atau 'youtube.com/shorts'
        $regExp = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:shorts\/([\w\-]{11})|(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/)([\w\-]{11}))|youtu\.be\/([\w\-]{11}))/';
        preg_match($regExp, $url, $match);

        if (!empty($match[1])) {
            // Jika URL adalah Shorts
            return 'https://www.youtube.com/embed/' . $match[1]; // Return embed URL untuk Shorts
        } elseif (!empty($match[2])) {
            // Jika URL adalah video standar YouTube
            return 'https://www.youtube.com/embed/' . $match[2]; // Return embed URL untuk video standar
        } elseif (!empty($match[3])) {
            // Jika URL menggunakan format 'youtu.be'
            return 'https://www.youtube.com/embed/' . $match[3]; // Return embed URL untuk video standar
        }

        return null; // Jika URL tidak valid
    }

    // Fungsi untuk mengekstrak ID video dari URL standar YouTube
    function extractVideoId($url) {
        $url = preg_replace('/\?[^\/]+$/', '', $url);  // Menghapus query parameter

        // Mengambil ID video dari URL standar YouTube atau YouTube Shorts
        $pattern = '/(?:https?:\/\/)?(?:www\.)?youtube\.com\/(?:shorts\/([\w\-]{11})|(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/)([\w\-]{11}))|youtu\.be\/([\w\-]{11})/';
        preg_match($pattern, $url, $matches);

        return $matches[1] ?? $matches[2] ?? $matches[3] ?? null;
    }
@endphp


@section('style')
    <style>
        #editor-container {
            min-height: 150px;
            max-height: 500px;
            overflow-y: auto;
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
                                <h4 class="card-title">Data Testimoni</h4>
                                <button class="btn btn-primary btn-round ms-auto" data-toggle="modal"
                                    data-target="#createTestimoniModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="testimoni-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Pekerjaan</th>
                                            <th>Komentar</th>
                                            <th>File</th>
                                            <th>Video</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($testimonis as $testimoni)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $testimoni->nama }}</td>
                                                <td>{{ $testimoni->pekerjaan }}</td>
                                                <td>{!! $testimoni->komentar !!}</td>
                                                <td>
                                                    @if ($testimoni->file)
                                                        <img src="{{ Storage::url($testimoni->file) }}"
                                                            alt="Testimoni Image" style="width: 100px; height: auto;">
                                                    @else
                                                        No Image
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($testimoni->video_link)
                                                        @php
                                                            $embedURL = getEmbedURL($testimoni->video_link);
                                                        @endphp
                                                        @if ($embedURL)
                                                            <!-- Jika sudah dalam format embed -->
                                                            <iframe width="300" height="250" src="{{ $embedURL }}" frameborder="0"
                                                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                        @else
                                                            <!-- Jika hanya URL YouTube biasa -->
                                                            <iframe width="300" height="250" src="https://www.youtube.com/embed/{{ extractVideoId($testimoni->video_link) }}" frameborder="0"
                                                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                        @endif
                                                    @else
                                                        No Video
                                                    @endif

                                                    
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $testimoni->statuss_testimoni == 1 ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $testimoni->statuss_testimoni == 1 ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group" aria-label="Basic example">
                                                        <!-- Tombol Edit -->
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editTestimoniModal{{ $testimoni->id }}"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <!-- Tombol Delete -->
                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteTestimoniModal{{ $testimoni->id }}"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>

                                                        <!-- Tombol Ubah Status -->
                                                        <button class="btn btn-info btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#statusModal{{ $testimoni->id }}"
                                                            title="Ubah Status">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Status -->
                                            <div class="modal fade" id="statusModal{{ $testimoni->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="statusModalLabel{{ $testimoni->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('testimoniToggleStatus', $testimoni->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="statusModalLabel{{ $testimoni->id }}">
                                                                    Ubah Status Testimoni
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Pilih status untuk testimoni:
                                                                    <strong>{{ $testimoni->nama }}</strong>.
                                                                </p>
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="statuss_testimoni" class="form-control">
                                                                        <option value="1"
                                                                            {{ $testimoni->statuss_testimoni == 1 ? 'selected' : '' }}>
                                                                            Aktif
                                                                        </option>
                                                                        <option value="2"
                                                                            {{ $testimoni->statuss_testimoni == 2 ? 'selected' : '' }}>
                                                                            Tidak Aktif
                                                                        </option>
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

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editTestimoniModal{{ $testimoni->id }}"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="editTestimoniModalLabel{{ $testimoni->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('testimoniEdit', $testimoni->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editTestimoniModalLabel{{ $testimoni->id }}">Edit
                                                                    Testimoni</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Nama</label>
                                                                    <input type="text" name="nama"
                                                                        class="form-control" value="{{ $testimoni->nama }}"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Pekerjaan</label>
                                                                    <input type="text" name="pekerjaan"
                                                                        class="form-control"
                                                                        value="{{ $testimoni->pekerjaan }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Komentar</label>
                                                                    <textarea name="komentar" class="form-control ckeditor" required>{{ $testimoni->komentar }}</textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>File</label>
                                                                    <input type="file" name="file"
                                                                        class="form-control">
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Link YouTube Video</label>
                                                                    <input type="url"
                                                                        id="edit-youtube-url{{ $testimoni->id }}"
                                                                        name="video_link" class="form-control"
                                                                        value="{{ $testimoni->video_link }}"
                                                                        placeholder="Masukkan URL video YouTube">
                                                                </div>

                                                                <!-- Preview YouTube Video -->
                                                                <div class="form-group"
                                                                    id="edit-video-preview{{ $testimoni->id }}"
                                                                    style="display: {{ $testimoni->video_link ? 'block' : 'none' }};">
                                                                    <label>Preview Video</label>
                                                                    <iframe id="edit-video-iframe{{ $testimoni->id }}"
                                                                        class="embed-responsive-item" width="100%"
                                                                        height="315" frameborder="0"
                                                                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                                                        allowfullscreen></iframe>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save
                                                                    changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteTestimoniModal{{ $testimoni->id }}"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="deleteTestimoniModalLabel{{ $testimoni->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('testimoniDelete', $testimoni->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteTestimoniModalLabel{{ $testimoni->id }}">
                                                                    Delete Testimoni</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menghapus testimoni ini
                                                                    <strong>{{ $testimoni->nama }}</strong>?
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit"
                                                                    class="btn btn-danger">Delete</button>
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

    <!-- Create Modal -->
    <div class="modal fade" id="createTestimoniModal" tabindex="-1" role="dialog"
        aria-labelledby="createTestimoniModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('testimoniCreate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createTestimoniModalLabel">Create Testimoni</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Komentar</label>
                            <textarea name="komentar" class="form-control ckeditor" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>File</label>
                            <input type="file" name="file" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Link YouTube Video</label>
                            <input type="url" id="youtube-url" name="video_link" class="form-control"
                                placeholder="Masukkan URL video YouTube">
                        </div>

                        <!-- Preview YouTube Video -->
                        <div class="form-group" id="video-preview" style="display: none;">
                            <label>Preview Video</label>
                            <iframe id="video-iframe" class="embed-responsive-item" width="100%" height="315"
                                frameborder="0"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
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
            // Fungsi untuk menangani input video URL di modal create
            $('#youtube-url').on('input', function() {
                var videoURL = $(this).val();
                var iframeURL = getEmbedURL(videoURL); // Mendapatkan format embed URL

                if (iframeURL) {
                    $('#video-iframe').attr('src', iframeURL); // Update iframe dengan URL embed
                    $('#video-preview').show(); // Tampilkan preview iframe
                } else {
                    $('#video-preview').hide(); // Sembunyikan preview jika URL tidak valid
                }
            });

            $('[id^="edit-youtube-url"]').on('input', function() {
                var testimoniId = $(this).attr('id').replace('edit-youtube-url', ''); // Ambil ID testimoni
                var videoURL = $(this).val();
                var iframeURL = getEmbedURL(videoURL); // Mendapatkan format embed URL

                if (iframeURL) {
                    $('#edit-video-iframe' + testimoniId).attr('src',
                        iframeURL); // Update iframe dengan URL embed
                    $('#edit-video-preview' + testimoniId).show(); // Tampilkan preview iframe
                } else {
                    $('#edit-video-preview' + testimoniId)
                        .hide(); // Sembunyikan preview jika URL tidak valid
                }
            });

            // Fungsi untuk mendapatkan format embed URL dari URL YouTube
            function getEmbedURL(url) {
                // Menghapus parameter query (seperti ?si=...) jika ada
                url = url.replace(/\?[^\/]+$/, ''); // Menghapus bagian query setelah tanda ?

                // Cek apakah URL dalam format 'youtu.be' atau 'youtube.com'
                var regExp =
                    /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/)([\w\-]{11})|youtu\.be\/([\w\-]{11})|youtube\.com\/shorts\/([\w\-]{11}))/;
                var match = url.match(regExp);
                if (match) {
                    var videoID = match[1] || match[2] || match[3]; // Menangani video ID dari berbagai format URL
                    return 'https://www.youtube.com/embed/' + videoID; // Return embed URL untuk video biasa dan Shorts
                }
                return null; // Jika URL tidak valid
            }

            // Menangani form submit untuk memastikan video link sudah dalam format embed
            $('form').submit(function() {
                var videoLink;
                if ($(this).find('#youtube-url').length) {
                    videoLink = $('#youtube-url').val(); // Modal Create
                } else {
                    videoLink = $(this).find('[id^="edit-youtube-url"]').val(); // Modal Edit
                }
                var embedURL = getEmbedURL(videoLink); // Mendapatkan embed URL
                if (embedURL) {
                    $(this).find('[id^="youtube-url"]').val(
                    embedURL); // Update input dengan URL embed sebelum submit
                }
            });

            // Menampilkan video preview saat modal edit dibuka
            @foreach ($testimonis as $testimoni)
                var testimoniId = '{{ $testimoni->id }}';
                var videoURL = $('#edit-youtube-url' + testimoniId).val(); // Ambil URL video yang ada
                var iframeURL = getEmbedURL(videoURL); // Dapatkan embed URL

                if (iframeURL) {
                    $('#edit-video-iframe' + testimoniId).attr('src', iframeURL); // Set embed URL ke iframe
                    $('#edit-video-preview' + testimoniId).show(); // Tampilkan preview
                } else {
                    $('#edit-video-preview' + testimoniId).hide(); // Sembunyikan preview jika URL tidak valid
                }
            @endforeach
        });

        $(document).ready(function() {
            $('#testimoni-table').DataTable();

            // Fungsi untuk inisialisasi CKEditor pada elemen tertentu
            function initCKEditor(selector) {
                $(selector).each(function() {
                    const editorId = $(this).attr('id');

                    // Jika CKEditor sudah diinisialisasi pada elemen, hapus terlebih dahulu
                    if (CKEDITOR.instances[editorId]) {
                        CKEDITOR.instances[editorId].destroy(true);
                    }

                    // Inisialisasi CKEditor pada elemen
                    CKEDITOR.replace(editorId, {
                        toolbar: [{
                                name: 'basicstyles',
                                items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript',
                                    'Superscript', '-', 'RemoveFormat'
                                ]
                            },
                            {
                                name: 'paragraph',
                                items: ['NumberedList', 'BulletedList', '-', 'Outdent',
                                    'Indent', '-', 'Blockquote', '-', 'JustifyLeft',
                                    'JustifyCenter', 'JustifyRight', 'JustifyBlock'
                                ]
                            },
                            {
                                name: 'links',
                                items: ['Link', 'Unlink']
                            },
                            {
                                name: 'insert',
                                items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar']
                            },
                            {
                                name: 'styles',
                                items: ['Styles', 'Format', 'Font', 'FontSize']
                            },
                            {
                                name: 'colors',
                                items: ['TextColor', 'BGColor']
                            },
                            {
                                name: 'tools',
                                items: ['Maximize', 'ShowBlocks']
                            },
                            {
                                name: 'document',
                                items: ['Source']
                            }
                        ],
                        height: 200,
                        removePlugins: 'notification' // Menghilangkan peringatan
                    });
                });
            }

            // Inisialisasi CKEditor untuk semua textarea dengan class 'ckeditor'
            initCKEditor('.ckeditor');

            // Inisialisasi ulang saat modal dibuka (untuk modal edit)
            $('.modal').on('shown.bs.modal', function() {
                initCKEditor('.ckeditor');
            });
        });
    </script>
@endsection
