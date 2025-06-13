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
                                    data-target="#createGaleriModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="galeriadmin-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul Kegiatan</th>
                                            <th>Nama Kantor Cabang</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                            <th>Foto Galeri</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($galeri as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->judul_kegiatan }}</td>
                                                <td>{{ $item->nama_kantor_cabang }}</td>
                                                <td>{{ $item->deskripsi_kegiatan }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $item->status_galeri == 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $item->status_galeri }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($item->file_galeri)
                                                        <div class="form-group">
                                                            <div>
                                                                @foreach ($item->file_galeri as $image)
                                                                    <img src="{{ asset('storage/' . $image) }}"
                                                                        alt="Foto Program" width="100" height="auto"
                                                                        style="margin-right: 10px;">
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>

                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <!-- Tombol Edit -->
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editGaleriModal{{ $item->id }}"
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

                                            <!-- Modal Edit Galeri -->
                                            <div class="modal fade" id="editGaleriModal{{ $item->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="editGaleriModalLabel{{ $item->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('galeryAdmin.edit', $item->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editGaleriModalLabel{{ $item->id }}">Edit
                                                                    Galeri</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Judul Kegiatan -->
                                                                <div class="form-group">
                                                                    <label>Judul Kegiatan</label>
                                                                    <input type="text" name="judul_kegiatan"
                                                                        class="form-control"
                                                                        value="{{ $item->judul_kegiatan }}" required>
                                                                </div>
                                                                <!-- Deskripsi Kegiatan -->
                                                                <div class="form-group">
                                                                    <label>Deskripsi Kegiatan</label>
                                                                    <textarea name="deskripsi_kegiatan" class="form-control" required>{{ $item->deskripsi_kegiatan }}</textarea>
                                                                </div>
                                                                <!-- Nama Kantor Cabang -->
                                                                <div class="form-group">
                                                                    <label>Nama Kantor Cabang</label>
                                                                    <select name="nama_kantor_cabang" class="form-control"
                                                                        required>
                                                                        <option
                                                                            value="{{ \App\Models\Galeri::KANTOR_CABANG_INDRAMAYU }}"
                                                                            {{ $item->nama_kantor_cabang == \App\Models\Galeri::KANTOR_CABANG_INDRAMAYU ? 'selected' : '' }}>
                                                                            Kantor Cabang Indramayu
                                                                        </option>
                                                                        <option
                                                                            value="{{ \App\Models\Galeri::KANTOR_CABANG_SUMEDANG }}"
                                                                            {{ $item->nama_kantor_cabang == \App\Models\Galeri::KANTOR_CABANG_SUMEDANG ? 'selected' : '' }}>
                                                                            Kantor Cabang Sumedang
                                                                        </option>
                                                                        <option
                                                                            value="{{ \App\Models\Galeri::KANTOR_CABANG_BANDUNG }}"
                                                                            {{ $item->nama_kantor_cabang == \App\Models\Galeri::KANTOR_CABANG_BANDUNG ? 'selected' : '' }}>
                                                                            Kantor Cabang Bandung
                                                                        </option>
                                                                        <option
                                                                            value="{{ \App\Models\Galeri::KANTOR_CABANG_MAJALENGKA }}"
                                                                            {{ $item->nama_kantor_cabang == \App\Models\Galeri::KANTOR_CABANG_MAJALENGKA ? 'selected' : '' }}>
                                                                            Kantor Cabang Majalengka
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <!-- Foto Galeri (Gambar Upload) -->
                                                                <div class="form-group">
                                                                    <label for="foto_image">Foto Program</label>
                                                                    <input type="file" name="file_galeri[]"
                                                                        class="form-control" id="foto_image" multiple>

                                                                    <!-- Preview Foto Program yang sudah ada -->
                                                                    @if ($item->file_galeri)
                                                                        <div class="form-group">
                                                                            <div id="imagePreview">
                                                                                @foreach ($item->file_galeri as $image)
                                                                                    <img src="{{ asset('storage/' . $image) }}"
                                                                                        alt="Foto Program" width="100"
                                                                                        height="auto"
                                                                                        style="margin-right: 10px;">
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    <!-- Preview gambar baru yang dipilih -->
                                                                    <div id="imagePreview" class="mt-2">
                                                                        <!-- Preview images will be shown here -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-primary">Simpan
                                                                    Perubahan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Modal Delete Galeri -->
                                            <div class="modal fade" id="deleteProgramModal{{ $item->id }}"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="deleteProgramModalLabel{{ $item->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('galeryAdmin.delete', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteProgramModalLabel{{ $item->id }}">Hapus
                                                                    Galeri</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Apakah Anda yakin ingin menghapus galeri ini?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Tutup</button>
                                                                <button type="submit"
                                                                    class="btn btn-danger">Hapus</button>
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
                                                        <form action="{{ route('galeryAdmin.toggleStatus', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="statusModalLabel{{ $item->id }}">Ubah Status
                                                                    Galeri</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Pilih status untuk galeri:
                                                                    <strong>{{ $item->judul_kegiatan }}</strong>.</p>
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="status_galeri" class="form-control">
                                                                        <option value="1"
                                                                            {{ $item->status_galeri == 1 ? 'selected' : '' }}>
                                                                            Aktif</option>
                                                                        <option value="2"
                                                                            {{ $item->status_galeri == 2 ? 'selected' : '' }}>
                                                                            Tidak Aktif</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-primary">Simpan
                                                                    Perubahan</button>
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

    <!-- Modal Tambah Galeri -->
    <div class="modal fade" id="createGaleriModal" tabindex="-1" role="dialog"
        aria-labelledby="createGaleriModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('galeryAdmin.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createGaleriModalLabel">Tambah Galeri</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="judul_kegiatan">Judul Kegiatan</label>
                            <input type="text" class="form-control" name="judul_kegiatan" required>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi_kegiatan">Deskripsi Kegiatan</label>
                            <textarea class="form-control" name="deskripsi_kegiatan" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="nama_kantor_cabang">Nama Kantor Cabang</label>
                            <select class="form-control" name="nama_kantor_cabang" required>
                                <option value="{{ \App\Models\Galeri::KANTOR_CABANG_INDRAMAYU }}">
                                    {{ \App\Models\Galeri::KANTOR_CABANG_INDRAMAYU }}
                                </option>
                                <option value="{{ \App\Models\Galeri::KANTOR_CABANG_SUMEDANG }}">
                                    {{ \App\Models\Galeri::KANTOR_CABANG_SUMEDANG }}
                                </option>
                                <option value="{{ \App\Models\Galeri::KANTOR_CABANG_BANDUNG }}">
                                    {{ \App\Models\Galeri::KANTOR_CABANG_BANDUNG }}
                                </option>
                                <option value="{{ \App\Models\Galeri::KANTOR_CABANG_MAJALENGKA }}">
                                    {{ \App\Models\Galeri::KANTOR_CABANG_MAJALENGKA }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="file_galeri">Upload Gambar</label>
                            <input type="file" class="form-control" name="file_galeri[]" id="foto_image" multiple>
                            <div id="imagePreview" class="mt-2">
                                <!-- Preview images will be shown here -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#galeriadmin-table').DataTable();

            // Menampilkan preview gambar saat file dipilih
            $('#foto_image').on('change', function() {
                var files = this.files;
                $('#imagePreview').html('');

                // Loop untuk menampilkan setiap gambar yang dipilih
                for (var i = 0; i < files.length; i++) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var imgPreview = '<img src="' + e.target.result +
                            '" class="img-thumbnail" width="100" style="margin-right: 10px;">';
                        $('#imagePreview').append(imgPreview);
                    };
                    reader.readAsDataURL(files[i]);
                }
            });
        });
    </script>
@endsection
