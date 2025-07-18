@extends('Auth.App.master')

@section('style')
    <style>
        /* Menyusun halaman login */
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
        .login-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .overlay-text {
            position: absolute;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            color: #ffffff;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            z-index: 1;
            padding: 0.5rem 1rem;
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            white-space: nowrap;
        }
        /* Styling form login */
        .login-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.95);
            height: 100vh;
        }
        /* Styling untuk card login */
        .login-form .card {
            width: 100%;
            max-width: 700px;
            height: auto;
            min-height: 80vh;
            border-radius: 15px;
            padding: 3rem 2.5rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            margin-top: 5rem;
        }
        /* Styling untuk logo */
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
        /* Font yang lebih tebal untuk judul */
        .card h3 {
            font-weight: bold;
            font-size: 1.75rem;
            color: #333;
            margin-bottom: 1rem;
            text-align: center;
        }
        /* Pesan pertanyaan ditempatkan di bawah form */
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
        /* Styling untuk form control dan input */
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
            {{-- <div class="overlay-text">
                KILAU INDONESIA LEMBAGA KEMANUSIAAN
            </div> --}}
        </div>
        <div class="login-form">
            <div class="card">
                <!-- Menambahkan logo di atas -->
                <div class="login-logo">
                    <img src="{{ asset('assets/img/LogoKilau2.png') }}" alt="Kilau Logo">
                </div>

                <h2 class="text-center mt-1" style="font-weight:500;">Sign In</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form untuk login -->
                <form id="login-form" method="POST">
                    @csrf
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

                <!-- Pesan pertanyaan apakah sudah punya akun ditempatkan di bawah form -->
                <p class="account-check">
                    Apakah Anda sudah mempunyai akun? Jika belum, silahkan <a href="{{ route('register') }}">Signup</a>.
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
        $('#login-form').submit(function(event) {
            event.preventDefault();  // Mencegah form submit secara default

            var email = $('#email').val();
            var password = $('#password').val();
            var csrfToken = $('input[name="_token"]').val();

            $.ajax({
                url: "{{ route('loginProses') }}",  // URL untuk controller login
                method: "POST",
                data: {
                    _token: csrfToken,
                    email: email,
                    password: password
                },
                success: function(res) {
                    if (res.redirect_url) {
                        // Simpan data ke localStorage agar navbar update
                        if (res.token) {
                            localStorage.setItem('user_token', res.token);
                        }
                        if (res.user && res.user.name) {
                            localStorage.setItem('user_name', res.user.name);
                        }
                        if (res.user && res.user.level) {
                            localStorage.setItem('user_level', res.user.level);
                        }
                        if (res.user && res.user.referral_code) {
                            localStorage.setItem('user_referral_code', res.user.referral_code); // ← ini yang baru
                        }
                        if (res.user && res.user.photo) {
                            localStorage.setItem('user_photo', fixKilauUrl(res.user.photo));
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Berhasil!',
                            text: 'Mengalihkan ke dashboard...',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = res.redirect_url;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal!',
                            text: res.error || 'Terjadi kesalahan, silakan coba lagi.'
                        });
                    }
                },
                error: function(xhr) {
                    console.log("Error Internal:", xhr.responseJSON);
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Internal Gagal!',
                        text: xhr.responseJSON?.error || 'Terjadi kesalahan internal.'
                    });
                }
            });
        });
    </script>

    <script>
        function fixKilauUrl(url){
            if(!url) return '';
            return url.includes('/kilau/upload/')
                ? url
                : url.replace('/upload/','/kilau/upload/');
        }
    </script>
@endsection
