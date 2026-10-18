@extends('Auth.App.master')

@section('style')
    <style>
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
        .form-group label {
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-group {
            margin-bottom: 1.25rem;
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
        <div class="login-background"></div>
        <div class="login-form">
            <div class="card">
                <div class="login-logo">
                    <img src="{{ asset('assets/img/LogoKilau2.png') }}" alt="Kilau Logo">
                </div>

                <h2 class="text-center mt-5" style="font-weight:500;">Sign Up</h2>

                @if ($errors->has('auth'))
                    <div class="alert alert-danger">
                        {{ $errors->first('auth') }}
                    </div>
                @endif

                <form id="register-form" method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                            value="{{ old('name') }}" placeholder="Masukkan Nama" maxlength="255" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                            value="{{ old('email') }}" placeholder="Masukkan Alamat Email" maxlength="255" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                            placeholder="Minimal 8 karakter" minlength="8" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation"
                            name="password_confirmation" placeholder="Ulangi Password" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn" style="background-color: #1363c6; color:white;">
                            Daftar
                        </button>
                    </div>
                </form>

                <p class="account-check">
                    Sudah punya akun? <a href="{{ route('login') }}">Sign In</a>.
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
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('register-form');
            const password = document.getElementById('password');
            const confirmation = document.getElementById('password_confirmation');
            const requiredMessages = {
                name: 'Nama wajib diisi.',
                email: 'Email wajib diisi.',
                password: 'Password wajib diisi.',
                password_confirmation: 'Konfirmasi password wajib diisi.'
            };

            const validateConfirmation = function () {
                confirmation.setCustomValidity(
                    confirmation.value && confirmation.value !== password.value
                        ? 'Konfirmasi password tidak cocok.'
                        : ''
                );
            };

            form.addEventListener('invalid', function (event) {
                if (event.target.validity.valueMissing) {
                    event.target.setCustomValidity(requiredMessages[event.target.name]);
                } else if (event.target.validity.typeMismatch) {
                    event.target.setCustomValidity('Format email tidak valid.');
                } else if (event.target.validity.tooShort) {
                    event.target.setCustomValidity('Password minimal 8 karakter.');
                }
            }, true);
            form.addEventListener('input', function (event) {
                event.target.setCustomValidity('');
                validateConfirmation();
            });
        });
    </script>
@endsection
