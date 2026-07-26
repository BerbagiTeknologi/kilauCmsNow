@extends('AdminPage.App.master')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Validasi gagal.</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">User Management</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="user-management-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Terdaftar</th>
                                            <th>Login Terakhir</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <span class="badge {{ $user->role === 'admin' ? 'badge-primary' : 'badge-secondary' }}">
                                                        {{ $user->role ?? 'user' }}
                                                    </span>
                                                </td>
                                                <td>{{ optional($user->created_at)->format('d M Y') ?? '-' }}</td>
                                                <td>{{ optional($user->last_login_at)->format('d M Y H:i') ?? '-' }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm rounded-circle p-2"
                                                        data-toggle="modal"
                                                        data-target="#resetPasswordModal{{ $user->id }}"
                                                        title="Reset Password">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="resetPasswordModalLabel{{ $user->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.users.password.update', $user->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="resetPasswordModalLabel{{ $user->id }}">
                                                                    Reset Password User
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="mb-3">
                                                                    User:
                                                                    <strong>{{ $user->name }}</strong>
                                                                    <br>
                                                                    <small>{{ $user->email }}</small>
                                                                </p>
                                                                <div class="form-group">
                                                                    <label>Password Baru</label>
                                                                    <input type="password" name="password"
                                                                        class="form-control" minlength="8" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Konfirmasi Password Baru</label>
                                                                    <input type="password" name="password_confirmation"
                                                                        class="form-control" minlength="8" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Simpan</button>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#user-management-table').DataTable();
        });
    </script>
@endsection
