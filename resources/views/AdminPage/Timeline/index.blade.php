@extends('AdminPage.App.master')

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
                                <h4 class="card-title">Data Timeline</h4>
                                <button class="btn btn-primary btn-round ms-auto" data-toggle="modal"
                                    data-target="#createTimelineModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="timeline-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul Timeline</th>
                                            <th>Subjudul</th>
                                            <th>Status</th>
                                            <th>Icon</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($timeline as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->judul_timline }}</td>
                                                <td>{{ $item->subjudul_timline }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $item->status_timline == 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $item->status_timline }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <!-- Menampilkan ikon berdasarkan nama yang ada di database -->
                                                    @if ($item->icon_timeline)
                                                        <i class="fa {{ $item->icon_timeline }}"
                                                            style="font-size: 30px; color: #1363c6;"></i>
                                                    @else
                                                        No Icon
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <!-- Tombol Edit -->
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editTimelineModal{{ $item->id }}"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <!-- Tombol Delete -->
                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteTimelineModal{{ $item->id }}"
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

                                            <!-- Modal Toggle Status -->
                                            <div class="modal fade" id="statusModal{{ $item->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="statusModalLabel{{ $item->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('timeline.toggleStatus', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="statusModalLabel{{ $item->id }}">
                                                                    Ubah Status Timeline
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Pilih status untuk timeline:
                                                                    <strong>{{ $item->judul_timline }}</strong>.
                                                                </p>
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="status_timline" class="form-control">
                                                                        <option value="1"
                                                                            {{ $item->status_timline == 1 ? 'selected' : '' }}>
                                                                            Aktif</option>
                                                                        <option value="2"
                                                                            {{ $item->status_timline == 2 ? 'selected' : '' }}>
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
                                            <div class="modal fade" id="editTimelineModal{{ $item->id }}"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('timeline.edit', $item->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Timeline</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Judul Timeline</label>
                                                                    <input type="text" name="judul_timline"
                                                                        class="form-control"
                                                                        value="{{ $item->judul_timline }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Subjudul Timeline</label>
                                                                    <input type="text" name="subjudul_timline"
                                                                        class="form-control"
                                                                        value="{{ $item->subjudul_timline }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Deskripsi</label>
                                                                    <textarea name="deskripsi_timeline" class="form-control">{{ $item->deskripsi_timeline }}</textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Sequence</label>
                                                                    <input type="number" name="sequence_timeline"
                                                                        class="form-control"
                                                                        value="{{ $item->sequence_timeline }}">
                                                                </div>

                                                                <!-- Icon Timeline (Modal Edit) -->
                                                                <div class="form-group">
                                                                    <label>Icon Timeline</label>
                                                                    <div id="icons-container-edit" class="row">
                                                                        @foreach (['fa-home', 'fa-cog', 'fa-heart', 'fa-star', 'fa-book', 'fa-user', 'fa-flag', 'fa-rocket', 'fa-cloud', 'fa-check-circle', 'fa-envelope', 'fa-comment', 'fa-thumbs-up', 'fa-camera', 'fa-bell', 'fa-music', 'fa-paint-brush', 'fa-phone', 'fa-calendar', 'fa-laptop', 'fa-desktop', 'fa-sun', 'fa-moon', 'fa-snowflake', 'fa-shield-alt', 'fa-gift', 'fa-map', 'fa-users', 'fa-guitar', 'fa-puzzle-piece', 'fa-lightbulb'] as $icon)
                                                                            <div class="col-3 mb-2">
                                                                                <i class="fa {{ $icon }} icon-select-edit"
                                                                                    style="font-size: 30px; cursor: pointer; color: #1363c6;"></i>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <small class="text-muted">Pilih icon dari Font
                                                                        Awesome</small>
                                                                </div>

                                                                <!-- Icon Preview (Modal Edit) -->
                                                                <div class="form-group">
                                                                    <label>Preview Icon</label>
                                                                    <div id="icon-preview-edit" class="text-center">
                                                                        <i id="selected-icon-edit"
                                                                            class="fa {{ $item->icon_timeline ?? 'fa-home' }}"
                                                                            style="font-size: 40px; color: #1363c6;"></i>
                                                                        <input type="hidden" name="selected_icon"
                                                                            id="selected-icon-value-edit"
                                                                            value="{{ $item->icon_timeline ?? 'fa-home' }}">
                                                                    </div>
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
                                            <div class="modal fade" id="deleteTimelineModal{{ $item->id }}"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('timeline.delete', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus Timeline</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menghapus timeline
                                                                    <strong>{{ $item->judul_timline }}</strong>?
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

    <!-- Modal Create -->
    <div class="modal fade" id="createTimelineModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('timeline.create') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Timeline</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Judul Timeline</label>
                            <input type="text" name="judul_timline" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Subjudul Timeline</label>
                            <input type="text" name="subjudul_timline" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi_timeline" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Sequence</label>
                            <input type="number" name="sequence_timeline" class="form-control">
                        </div>

                        <!-- Icon Timeline (Modal Create) -->
                        <div class="form-group">
                            <label>Icon Timeline</label>
                            <div id="icons-container-create" class="row">
                                @foreach (['fa-home', 'fa-cog', 'fa-heart', 'fa-star', 'fa-book', 'fa-user', 'fa-flag', 'fa-rocket', 'fa-cloud', 'fa-check-circle', 'fa-envelope', 'fa-comment', 'fa-thumbs-up', 'fa-camera', 'fa-bell', 'fa-music', 'fa-paint-brush', 'fa-phone', 'fa-calendar', 'fa-laptop', 'fa-desktop', 'fa-sun', 'fa-moon', 'fa-snowflake', 'fa-shield-alt', 'fa-gift', 'fa-map', 'fa-users', 'fa-guitar', 'fa-puzzle-piece', 'fa-lightbulb'] as $icon)
                                    <div class="col-3 mb-2">
                                        <i class="fa {{ $icon }} icon-select-create"
                                            style="font-size: 30px; cursor: pointer; color: #1363c6;"></i>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Pilih icon dari Font Awesome</small>
                        </div>

                        <!-- Icon Preview (Modal Create) -->
                        <div class="form-group">
                            <label>Preview Icon</label>
                            <div id="icon-preview-create" class="text-center">
                                <i id="selected-icon-create" class="fa fa-home"
                                    style="font-size: 40px; color: #1363c6;"></i>
                                <input type="hidden" name="selected_icon" id="selected-icon-value-create">
                            </div>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#timeline-table').DataTable();

            $('#icons-container-create .icon-select-create').on('click', function() {
                var selectedIcon = $(this).attr('class').split(' ')[1]; // Ambil kelas ikon yang dipilih

                // Tampilkan ikon yang dipilih di preview
                $('#selected-icon-create').attr('class', 'fa ' + selectedIcon);
                $('#selected-icon-create').css('color', '#1363c6'); // Setel warna ikon preview
                $('#selected-icon-value-create').val(
                selectedIcon); // Masukkan nama ikon ke input tersembunyi
            });

            // Fungsi untuk mengubah ikon yang dipilih dan menampilkannya di preview (Modal Edit)
            $('#icons-container-edit .icon-select-edit').on('click', function() {
                var selectedIcon = $(this).attr('class').split(' ')[1]; // Ambil kelas ikon yang dipilih

                // Tampilkan ikon yang dipilih di preview
                $('#selected-icon-edit').attr('class', 'fa ' + selectedIcon);
                $('#selected-icon-edit').css('color', '#1363c6'); // Setel warna ikon preview
                $('#selected-icon-value-edit').val(selectedIcon); // Masukkan nama ikon ke input tersembunyi
            });

            // Mengatur default ikon di modal Edit jika ada
            if ($('#selected-icon-edit').length > 0 && $('#selected-icon-edit').attr('class') === 'fa ') {
                var defaultIcon = $('#selected-icon-value-edit').val();
                if (defaultIcon) {
                    $('#selected-icon-edit').attr('class', 'fa ' + defaultIcon);
                    $('#selected-icon-edit').css('color', '#1363c6');
                }
            }

            // Fungsi Preview Gambar
            $('.file-input').on('change', function() {
                const file = this.files[0];
                const previewSelector = $(this).data('preview');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewSelector).attr('src', e.target.result).css('display', 'block');
                    };
                    reader.readAsDataURL(file);
                } else {
                    $(previewSelector).attr('src', '#').css('display', 'none');
                }
            });
        });
    </script>
@endsection
