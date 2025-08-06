<div class="main-header">
    <div class="main-header-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">

            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
        <div class="container-fluid">
            <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
            </nav>

            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                        aria-expanded="false" aria-haspopup="true">
                        <i class="fa fa-search"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-search animated fadeIn">
                        <form class="navbar-left navbar-form nav-search">
                            <div class="input-group">
                                <input type="text" placeholder="Search ..." class="form-control" />
                            </div>
                        </form>
                    </ul>
                </li>
                
                <li class="nav-item dropdown me-2">
                    <a class="nav-link position-relative" href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fa-lg"></i>
                        @if($notifCount ?? 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $notifCount }}
                            </span>
                        @endif
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end animated fadeIn shadow-sm"
                        style="min-width: 320px">
                        <h6 class="dropdown-header fw-semibold">Notifikasi Artikel</h6>

                        @forelse($notifList ?? [] as $n)
                            <li class="px-3 py-2 small {{ $n->status === 'unread' ? 'fw-bold' : '' }}">
                                {{ $n->message }}<br>
                                <span class="text-muted fst-italic">
                                    {{ $n->created_at->diffForHumans() }}
                                </span>
                            </li>
                            @if(!$loop->last)<li><hr class="dropdown-divider my-0"></li>@endif
                        @empty
                            <li class="px-3 py-2 small text-muted">Belum ada notifikasi</li>
                        @endforelse

                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center small" href="#">
                                Lihat semua
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                        aria-expanded="false">
                        <div class="avatar-sm">
                            <i class="fas fa-user avatar-img rounded-circle text-primary me-2"
                                style="font-size: 25px; margin-left: 10px; margin-top: 5px;"></i>
                        </div>
                        <span class="profile-username">
                            <span class="op-7">Hi,</span>
                            <span class="fw-bold">Admin Kilau</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-logout" style="background: none; border: none; margin-left: 10px; color: #1363c6; font-size: 17px; cursor: pointer; font-weight:bold;">
                                        LOGOUT
                                    </button>
                                </form>
                            </li>
                        </div>
                    </ul>                    
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>
