@extends('Auth.App.master')

@section('style')
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        section {
            min-height: 100vh;
            padding: 0 9%;
            background-color: #1363c6;
            background-image: url({{ asset('assets/img/bg-hero.png') }});
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .home {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .title {
            font-size: 50px;
            /* Teks lebih besar */
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.9);
            max-width: 24ch;
            word-wrap: break-word;
            word-break: break-word;
            display: inline-block;
        }

        .title-wrapper {
            position: absolute;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 3rem;
            font-weight: bold;
            text-align: center;
            z-index: 1;
            background-color: rgba(0, 0, 0, 0);
            border-radius: 10px;
        }

        .outer-box {
            justify-content: center;
            display: flex;
            flex-direction: row;
            /* Mengatur flex agar card bersebelahan */
            padding-top: 120px;
            border-radius: 10px;
            width: 100%;
            gap: 20px;
            /* Menambahkan jarak antar card */
            flex-wrap: wrap;
            /* Agar card tetap rapi pada ukuran layar kecil */
            justify-content: center;
        }

        .colored-box {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.2s;
            width: 350px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .colored-box:hover {
            background-color: #0173fe;
            color: white;
            transform: scale(1.05);
        }

        .colored-box-content {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .colored-box a {
            text-decoration: none;
            color: inherit;
        }

        .colored-box i {
            font-size: 40px;
            margin-bottom: 10px;
            margin-top: 10px;
            color: black;
        }

        .colored-box:hover i {
            color: white;
        }

        .btn-link {
            text-decoration: none !important;
            color: black;
            padding: 0;
        }

        .btn-link:hover {
            text-decoration: none !important;
            color: black;
        }

        .footer {
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
        }

        /* Responsivitas untuk Handphone */
        @media screen and (max-width: 480px) {
            .outer-box {
                padding-top: 50px;
                flex-direction: column;
                /* Kolom untuk tampilan mobile */
                width: 100%;
            }

            .row {
                justify-content: center;
                flex-direction: column;
                gap: 15px;
                width: 100%;
            }

            .colored-box {
                max-width: 180px;
                padding: 10px;
            }

            .colored-box i {
                font-size: 20px;
            }

            .title-wrapper {
                top: 10px;
                font-size: 1.5rem;
            }

            .title {
                font-size: 15px;
            }

            .box-text {
                font-size: 14px;
            }

            .footer {
                background-color: rgba(0, 0, 0, 0.5);
                color: white;
                text-align: center;
                font-size: 12px;
                padding: 5px 0;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
            }
        }
    </style>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
@endsection

@section('content')
    <section class="home">
        <div class="title-wrapper">
            <h1 class="title">KILAU INFORMATION SYSTEM</h1>
        </div>

        <div class="main-content">
            <div class="outer-box">
                <!-- Jika role = 'admin', tampilkan pilihan CMS -->
                @if (session('user_role') === 'admin')
                    <div class="colored-box">
                        <a href="{{ route('dashboard') }}">
                            <div class="colored-box-content">
                                <i class="bi bi-people-fill"></i>
                                <span class="box-text">WEB CMS KILAU</span>
                            </div>
                        </a>
                    </div>
                @endif

                @if (session('user_token'))
                    <div class="colored-box">
                        <a href="https://kilauindonesia.org/kilau/dashboard">
                            <div class="colored-box-content">
                                <i class="bi bi-database"></i>
                                <span class="box-text">WEB MANAGEMENT KILAU</span>
                            </div>
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        // Custom scripts if necessary
    </script>
@endsection
