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

        .back-arrow {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px; /* Sesuaikan ukuran ikon */
            color: white;
            text-decoration: none;
            transition: color 0.3s ease-in-out, transform 0.2s ease-in-out;
        }

        .back-arrow:hover {
            color: #052c5b; /* Warna biru saat dihover */
            transform: scale(1.2); /* Efek membesar saat hover */
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

        .title {
            font-size: 50px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.9);
            max-width: 24ch;
            word-wrap: break-word;
            word-break: break-word;
            display: inline-block;
        }

        .subtitle {
            color: white;
            font-size: 18px;
            margin-top: 10px;
            font-weight: bold;
        }

        .outer-box {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            padding-top: 120px;
        }

        .colored-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: white;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.2s, box-shadow 0.2s;
            width: 150px;
            height: 150px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            padding: 20px;
            text-align: center;
            position: relative;
            overflow: hidden; /* Hide text initially */
        }

        .colored-box:hover {
            background-color: #0173fe;
            color: white;
            transform: scale(1.1);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); /* Shadow effect */
        }

        .colored-box img {
            width: 80px;
            height: 80px;
            margin-bottom: 27px;
            border-radius: 50%; /* Makes the image circular */
            box-shadow: 0px 4px 8px rgba(0, 123, 255, 0.5); /* Blue shadow */
        }


        .colored-box i {
            font-size: 40px;
            margin-bottom: 10px;
            margin-top: 10px;
            color: black;
            transition: color 0.3s;
        }

        .colored-box:hover i {
            color: white;
        }

        .colored-box span {
            display: -webkit-box;
            -webkit-line-clamp: 3; 
            -webkit-box-orient: vertical;
            overflow: hidden;
            position: absolute;
            bottom: 15px;
            font-size: 13px;
            font-weight: bold;
            color: white;
            text-align: center;
            white-space: normal;  
            text-overflow: ellipsis; 
            max-width: 100px;  
        }


        .colored-box:hover span {
            display: block;
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

         /* Responsivitas untuk Handphone Kecil */
       /* ========= Handphone kecil & sedang (≤ 600 px) ========= */
        @media screen and (max-width: 600px){
        
            /* ── kontainer: tetap flex‑wrap ── */
            .outer-box{
                padding-top:30px;
                flex-direction:row;         /* baris, bukan kolom */
                flex-wrap:wrap;             /* agar bisa turun baris */
                justify-content:space-between;
                width:100%;
                gap:12px;                   /* jarak antar card */
            }
        
            /* ── setiap card: ambil ±48 % lebar ── */
            .colored-box{
                flex:0 0 calc(50% - 12px);  /* 2 kolom, kurangi gap */
                max-width:calc(50% - 12px);
                padding:10px;
                text-align:center;
            }
        
            /* gambar & ikon menyesuaikan */
            .colored-box img{ max-width:70%; }
            .colored-box i{ font-size:20px; }
        
            /* judul halaman */
            .title-wrapper{ top:5px; margin-top:10px; }
            .title{ font-size:6vw; max-width:90%; }
        
            /* footer */
            .footer{
                position:fixed; bottom:0; left:0;
                width:100%; padding:5px 0;
                background:rgba(0,0,0,.5); color:#fff;
                font-size:12px; text-align:center;
            }
        }


        /* Responsivitas untuk Handphone Lebih Besar (misalnya, Samsung Galaxy Note) */
        @media screen and (min-width: 350px) and (max-width: 600px) {
            .title-wrapper {
                top: 50px;
                /* Menurunkan posisi wrapper agar lebih dekat dengan bagian atas */
                font-size: 1.5rem;
                width: 100%;
                text-align: center;
                margin-top: 10px;
            }

            .title {
                font-size: 6vw;
                /* Ukuran font lebih kecil berdasarkan lebar layar */
                word-wrap: break-word;
                word-break: break-word;
                text-align: center;
                max-width: 90%;
            }

            .colored-box img {
                max-width: 70%;
                /* Memperkecil logo pada layar yang lebih besar dari 480px hingga 600px */
            }
        }

        
    </style>
@endsection

@section('content')
    <section class="home">
        <a href="{{ url('/') }}" class="back-arrow">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="title-wrapper">
            <h1 class="title">KILAU DIGITAL PLATFORM PILIH WEB UNTUK LOGIN</h1>
        </div>

        <div class="main-content">
            <div class="outer-box">
                <!-- Baris 1: Kilau CMS Logo -->
                <div class="colored-box" data-bs-toggle="modal" data-bs-target="#loginModal1">
                    <img src="{{ asset('assets/img/LogoKilau2.png') }}" alt="Logo Kilau CMS">
                    <i class="bi bi-people-fill"></i>
                    <span>CMS PROFILE KILAU</span>
                </div>

                <!-- Baris 2: Kilau Management & Kilau KLINIQ Logo -->
                <div class="colored-box" style="margin-top: 30px;" data-bs-toggle="modal" data-bs-target="#loginModal2">
                    <img src="{{ asset('assets/img/kilauomg.png') }}" alt="Logo Kilau Management">
                    <i class="bi bi-database"></i>
                    <span>MANAGEMENT KILAU</span>
                </div>

                <div class="colored-box" style="margin-top: 30px;" data-bs-toggle="modal" data-bs-target="#loginModal3">
                    <img src="{{ asset('assets/img/kliniq.png') }}" alt="Logo Kilau KLINIQ">
                    <i class="bi bi-hospital-fill"></i>
                    <span>KLINIQ KITA KILAU</span>
                </div>

                <!-- Baris 3: Berbagi Pendidikan & Berbagi Bahagia Logo -->
                <div class="colored-box" data-bs-toggle="modal" data-bs-target="#loginModal4">
                    <img src="{{ asset('assets/img/logoBP.png') }}" alt="Logo Berbagi Pendidikan">
                    <i class="bi bi-book-fill"></i>
                    <span>BERBAGI PENDIDIKAN</span>
                </div>

                <div class="colored-box" data-bs-toggle="modal" data-bs-target="#loginModal5">
                    <img src="{{ asset('assets/img/bah.png') }}" alt="Logo Berbagi Bahagia">
                    <i class="bi bi-heart-fill"></i>
                    <span>BERBAGI BAHAGIA</span>
                </div>
                
                 <div class="colored-box" data-bs-toggle="modal" data-bs-target="#loginModal6">
                    <img src="{{ asset('assets/img/bt.jpg') }}" alt="Logo Berbagi Teknologi">
                    <i class="bi bi-heart-fill"></i>
                    <span>BERBAGI TEKNOLOGI</span>
                </div>
            </div>
        </div>
        
    </section>

    <!-- Modal for Login Descriptions -->
    <div class="modal fade" id="loginModal1" tabindex="-1" aria-labelledby="loginModalLabel1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel1">Login to WEB CMS KILAU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Silakan login untuk mengakses fitur-fitur CMS Kilau.</p>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('login') }}'">Login</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loginModal2" tabindex="-1" aria-labelledby="loginModalLabel2" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel2">Login to WEB MANAGEMENT KILAU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Silakan login untuk mengakses fitur manajemen Kilau.</p>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='https://kilauindonesia.org/login', '_blank'">Login</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loginModal3" tabindex="-1" aria-labelledby="loginModalLabel3" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel3">Login to WEB KLINIQ KILAU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Silakan login untuk mengakses layanan KLINIQ Kilau.</p>
                    <button type="button" class="btn btn-primary" onclick="window.open('https://kl.kliniqta.id/', '_blank')">Login</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loginModal4" tabindex="-1" aria-labelledby="loginModalLabel4" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel4">Login to Berbagi Pendidikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Silakan login untuk mengakses fitur berbagi pendidikan.</p>
                    <button type="button" class="btn btn-primary" onclick="window.open('https://berbagipendidikan.org/', '_blank')">Login</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loginModal5" tabindex="-1" aria-labelledby="loginModalLabel5" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel5">Login to Berbagi Bahagia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Silakan login untuk mengakses fitur berbagi bahagia.</p>
                    <button type="button" class="btn btn-primary" onclick="window.open('https://berbagibahagia.org/', '_blank')">Login</button>
                </div>
            </div>
        </div>
    </div>
    
     <div class="modal fade" id="loginModal6" tabindex="-1" aria-labelledby="loginModalLabel6" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel6">Login to Berbagi Teknologi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Silakan login untuk mengakses fitur berbagi teknologi.</p>
                    <button type="button" class="btn btn-primary" onclick="window.open('https://berbagiteknologi.net/', '_blank')">Login</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.querySelectorAll('.colored-box span').forEach(function(span) {
            var text = span.innerText;
            var words = text.split(' '); // Memecah teks menjadi array kata
            if (words.length > 3) {
                span.innerText = words.slice(0, 3).join(' ') + '...'; // Hanya ambil 3 kata pertama
            }
        });

    </script>
@endsection
