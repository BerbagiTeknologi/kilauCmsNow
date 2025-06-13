@extends('Auth.App.master')

@section('style')
    <style>
        /* Contoh styling, sesuaikan dengan kebutuhan */
        .login-page {
            display: flex;
            height: 100vh;
            width: 100vw;
            background-color: #f8f9fa;
            overflow: hidden;
        }
        .login-background {
            flex: 1.5;
            background-image: url('{{ asset('assets/img/loginpageke2.png') }}');
            background-size: cover;
            background-position: center;
            height: 100%;
            position: relative;
        }
        .login-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.95);
            height: 100vh;
        }
        .login-form .card {
            width: 100%;
            max-width: 700px;
            border-radius: 15px;
            padding: 3rem 2.5rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            background: #ffffff;
            position: relative;
            margin-top: 5rem;
        }
        .login-logo {
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            background-color: white;
            padding: 10px;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        .login-logo img {
            height: 100px;
            width: 100px;
        }
        .card h3 {
            font-weight: bold;
            font-size: 1.75rem;
            color: #333;
            margin-bottom: 2rem;
            text-align: center;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-control {
            padding: 0.75rem;
            border-radius: 8px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #1363c6;
        }
        .account-check {
            font-size: 0.95rem;
            text-align: center;
            margin-top: 1rem;
        }
        .account-check a {
            color: #1363c6;
            text-decoration: none;
            font-weight: bold;
        }
        .account-check a:hover {
            text-decoration: underline;
        }
        .login-footer {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2rem;
        }
        .login-footer p {
            margin-bottom: 0;
            font-size: 0.9rem;
            color: #777;
        }
        /* Styling untuk ukuran layar kecil */
        @media (max-width: 768px) {
            .login-background {
                display: none;
            }
            .login-page {
                justify-content: center;
            }
            .login-form {
                flex: 1;
                padding: 2rem;
                max-width: 100%;
            }
            .login-logo {
                top: -50px;
            }
            .login-logo img {
                height: 60px;
                width: 60px;
            }
        }
        @media (max-width: 576px) {
            .login-form .card {
                padding: 1.5rem;
                max-width: 100%;
            }
            .login-footer p {
                font-size: 0.8rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="login-page">
        <div class="login-background">
            <!-- Background image -->
        </div>
        <div class="login-form">
            <div class="card">
                <!-- Logo -->
                <div class="login-logo">
                    <img src="{{ asset('assets/img/LogoKilau2.png') }}" alt="Kilau Logo">
                </div>

                <h2 class="text-center mt-5" style="font-weight:500;">Signup</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Registrasi -->
                <form id="register-form" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama Lengkap" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Alamat Email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password" required>
                    </div>
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn" style="background-color: #1363c6; color:white;">Submit</button>
                    </div>
                </form>
                <!-- Pesan untuk pengguna yang sudah punya akun -->
                <p class="account-check">
                    Sudah punya akun? <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}">Sign In</a>.
                </p>
                <div class="login-footer mt-4">
                    <p>© 2025 Berbagi Teknologi. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Fungsi untuk mengambil query parameter (jika diperlukan)
        function getQueryParam(param) {
            var urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        $(document).ready(function() {
            $('#register-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: 'https://kilauindonesia.org/kilau/api/createUser',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Jika properti "user" ada, artinya pendaftaran berhasil
                        if (response.user) {
                            // Simpan token dan data user ke localStorage
                            localStorage.setItem('user_token', response.token);
                            localStorage.setItem('user_level', response.user.level);
                            localStorage.setItem('user_name', response.user.name);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Pendaftaran Berhasil',
                                text: response.message || 'Akun berhasil dibuat. Anda sekarang sudah dapat mengirim komentar.'
                            }).then(() => {
                                // window.location.href = "{{ route('home') }}";
                                window.location.href = "{{ route('login') }}?registered=1";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pendaftaran Gagal',
                                text: response.message || 'Terjadi kesalahan, silakan coba lagi.'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada server. Silakan coba lagi nanti.'
                        });
                    }
                });
            });
        });
    </script>
@endsection
