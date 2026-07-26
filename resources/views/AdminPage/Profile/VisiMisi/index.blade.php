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
                                <h4 class="card-title">Data Visi & Misi</h4>
                                <button class="btn btn-primary btn-round ms-auto" data-toggle="modal"
                                    data-target="#createVisiMisiModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="visimisi-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Visi</th>
                                            <th>Misi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($visimisi as $item)
                                            <tr>
                                                <td>{!! Str::limit($item->visi, 100) !!}</td>
                                                <td>{!! Str::limit($item->misi, 100) !!}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $item->status_visimisi === 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $item->status_visimisi }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editVisiMisiModal{{ $item->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteVisiMisiModal{{ $item->id }}">
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
                                            <div class="modal fade" id="editVisiMisiModal{{ $item->id }}" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('profil.visimisiEdit', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Visi & Misi</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Visi</label>
                                                                    <textarea id="editVisi" name="visi" class="form-control ckeditor" required>{{ $item->visi }}</textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Misi</label>
                                                                    <textarea id="editMisi" name="misi" class="form-control ckeditor" required>{{ $item->misi }}</textarea>
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
                                                        <form action="{{ route('profil.visimisiToggleStatus', $item->id) }}"
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
                                                                    <select name="status_visimisi" class="form-control"
                                                                        required>
                                                                        <option value="1"
                                                                            {{ $item->status_visimisi == '1' ? 'selected' : '' }}>
                                                                            Aktif</option>
                                                                        <option value="2"
                                                                            {{ $item->status_visimisi == '2' ? 'selected' : '' }}>
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
                                            <div class="modal fade" id="deleteVisiMisiModal{{ $item->id }}"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('profil.visimisiDelete', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus Visi & Misi</h5>
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
    <div class="modal fade" id="createVisiMisiModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('profil.visimisiCreate') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Visi & Misi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Visi</label>
                            <textarea name="visi" class="form-control ckeditor" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Misi</label>
                            <textarea name="misi" class="form-control ckeditor" required></textarea>
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
            $('#visimisi-table').DataTable();

            // Inisialisasi CKEditor hanya sekali
            CKEDITOR.replace('editVisi');
            CKEDITOR.replace('editMisi');

            // Fungsi untuk menampilkan data ke modal edit saat tombol diklik
            $('.edit-button').click(function() {
                var visimisiId = $(this).data('id');
                var visi = $(this).data('visi');
                var misi = $(this).data('misi');
                var url = "{{ route('profil.visimisiEdit', ':id') }}".replace(':id', visimisiId);

                $('#editVisiMisiForm').attr('action', url);
                CKEDITOR.instances['editVisi'].setData(visi);
                CKEDITOR.instances['editMisi'].setData(misi);

                $('#editVisiMisiModal').modal('show');
            });
        });
    </script>
@endsection

