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
                                <h4 class="card-title">Data Tentang Kami</h4>
                                <button class="btn btn-primary btn-round ms-auto" id="tambahDataBtn" data-toggle="modal"
                                    data-target="#createTentangKamiModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tentangkami-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            {{-- <th>No</th> --}}
                                            <th>Judul</th>
                                            <th>Deskripsi</th>
                                            <th>File</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tentangKami as $item)
                                            <tr>
                                                {{-- <td>{{ $loop->iteration }}</td> --}}
                                                <td>{{ $item->judul_tentang_kami }}</td>
                                                <td>{{ Str::limit($item->deskripsi, 100) }}</td>
                                                <td>
                                                    @if ($item->file)
                                                        <img src="{{ Storage::url($item->file) }}" alt="Tentang Kami File"
                                                            style="width: 100px; height: auto;">
                                                    @else
                                                        No Image
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $item->status_tentang_kami === 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $item->status_tentang_kami }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editTentangKamiModal{{ $item->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteTentangKamiModal{{ $item->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>

                                                        <button class="btn btn-info btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#statusModal{{ $item->id }}">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Edit -->
                                            <div class="modal fade" id="editTentangKamiModal{{ $item->id }}"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('profil.tentangkamiEdit', $item->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Tentang Kami</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Judul</label>
                                                                    <input type="text" name="judul_tentang_kami"
                                                                        class="form-control"
                                                                        value="{{ $item->judul_tentang_kami }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Deskripsi</label>
                                                                    <textarea name="deskripsi" class="form-control" required>{{ $item->deskripsi }}</textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>File (Optional)</label>
                                                                    <input type="file" name="file"
                                                                        class="form-control">
                                                                    @if ($item->file)
                                                                        <p>Current File: <a
                                                                                href="{{ Storage::url($item->file) }}"
                                                                                target="_blank">{{ basename($item->file) }}</a>
                                                                        </p>
                                                                    @endif
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

                                            <!-- Modal Status -->
                                            <div class="modal fade" id="statusModal{{ $item->id }}" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form
                                                            action="{{ route('profil.tentangkamiToggleStatus', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Ubah Status</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="status_tentang_kami" class="form-control"
                                                                        required>
                                                                        <option value="1"
                                                                            {{ $item->status_tentang_kami == 1 ? 'selected' : '' }}>
                                                                            Aktif</option>
                                                                        <option value="2"
                                                                            {{ $item->status_tentang_kami == 2 ? 'selected' : '' }}>
                                                                            Tidak Aktif</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-primary">Ubah
                                                                    Status</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Delete -->
                                            <div class="modal fade" id="deleteTentangKamiModal{{ $item->id }}"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('profil.tentangkamiDelete', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus Tentang Kami</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menghapus data ini?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Batal</button>
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
    <div class="modal fade" id="createTentangKamiModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('profil.tentangkamiCreate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Tentang Kami</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" name="judul_tentang_kami" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>File (Optional)</label>
                            <input type="file" name="file" class="form-control file-input"
                                data-preview="#file-preview">
                        </div>
                        <div class="form-group">
                            <img id="file-preview" src="#" alt="File Preview"
                                style="display:none; width: 100px; height: auto;">
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
    <script>
        $(document).ready(function() {
            toggleTambahDataBtn();
            $('#tentangkami-table').DataTable();

            // Fungsi untuk mengecek apakah tombol tambah data perlu disembunyikan atau ditampilkan
            function toggleTambahDataBtn() {
                if ($('#tentangkami-table tbody tr').length > 0) {
                    $('#tambahDataBtn').hide(); // Sembunyikan tombol jika ada data
                } else {
                    $('#tambahDataBtn').show(); // Tampilkan tombol jika tidak ada data
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
