@extends('AdminPage.App.master')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Pengajuan Kerjasama</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="colaborasi-table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul Program</th>
                                            <th>Kategori Mitra</th>
                                            <th>Nama Mitra</th>
                                            <th>Email</th>
                                            <th>Balasan</th>
                                            <th>Status Progress</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($colaborasi as $data)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $data->program->judul }}</td>
                                                {{-- <td>{{ $data->jenis_kerjasama }}</td> --}}
                                                <td>{{ $data->kategori_mitra }}</td>
                                                <td>{{ $data->nama_lengkap }}</td>
                                                <td>{{ $data->alamat_email }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $data->status_progress_kerjasama == 'Pending' ? 'badge-warning' : 'badge-success' }}">
                                                        {{ $data->status_progress_kerjasama }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $data->status_kerjasama === 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $data->status_kerjasama }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <button class="btn btn-secondary btn-sm rounded-circle p-2"
                                                            data-toggle="modal" data-target="#showModal{{ $data->id }}"
                                                            title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </button>

                                                        <!-- Tombol Kirim Balasan -->
                                                        <button class="btn btn-primary btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#balasanModal{{ $data->id }}"
                                                            title="Kirim Balasan">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>

                                                        <!-- Tombol Ubah Status -->
                                                        <button class="btn btn-info btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#statusModal{{ $data->id }}"
                                                            title="Ubah Status">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>

                                                        <!-- Tombol Hapus -->
                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#hapusModal{{ $data->id }}" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                            </tr>

                                            <!-- Modal Kirim Balasan -->
                                            <div class="modal fade" id="balasanModal{{ $data->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('colaborasi.update', $data->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Kirim Balasan ke
                                                                    {{ $data->nama_lengkap }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Balasan</label>
                                                                    <textarea name="balasan" class="form-control" rows="4" required>{{ old('balasan') }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Batal</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Kirim</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Ubah Status -->
                                            <div class="modal fade" id="statusModal{{ $data->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('colaborasi.toggleStatus', $data->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Ubah Status Kerjasama</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <label>Status</label>
                                                                <select name="status_kerjasama" class="form-control">
                                                                    <option value="1"
                                                                        {{ $data->status_kerjasama == 1 ? 'selected' : '' }}>
                                                                        Aktif</option>
                                                                    <option value="2"
                                                                        {{ $data->status_kerjasama == 2 ? 'selected' : '' }}>
                                                                        Tidak Aktif</option>
                                                                </select>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Batal</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Hapus -->
                                            <div class="modal fade" id="hapusModal{{ $data->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('colaborasi.delete', $data->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menghapus data kolaborasi
                                                                    <strong>{{ $data->nama_lengkap }}</strong>?
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger">Hapus</button>
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

    @foreach ($colaborasi as $data)
        <div class="modal fade" id="showModal{{ $data->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Data Pengajuan Kerjasama</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td>{{ $data->id }}</td>
                            </tr>
                            <tr>
                                <th>Judul Program</th>
                                <td>{{ $data->program->judul }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Kerjasama</th>
                                <td>{{ $data->jenis_kerjasama }}</td>
                            </tr>
                            <tr>
                                <th>Kategori Mitra</th>
                                <td>{{ $data->kategori_mitra }}</td>
                            </tr>
                            @if ($data->kategori_mitra === 'Perusahaan' || $data->kategori_mitra === 'Instansi/Lembaga/Komunitas')
                                <tr>
                                    <th>Nama Perusahaan/Instansi</th>
                                    <td>{{ $data->nama_perusahaan ?? 'Tidak Diisi' }}</td>
                                </tr>
                                <tr>
                                    <th>Jabatan</th>
                                    <td>{{ $data->jabatan ?? 'Tidak Diisi' }}</td>
                                </tr>
                                <tr>
                                    <th>No. HP Perusahaan/Instansi</th>
                                    <td>{{ $data->nomor_hp_organisasi ?? 'Tidak Diisi' }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Nama Mitra</th>
                                <td>{{ $data->nama_lengkap }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $data->alamat_email }}</td>
                            </tr>
                            <tr>
                                <th>No. HP</th>
                                <td>{{ $data->nomor_hp }}</td>
                            </tr>
                            <tr>
                                <th>Deskripsi Pengajuan</th>
                                <td>{{ $data->deskripsi_pengajuan_kerjasama ?? 'Tidak Ada Deskripsi' }}</td>
                            </tr>
                            <tr>
                                <th>Balasan</th>
                                <td>{{ $data->balasan ? $data->balasan : 'Belum ada balasan' }}</td>
                            </tr>
                            <tr>
                                <th>Status Progress</th>
                                <td>
                                    <span
                                        class="badge {{ $data->status_progress_kerjasama === 'Pending' ? 'badge-warning' : 'badge-success' }}">
                                        {{ $data->status_progress_kerjasama }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Status Kerjasama</th>
                                <td>
                                    <span
                                        class="badge {{ $data->status_kerjasama === 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $data->status_kerjasama }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td>{{ $data->created_at }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Diperbarui</th>
                                <td>{{ $data->updated_at }}</td>
                            </tr>
                            @if ($data->npwp_file)
                                <tr>
                                    <th>Dokumen NPWP</th>
                                    <td>
                                        <a href="{{ Storage::url($data->npwp_file) }}" target="_blank">Lihat Data NPWP</a>
                                    </td>
                                </tr>
                            @endif
                            @if ($data->foto_orang_npwp)
                                <tr>
                                    <th>Selfie dengan NPWP</th>
                                    <td>
                                        <a href="{{ Storage::url($data->foto_orang_npwp) }}" target="_blank">Lihat Data Orang NPWP</a>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#colaborasi-table').DataTable();
        });
    </script>
@endsection
