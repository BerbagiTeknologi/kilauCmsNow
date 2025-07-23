<!-- Sidebar -->
<div class="sidebar" data-background-color="light">
    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="light">
            <a href="index.html" class="logo">
                <img src="{{ asset('assets_admin/img/LogoKilau2.png') }}" alt="Kilau" class="navbar-brand"
                    height="50" width="50" />
                <p style="color: black; padding-top: 10px; font-weight: 500;">Kilau Indonesia</p>
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right" style="color: black;"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left" style="color: black;"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt" style="color: black;"></i>
            </button>
        </div>
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-primary">
                <!-- Home Section -->
                <li
                    class="nav-item {{ Request::routeIs('dashboard') || Request::routeIs('settingsmenu') ? 'active' : '' }} nav-category">
                    <p>Home</p>
                </li>
                <li class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item {{ Request::routeIs('settingsmenu') ? 'active' : '' }}">
                    <a href="{{ route('settingsmenu') }}">
                        <i class="fas fa-cog"></i>
                        <p>Settings Menu</p>
                    </a>
                </li>

                <li
                    class="nav-item {{ Request::routeIs('testimoni') || Request::routeIs('faq') || Request::routeIs('mitra') || Request::routeIs('kontak') || Request::routeIs('berita') || Request::routeIs('profile-kilau') ? 'active' : '' }} nav-category">
                    <p>Ruang Kerja</p>
                </li>

                <li
                    class="nav-item {{ Request::routeIs('profile-kilau') || Request::routeIs('profil.tentangkami') || Request::routeIs('profil.homekilau') || Request::routeIs('profil.iklankilau') || Request::routeIs('profil.struktur') || Request::routeIs('profil.sejarah') || Request::routeIs('profil.visimisi') || Request::routeIs('profil.pimpinan') || Request::routeIs('profil.iklandonasi') || Request::routeIs('profil.legalitaslembaga') ? 'active' : '' }} dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownProfile" role="button"
                        data-toggle="collapse" data-target="#profileKilauCollapse" aria-expanded="false"
                        aria-controls="profileKilauCollapse">
                        <i class="fas fa-info-circle"></i>
                        <p>Profile Kilau</p>
                    </a>
                    <div class="collapse {{ Request::routeIs('profil.tentangkami') || Request::routeIs('profil.homekilau') || Request::routeIs('profil.iklankilau') || Request::routeIs('profil.struktur') || Request::routeIs('profil.sejarah') || Request::routeIs('profil.visimisi') || Request::routeIs('profil.pimpinan') || Request::routeIs('profil.iklandonasi') || Request::routeIs('profil.legalitaslembaga') ? 'show' : '' }}"
                        id="profileKilauCollapse">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ route('profil.tentangkami') }}"
                                    class="{{ Request::routeIs('profil.tentangkami') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('profil.tentangkami') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Tentang Kami</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profil.legalitaslembaga') }}"
                                    class="{{ Request::routeIs('profil.legalitaslembaga') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('profil.legalitaslembaga') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Legalitas Lembaga</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profil.pimpinan') }}"
                                    class="{{ Request::routeIs('profil.pimpinan') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('profil.pimpinan') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Pimpinan Kilau</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profil.iklankilau') }}"
                                    class="{{ Request::routeIs('profil.iklankilau') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('profil.iklankilau') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Iklan Page</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profil.iklandonasi') }}"
                                    class="{{ Request::routeIs('profil.iklandonasi') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('profil.iklandonasi') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Iklan Donasi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profil.homekilau') }}"
                                    class="{{ Request::routeIs('profil.homekilau') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('profil.homekilau') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Home Kilau</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profil.struktur') }}"
                                    class="{{ Request::routeIs('profil.struktur') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('profil.struktur') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Struktur</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profil.sejarah') }}"
                                    class="{{ Request::routeIs('profil.sejarah') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('profil.sejarah') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Sejarah</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('profil.visimisi') }}"
                                    class="{{ Request::routeIs('profil.visimisi') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('profil.visimisi') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Visi dan Misi</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li
                    class="nav-item 
                    {{ Request::routeIs('kontak') || Request::routeIs('colaborasi') ? 'active' : '' }} dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownHubungiKami" role="button"
                        data-toggle="collapse" data-target="#hubungiKamiCollapse" aria-expanded="false"
                        aria-controls="hubungiKamiCollapse">
                        <i class="fas fa-envelope"></i> <!-- Ikon untuk "Hubungi Kami" -->
                        <p>Hubungi Kami</p>
                    </a>
                    <div class="collapse {{ Request::routeIs('kontak') || Request::routeIs('colaborasi') ? 'show' : '' }}"
                        id="hubungiKamiCollapse">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ route('kontak') }}"
                                    class="{{ Request::routeIs('kontak') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('kontak') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Kontak</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('colaborasi') }}"
                                    class="{{ Request::routeIs('colaborasi') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('colaborasi') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Kolaborasi</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- ==== Artikel Kilau ==== --}}
                <li class="nav-item dropdown {{ Request::routeIs('article') || Request::routeIs('getKategoriArticle') || Request::routeIs('admin.commentArticle.index') ? 'active' : '' }}">
                    <a  href="#"
                        class="nav-link dropdown-toggle"
                        id="navbarDropdownArticleKilau"
                        role="button"
                        data-toggle="collapse"
                        data-target="#articleKilauCollapse"
                        aria-expanded="{{ Request::routeIs('article') || Request::routeIs('getKategoriArticle') || Request::routeIs('admin.commentArticle.index') ? 'true' : 'false' }}"
                        aria-controls="articleKilauCollapse">
                        <i class="fas fa-sticky-note"></i>   {{-- ikon baru --}}
                        <p>Artikel Kilau</p>
                    </a>

                    <div id="articleKilauCollapse"
                        class="collapse {{ Request::routeIs('article') || Request::routeIs('getKategoriArticle') || Request::routeIs('admin.commentArticle.index') ? 'show' : '' }}">
                        <ul class="nav nav-collapse">
                            <li>
                                <a  href="{{ route('article') }}"
                                    class="{{ Request::routeIs('article') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('article') ? 'color:#1572E8;font-weight:bold;' : '' }}">
                                    <span class="sub-item">Data Artikel</span>
                                </a>
                            </li>

                        
                            <li>
                                <a href="{{ route('getKategoriArticle') }}"
                                class="{{ Request::routeIs('getKategoriArticle') ? 'active' : '' }}"
                                style="{{ Request::routeIs('getKategoriArticle') ? 'color:#1572E8;font-weight:bold;' : '' }}">
                                    <span class="sub-item">Data Kategori Artikel</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.commentArticle.index') }}"
                                class="{{ Request::routeIs('admin.commentArticle.index') ? 'active' : '' }}"
                                style="{{ Request::routeIs('admin.commentArticle.index') ? 'color:#1572E8;font-weight:bold;' : '' }}">
                                    <span class="sub-item">Data Komentar Artikel</span>
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                </li>



                <li
                    class="nav-item 
                {{ Request::routeIs('berita') || Request::routeIs('getKategoriBerita') || Request::routeIs('getKomentarBerita') ? 'active' : '' }} dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownBeritaKilau" role="button"
                        data-toggle="collapse" data-target="#beritaKilauCollapse" aria-expanded="false"
                        aria-controls="beritaKilauCollapse">
                        <i class="fas fa-newspaper"></i> <!-- Ikon untuk "Berita Kilau" -->
                        <p>Berita Kilau</p>
                    </a>
                    <div class="collapse {{ Request::routeIs('berita') || Request::routeIs('getKategoriBerita') || Request::routeIs('getKomentarBerita') ? 'show' : '' }}"
                        id="beritaKilauCollapse">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ route('berita') }}"
                                    class="{{ Request::routeIs('berita') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('berita') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Data Berita</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('getKategoriBerita') }}"
                                    class="{{ Request::routeIs('getKategoriBerita') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('getKategoriBerita') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Data Kategori</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('getKomentarBerita') }}"
                                    class="{{ Request::routeIs('getKomentarBerita') ? 'active' : '' }}"
                                    style="{{ Request::routeIs('getKomentarBerita') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Data Komentar</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

               @php
                    $isProgramRoute = Request::routeIs('program') || Request::routeIs('programReferrals');
                @endphp

                <li class="nav-item dropdown {{ $isProgramRoute ? 'active' : '' }}">
                    <a href="#" class="nav-link dropdown-toggle"
                    id="navbarDropdownProgramKilau"
                    data-toggle="collapse"
                    data-target="#programKilauCollapse"
                    aria-expanded="{{ $isProgramRoute ? 'true' : 'false' }}"
                    aria-controls="programKilauCollapse">
                        <i class="fas fa-project-diagram"></i> <!-- Ikon untuk "Program Kilau" -->
                        <p>Program Kilau</p>
                    </a>

                    <div class="collapse {{ $isProgramRoute ? 'show' : '' }}" id="programKilauCollapse">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ route('program') }}"
                                class="{{ Request::routeIs('program') ? 'active' : '' }}"
                                style="{{ Request::routeIs('program') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Data Program</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('programReferrals') }}"
                                class="{{ Request::routeIs('programReferrals') ? 'active' : '' }}"
                                style="{{ Request::routeIs('programReferrals') ? 'color: #1572E8; font-weight: bold;' : '' }}">
                                    <span class="sub-item">Data Referral Fundraiser</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                {{-- <li class="nav-item {{ Request::routeIs('program') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fas fa-tasks"></i>
                        <p>Program</p>
                    </a>
                </li> --}}

                <li class="nav-item {{ Request::routeIs('timeline') ? 'active' : '' }}">
                    <a href="{{ route('timeline') }}">
                        <i class="fas fa-hourglass"></i>
                        <p>Timeline Kilau</p>
                    </a>
                </li>

                


                <li class="nav-item {{ Request::routeIs('galeryAdmin') ? 'active' : '' }}">
                    <a href="{{ route('galeryAdmin') }}">
                        <i class="fas fa-image"></i>
                        <p>Galeri</p>
                    </a>
                </li>


                <li class="nav-item {{ Request::routeIs('document') ? 'active' : '' }}">
                    <a href="{{ route('document') }}">
                        <i class="fas fa-file-alt"></i>
                        <p>Dokumen</p>
                    </a>
                </li>

                <li class="nav-item {{ Request::routeIs('testimoni') ? 'active' : '' }}">
                    <a href="{{ route('testimoni') }}">
                        <i class="fas fa-comments"></i>
                        <p>Testimoni</p>
                    </a>
                </li>

                <li class="nav-item {{ Request::routeIs('faq') ? 'active' : '' }}">
                    <a href="{{ route('faq') }}">
                        <i class="fas fa-question-circle"></i>
                        <p>FAQ</p>
                    </a>
                </li>

                <li class="nav-item {{ Request::routeIs('mitra') ? 'active' : '' }}">
                    <a href="{{ route('mitra') }}">
                        <i class="fas fa-handshake"></i>
                        <p>Mitra Donatur</p>
                    </a>
                </li>

                <!--<li class="nav-item {{ Request::routeIs('kontak') ? 'active' : '' }}">-->
                <!--    <a href="{{ route('kontak') }}">-->
                <!--        <i class="fas fa-fax"></i>-->
                <!--        <p>Kontak</p>-->
                <!--    </a>-->
                <!--</li>          -->

                {{--  <!-- Logout -->
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" id="logout-form">
                        @csrf
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </form>
                </li> --}}

                <!-- Kembali ke Dashboard -->
                {{--  <li class="nav-item">
                    <a href="{{ route('dashboardlogin') }}" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i>
                        <p>Kembali ke Dashboard</p>
                    </a>
                </li>     --}}

            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->

<!-- CSS -->
<style>
    .nav-category {
        font-weight: bold;
        color: #5c5c5c;
        font-size: 16px;
        padding: 10px 0;
        text-transform: uppercase;
        margin-top: 20px;
        border-bottom: 2px solid #f0f0f0;
        background-color: #fafafa;
        padding-left: 15px;
        font-family: 'Arial', sans-serif;
    }

    .nav-category p {
        margin: 0;
        padding: 0;
    }

    .nav-item {
        position: relative;
        padding-left: 10px;
        margin-bottom: 10px;
    }

    .nav-item a {
        font-size: 14px;
        font-weight: 400;
        color: #333;
        display: flex;
        align-items: center;
        padding: 12px 20px;
        transition: background-color 0.3s ease;
    }
</style>
