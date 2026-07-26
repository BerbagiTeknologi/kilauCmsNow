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
                                <h4 class="card-title">Data Donasi Iklan</h4>
                                <button class="btn btn-primary btn-round ms-auto" id="tambahDataBtn" data-toggle="modal"
                                    data-target="#createStrukturModal">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="struktur-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama Iklan Donasi</th>
                                            <th>File Iklan</th>
                                            <th>Icon Donasi</th>
                                            <th>Nama Button Donasi</th>
                                            <th>Link</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($donasiiklan as $item)
                                            <tr>
                                                <td>{{ $item->nama }}</td>
                                                <td>
                                                    @if ($item->file)
                                                        <img src="{{ Storage::url($item->file) }}" alt="Struktur File"
                                                            style="width: 100px; height: auto;">
                                                    @else
                                                        No Image
                                                    @endif
                                                </td>
                                                <td>
                                                    <!-- Menampilkan ikon berdasarkan nama yang ada di database -->
                                                    @if ($item->icon_iklan)
                                                        <i class="fa {{ $item->icon_iklan }}"
                                                            style="font-size: 30px; color: #1363c6;"></i>
                                                    @else
                                                        No Icon
                                                    @endif
                                                </td>
                                                <td>{{ $item->name_button_iklan ?? "No Name" }}</td>
                                                <td>{{ $item->link }}</td>
                                                <td>
                                                    <span class="badge {{ $item->status_donasi_iklan === 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $item->status_donasi_iklan }}
                                                    </span>
                                                </td>  
                                                                                              
                                                <td>
                                                    <div class="btn-group gap-2" role="group">
                                                        <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#editStrukturModal{{ $item->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <button class="btn btn-danger btn-sm rounded-circle p-2"
                                                            data-toggle="modal"
                                                            data-target="#deleteStrukturModal{{ $item->id }}">
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
                                            <div class="modal fade" id="editStrukturModal{{ $item->id }}"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('profil.iklandonasiEdit', $item->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Donasi Iklan</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Nama Donasi</label>
                                                                    <input type="text" name="nama"
                                                                        class="form-control"
                                                                        value="{{ $item->nama }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="text-danger">File (Optional) (Hanya PNG yang diperbolehkan)</label>
                                                                    <input type="file" name="file" class="form-control file-input" accept="image/png,image/jpeg,image/jpg" data-preview="#file-preview-edit-{{ $item->id }}">
                                                                    
                                                                    @if ($item->file)
                                                                        <img id="file-preview-edit-{{ $item->id }}" src="{{ Storage::url($item->file) }}" alt="Preview" style="width: 100px; height: auto; display:block;">
                                                                    @else
                                                                        <img id="file-preview-edit-{{ $item->id }}" src="#" alt="Preview" style="width: 100px; height: auto; display:none;">
                                                                    @endif
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label>Nama Tombol (Opsional)</label>
                                                                    <input type="text" name="name_button_iklan" class="form-control" value="{{ $item->name_button_iklan }}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Link (Opsional)</label>
                                                                    <input type="text" name="link" class="form-control" value="{{ $item->link }}" placeholder="https://contoh.com">
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label>Pilih Ikon</label>
                                                                    <div id="icons-container-edit-{{ $item->id }}" class="d-flex flex-wrap gap-2">
                                                                        @php
                                                                            $icons = ['fa-home', 'fa-cog', 'fa-heart', 'fa-star', 'fa-book', 'fa-user', 'fa-flag', 'fa-rocket', 'fa-cloud', 'fa-check-circle', 'fa-envelope', 'fa-comment', 'fa-thumbs-up', 'fa-camera', 'fa-bell', 'fa-music', 'fa-paint-brush', 'fa-phone', 'fa-calendar', 'fa-laptop', 'fa-desktop', 'fa-sun', 'fa-moon', 'fa-snowflake', 'fa-shield-alt', 'fa-gift', 'fa-map', 'fa-users', 'fa-money-bill-wave', 'fa-puzzle-piece', 'fa-lightbulb'];
                                                                        @endphp
                                                                        @foreach($icons as $icon)
                                                                            <div class="icon-select-edit m-1 p-2 border" style="cursor:pointer;">
                                                                                <i class="fa {{ $icon }} fa-lg {{ $item->icon_iklan == $icon ? 'text-primary' : '' }}"></i>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <input type="hidden" id="selected-icon-value-edit-{{ $item->id }}" name="icon_iklan" value="{{ $item->icon_iklan }}">
                                                                    <div class="mt-2">Ikon dipilih: <i id="selected-icon-edit-{{ $item->id }}" class="fa {{ $item->icon_iklan }}"></i></div>
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
                                                            action="{{ route('profil.iklandonasiToggleStatus', $item->id) }}"
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
                                                                    <select name="statuskilauiklan" class="form-control"
                                                                        required>
                                                                        <option value="1"
                                                                            {{ $item->statuskilauiklan == 1 ? 'selected' : '' }}>
                                                                            Aktif</option>
                                                                        <option value="2"
                                                                            {{ $item->statuskilauiklan == 2 ? 'selected' : '' }}>
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
                                            <div class="modal fade" id="deleteStrukturModal{{ $item->id }}"
                                                tabindex="-1" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('profil.iklandonasiDelete', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus Struktur</h5>
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
    <div class="modal fade" id="createStrukturModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('profil.iklandonasiCreate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Donasi Iklan</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Donasi Iklan</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="text-danger">File (Optional) (Hanya PNG yang diperbolehkan)</label>
                            <input type="file" name="file" class="form-control file-input" accept="" data-preview="#file-preview-create">
                        </div>                        
                        <div class="form-group">
                            <img id="file-preview-create" src="#" alt="Preview" style="display:none; width: 100px; height: auto;">
                        </div>
                        <div class="form-group">
                            <label>Nama Tombol (Opsional)</label>
                            <input type="text" name="name_button_iklan" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Link (Opsional)</label>
                            <input type="text" name="link" class="form-control" placeholder="https://contoh.com">
                        </div>
                        
                        
                        <div class="form-group">
                            <label>Pilih Ikon</label>
                            <div id="icons-container-create" class="d-flex flex-wrap gap-2">
                                @php
                                    $icons = ['fa-home', 'fa-cog', 'fa-heart', 'fa-star', 'fa-book', 'fa-user', 'fa-flag', 'fa-rocket', 'fa-cloud', 'fa-check-circle', 'fa-envelope', 'fa-comment', 'fa-thumbs-up', 'fa-camera', 'fa-bell', 'fa-music', 'fa-paint-brush', 'fa-phone', 'fa-calendar', 'fa-laptop', 'fa-desktop', 'fa-sun', 'fa-moon', 'fa-snowflake', 'fa-shield-alt', 'fa-gift', 'fa-map', 'fa-users', 'fa-money-bill-wave', 'fa-puzzle-piece', 'fa-lightbulb'];
                                @endphp
                                @foreach($icons as $icon)
                                    <div class="icon-select-create m-1 p-2 border" style="cursor:pointer;">
                                        <i class="fa {{ $icon }} fa-lg"></i>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="selected-icon-value-create" name="icon_iklan">
                            <div class="mt-2">Ikon dipilih: <i id="selected-icon-create" class="fa"></i></div>
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
            $('#struktur-table').DataTable();

            // Fungsi Preview Gambar untuk Modal Create & Edit
            $('.file-input').on('change', function () {
                const file = this.files[0];
                const previewSelector = $(this).data('preview');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $(previewSelector).attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $(previewSelector).hide();
                }
            });


            // Event klik pada icon di modal create
            $('#icons-container-create .icon-select-create').on('click', function () {
                var selectedIcon = $(this).find('i').attr('class').split(' ')[1]; // Ambil nama icon
                
                // Tampilkan di preview
                $('#selected-icon-create').attr('class', 'fa ' + selectedIcon);
                $('#selected-icon-create').css('color', '#1363c6');

                // Simpan nilai icon ke input hidden
                $('#selected-icon-value-create').val(selectedIcon);
            });

             // EDIT: Klik icon per modal edit
            @foreach($donasiiklan as $item)
                $('#icons-container-edit-{{ $item->id }} .icon-select-edit').on('click', function () {
                    let selectedIcon = $(this).find('i').attr('class').split(' ')[1];
                    $('#selected-icon-edit-{{ $item->id }}').attr('class', 'fa ' + selectedIcon).css('color', '#1363c6');
                    $('#selected-icon-value-edit-{{ $item->id }}').val(selectedIcon);
                    $('#icons-container-edit-{{ $item->id }} .icon-select-edit i').removeClass('text-primary');
                    $(this).find('i').addClass('text-primary');
                });
            @endforeach
        });
    </script>
@endsection
