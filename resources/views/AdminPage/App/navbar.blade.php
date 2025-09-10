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

                {{-- ================= BELL NOTIFIKASI BERITA (BARU) ================= --}}
                <li class="nav-item dropdown me-2">
                    <a class="nav-link position-relative" href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-newspaper fa-lg"></i>
                        <span id="berita-badge"
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none">
                            0
                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end animated fadeIn shadow-sm"
                        style="min-width: 320px">
                        <h6 class="dropdown-header fw-semibold">Notifikasi Berita</h6>

                        <div id="berita-notif-list">
                            <li class="px-3 py-2 small text-muted">Belum ada notifikasi</li>
                        </div>

                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" id="berita-clear"
                                    class="dropdown-item text-center small">
                                Kosongkan notifikasi
                            </button>
                        </li>
                    </ul>
                </li>
                {{-- ================= END BELL NOTIFIKASI BERITA ================= --}}

                {{-- ============== BELL NOTIFIKASI ARTIKEL (SUDAH ADA) ============== --}}
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
                            <a class="dropdown-item text-center small" href="{{ route('article') }}">
                                Lihat semua
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- ============== END BELL NOTIFIKASI ARTIKEL ====================== --}}

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

{{-- ===================== SCRIPTS: NOTIFIKASI BERITA ===================== --}}
<script>
(function(){
  // ---- Config pola endpoint berita yang mau dipantau ----
  // Sesuaikan kalau kamu punya URL lain; pola ini cukup umum:
  const BERITA_URL_PATTERNS = [
    /\/api\/berita(\?|$)/i,            // GET list berita
    /\/api\/berita-create(\?|$)/i,     // POST create berita
    /\/berita(\/|$)/i                  // fallback pola umum
  ];

  const STORE_KEY = 'beritaNotif';
  const MAX_ITEMS = 5;

  const $badge = $('#berita-badge');
  const $list  = $('#berita-notif-list');

  function loadStore(){
    try {
      return JSON.parse(localStorage.getItem(STORE_KEY) || '[]');
    } catch(e){ return []; }
  }
  function saveStore(arr){
    localStorage.setItem(STORE_KEY, JSON.stringify(arr.slice(0, MAX_ITEMS)));
  }

  function render(){
    const items = loadStore();
    // badge
    const unread = items.filter(x => !x.read).length;
    if (unread > 0) { $badge.removeClass('d-none').text(unread); }
    else            { $badge.addClass('d-none').text(0); }

    // list
    if (!items.length) {
      $list.html(`<li class="px-3 py-2 small text-muted">Belum ada notifikasi</li>`);
      return;
    }
    let html = '';
    items.forEach((n, i) => {
      html += `
        <li class="px-3 py-2 small ${n.read ? '' : 'fw-bold'}">
          ${n.message}<br>
          <span class="text-muted fst-italic">${n.time}</span>
        </li>
      `;
      if (i !== items.length-1) html += `<li><hr class="dropdown-divider my-0"></li>`;
    });
    $list.html(html);
  }

  function nowHuman(){
    // waktu sederhana; jika pakai moment/dayjs bisa lebih cakep
    const d = new Date();
    return d.toLocaleString();
  }

  function matchesBerita(url){
    if (!url) return false;
    return BERITA_URL_PATTERNS.some(p => p.test(url));
  }

  function pushNotif(message){
    const items = loadStore();
    items.unshift({ message, time: nowHuman(), read:false });
    saveStore(items);
    render();
  }

  // Kosongkan
  $('#berita-clear').on('click', function(){
    saveStore([]);
    render();
  });

  // Saat dropdown dibuka => tandai read
  $(document).on('show.bs.dropdown', '.nav-item.dropdown.me-2', function(e){
    // Pastikan yang dibuka adalah menu Berita (cek ada #berita-notif-list di dalamnya)
    if ($(this).find('#berita-notif-list').length) {
      const items = loadStore().map(x => ({...x, read:true}));
      saveStore(items);
      render();
    }
  });

  // Hook ke semua AJAX jQuery
  $(document)
    .ajaxSend(function(_e, _xhr, opts){
      if (opts && matchesBerita(opts.url)) {
        pushNotif('Sedang memuat berita…');
      }
    })
    .ajaxSuccess(function(_e, _xhr, opts){
      if (!opts || !matchesBerita(opts.url)) return;
      if (/berita-create/i.test(opts.url)) {
        pushNotif('Berita berhasil ditambahkan.');
      } else {
        pushNotif('Daftar berita berhasil dimuat.');
      }
    })
    .ajaxError(function(_e, _xhr, opts){
      if (!opts || !matchesBerita(opts.url)) return;
      if (/berita-create/i.test(opts.url)) {
        pushNotif('Gagal menambah berita.');
      } else {
        pushNotif('Gagal memuat berita.');
      }
    });

  // Render awal dari localStorage
  render();
})();
</script>
