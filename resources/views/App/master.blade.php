<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>KILAU</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Kilau Digital Platform" name="description" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $googleAnalyticsMeasurementId = config('services.google_analytics.measurement_id');
    @endphp
    @if ($googleAnalyticsMeasurementId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsMeasurementId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ $googleAnalyticsMeasurementId }}');
        </script>
    @endif
    <script>
        window.trackKilauEvent = function (name, params = {}) {
            if (!name || typeof window.gtag !== 'function') {
                return;
            }

            const payload = {
                page_path: window.location.pathname,
                page_title: document.title,
                ...params,
            };

            Object.keys(payload).forEach(function (key) {
                if (payload[key] === undefined || payload[key] === null || payload[key] === '') {
                    delete payload[key];
                }
            });

            window.gtag('event', name, payload);
        };
    </script>
    
    <!-- Meta Open Graph untuk WhatsApp, Facebook, dan Twitter -->
    @if(isset($berita) && !empty($berita))
        <!-- Jika sedang di halaman berita -->
        <meta property="og:title" content="{{ $berita['judul'] ?? 'Default Title' }}" />
        <meta property="og:description" content="{{ isset($berita['konten']) ? strip_tags($berita['konten']) : 'Deskripsi tidak tersedia' }}" />
        <meta property="og:image" content="{{ isset($berita['foto']) ? 'https://berbagipendidikan.org' . $berita['foto'] : asset('storage/default.jpg') }}" />
        <meta property="og:url" content="{{ url()->current() }}" />
    @elseif(isset($selectedProgram) && !empty($selectedProgram))
        <!-- Jika sedang di halaman program -->
        <meta property="og:title" content="{{ $selectedProgram->judul ?? 'Default Title' }}" />
        <meta property="og:description" content="{{ $selectedProgram ? strip_tags($selectedProgram->deskripsi) : 'Default Description' }}" />
        <meta property="og:image" content="{{ asset('storage/' . ($selectedProgram->thumbnail_image ?? 'default.jpg')) }}" />
        <meta property="og:url" content="{{ url()->current() }}" />
    @else
       
    @endif

    

    <!-- Favicon -->
    <link href="{{ asset('assets/img/LogoKilau2.png') }}" rel="icon" />

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Ubuntu:wght@500;700&display=swap" rel="stylesheet" />

    <!-- Font Awesome 5.10.0 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('assets/lib/animate/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />

    <style>
        .floating-buttons {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: row;
        gap: 15px !important;
        z-index: 999;
    }

    /* Gaya dasar tombol floating (supaya sama ukurannya) */
    .floating-buttons a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 54px;
        height: 54px;
        font-size: 24px;
        border-radius: 50%;
        text-align: center;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s ease-in-out, transform 0.2s ease-in-out;
        color: white;
    }

    /* Tombol Chat Bot */
    .chatbot-button {
        background-color: #007bff;
    }
    .chatbot-button:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    /* Tombol Donasi */
    .donation-button {
        background-color: #dc3545;
    }
    .donation-button:hover {
        background-color: #bb2d3b;
        transform: scale(1.1);
    }

     .profile-avatar > img.avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 9999px;
        display: block;
    }

        
    </style>

    @yield('style')
</head>

<body>
    <div id="app-wrapper" class="d-flex flex-column min-vh-100">
    @include('App.navbar')

    <main class="flex-fill py-4">
            @yield('content')
    </main>

    <!-- Wrapper untuk tombol Chat Bot -->
    <div class="floating-buttons">
           <a href="#" class="donation-button" id="floating-donate-btn" title="Donasi"
                data-ga-event="click_donation_floating" data-ga-source="floating_button"
                @if(isset($selectedProgram)) data-program-id="{{ $selectedProgram->id }}" @endif>
            <i class="bi bi-heart-fill"></i>
            </a>

        <!-- Tombol Chat Bot -->
        <a href="#" class="chatbot-button" id="chatbot-button" data-ga-event="open_chatbot">
            <i class="bi bi-chat-dots" id="chatbot-icon"></i>
        </a>
    </div>

    <!-- Chatbot Container -->
    <div class="chat-container" id="chat-container">
        <div class="chat-header">
            Customer Service
            <!-- Tombol Close Chatbot -->
            <span class="close-chatbot" id="close-chatbot" data-ga-event="close_chatbot">×</span>
        </div>
        <div class="chat-box" id="chat-box">
            <!-- Pesan chat akan muncul di sini -->
        </div>
        <div class="chat-input-container">
            <input type="text" id="user-input" class="chat-input" placeholder="Ketik pesan..." />
            <button class="chat-button" onclick="sendMessage()">Kirim</button>
        </div>
    </div>

    @include('App.footer')
    </div>

    <!-- Bootstrap 5 Bundle (Popper.js included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery dan Plugin -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="{{ asset('assets/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('assets/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('assets/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('assets/lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @if ($message = Session::get('success'))
        <script>
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "{{ $message }}",
            });
        </script>
    @endif

    @if ($message = Session::get('error'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: "{{ $message }}",
            });
        </script>
    @endif

    <script>
        (function () {
            const STORAGE_KEY = 'kilaucms_affiliate';
            const TTL_MS = 7 * 24 * 60 * 60 * 1000; // 7 hari

            function nowMs() {
                return Date.now ? Date.now() : new Date().getTime();
            }

            function safeParseJson(value) {
                if (!value) return null;
                try {
                    return JSON.parse(value);
                } catch (e) {
                    return null;
                }
            }

            function normalizeAffiliate(value) {
                return (value || '').toString().trim().slice(0, 64);
            }

            function setAffiliate(affiliateSub) {
                affiliateSub = normalizeAffiliate(affiliateSub);
                if (!affiliateSub) return;
                try {
                    const payload = {
                        affiliate_sub: String(affiliateSub),
                        referral_code: String(affiliateSub),
                        expires_at: nowMs() + TTL_MS,
                    };
                    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
                } catch (e) {
                    // localStorage bisa tidak tersedia (mode private / diblokir)
                }
            }

            function captureFromUrl() {
                try {
                    const params = new URLSearchParams(window.location.search || '');
                    const affiliateSub = normalizeAffiliate(params.get('aff') || params.get('ref'));

                    if (!affiliateSub) return;

                    // last click wins: jika ada aff baru, overwrite yang lama
                    setAffiliate(affiliateSub);
                } catch (e) {
                    // Abaikan jika URLSearchParams tidak tersedia
                }
            }

            function getAffiliateSub() {
                try {
                    const payload = safeParseJson(window.localStorage.getItem(STORAGE_KEY));
                    if (!payload) return null;

                    const affiliateSub = normalizeAffiliate(payload.referral_code || payload.affiliate_sub);
                    const expiresAt = Number(payload.expires_at || 0);

                    if (!affiliateSub || !expiresAt) {
                        window.localStorage.removeItem(STORAGE_KEY);
                        return null;
                    }

                    if (nowMs() > expiresAt) {
                        window.localStorage.removeItem(STORAGE_KEY);
                        return null;
                    }

                    return affiliateSub;
                } catch (e) {
                    return null;
                }
            }

            window.KilauAffiliate = window.KilauAffiliate || {
                captureFromUrl: captureFromUrl,
                getAffiliateSub: getAffiliateSub,
                getReferralCode: getAffiliateSub,
            };

            // Capture secepat mungkin agar script lain bisa langsung pakai.
            window.KilauAffiliate.captureFromUrl();
        })();
    </script>

    <script>
        (function () {
            async function verify(url) {
                if (!url) return null;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(payload.message || 'Payment verification failed.');
                }

                return payload;
            }

            async function confirm(url, successMessage) {
                try {
                    const result = await verify(url);

                    if (result && !result.is_paid) {
                        return Swal.fire({
                            icon: 'info',
                            title: 'Pembayaran Sedang Diverifikasi',
                            text: 'Midtrans belum menyatakan pembayaran berhasil.',
                        });
                    }

                    return Swal.fire('Terima kasih!', successMessage, 'success');
                } catch (error) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Verifikasi Belum Selesai',
                        text: 'Pembayaran diterima, tetapi statusnya belum dapat diverifikasi. Silakan periksa kembali beberapa saat lagi.',
                    });
                }
            }

            window.KilauPayment = { confirm };
        })();
    </script>

    <script>
        (function () {
            const ignoredDatasetKeys = [
                'gaEvent',
                'gaSubmitEvent',
                'gaModalEvent',
                'gaChangeEvent',
                'gaInclude',
            ];

            function toParamName(key) {
                return key
                    .replace(/^ga/, '')
                    .replace(/^[A-Z]/, letter => letter.toLowerCase())
                    .replace(/[A-Z]/g, letter => '_' + letter.toLowerCase());
            }

            function castValue(value) {
                if (typeof value !== 'string') {
                    return value;
                }

                const trimmed = value.trim();

                if (/^-?\d+(\.\d+)?$/.test(trimmed)) {
                    return Number(trimmed);
                }

                return trimmed;
            }

            function parseAmount(value) {
                if (typeof value === 'number') {
                    return value;
                }

                if (!value) {
                    return null;
                }

                const normalized = String(value).replace(/[^\d]/g, '');

                return normalized ? Number(normalized) : null;
            }

            function datasetParams(element) {
                const params = {};

                Object.entries(element.dataset || {}).forEach(function ([key, value]) {
                    if (!key.startsWith('ga') || ignoredDatasetKeys.includes(key)) {
                        return;
                    }

                    params[toParamName(key)] = castValue(value);
                });

                return params;
            }

            function track(name, params = {}) {
                if (window.trackKilauEvent) {
                    window.trackKilauEvent(name, params);
                }
            }

            function trackDataEvent(element, eventNameKey) {
                const eventName = element.dataset[eventNameKey];

                if (!eventName) {
                    return false;
                }

                track(eventName, datasetParams(element));

                return true;
            }

            function donationParamsFromForm(form, amount) {
                const formData = new FormData(form);
                const programId = formData.get('id_program') || '';
                const opsionalUmum = formData.get('opsional_umum') || '';
                const typeDonasi = formData.get('type_donasi') || (programId ? '1' : '2');
                const parsedAmount = parseAmount(amount || formData.get('total'));

                return {
                    source: 'donation_form',
                    currency: 'IDR',
                    value: parsedAmount,
                    amount: parsedAmount,
                    donation_type: String(typeDonasi) === '1' ? 'program' : 'general',
                    program_id: programId,
                    opsional_umum: opsionalUmum,
                };
            }

            function isWhatsappUrl(url) {
                if (!url) {
                    return false;
                }

                try {
                    const hostname = new URL(url, window.location.href).hostname.replace(/^www\./, '');

                    return hostname === 'wa.me'
                        || hostname === 'whatsapp.com'
                        || hostname.endsWith('.whatsapp.com');
                } catch (e) {
                    return false;
                }
            }

            function isDocumentUrl(url) {
                if (!url) {
                    return false;
                }

                try {
                    const pathname = new URL(url, window.location.href).pathname.toLowerCase();

                    return /\.(pdf|doc|docx|xls|xlsx|ppt|pptx|csv|zip|rar|7z)$/i.test(pathname);
                } catch (e) {
                    return false;
                }
            }

            window.KilauAnalytics = window.KilauAnalytics || {};
            window.KilauAnalytics.donationParamsFromForm = donationParamsFromForm;

            document.addEventListener('click', function (event) {
                const target = event.target instanceof Element ? event.target : null;

                if (!target) {
                    return;
                }

                const dataEventElement = target.closest('[data-ga-event]');
                if (dataEventElement && trackDataEvent(dataEventElement, 'gaEvent')) {
                    return;
                }

                const linkElement = target.closest('a[href]');
                if (linkElement) {
                    if (isWhatsappUrl(linkElement.href)) {
                        track('click_whatsapp_contact', {
                            source: 'whatsapp_link',
                            link_url: linkElement.href || '',
                        });
                        return;
                    }

                    if (linkElement.hasAttribute('download') || isDocumentUrl(linkElement.href)) {
                        track('download_document', {
                            source: 'document_link',
                            file_url: linkElement.href || '',
                        });
                        return;
                    }
                }

                const donationButton = target.closest('.btn-donasi');
                if (donationButton) {
                    track('click_donation_cta', {
                        source: 'program_button',
                        program_id: donationButton.dataset.programId,
                        program_title: donationButton.dataset.programTitle,
                    });
                    return;
                }

                const donationAmountButton = target.closest('.donasi-btn, .our-donasi-btn');
                if (donationAmountButton) {
                    track('select_donation_amount', {
                        currency: 'IDR',
                        value: parseAmount(donationAmountButton.dataset.amount),
                    });
                    return;
                }

                const donationTypeButton = target.closest('#donasiProgramBtn, #donasiUmumBtn');
                if (donationTypeButton) {
                    track('select_donation_type', {
                        donation_type: donationTypeButton.id === 'donasiProgramBtn' ? 'program' : 'general',
                    });
                    return;
                }

                const donationOptionButton = target.closest('[name="opsional_umum"]');
                if (donationOptionButton) {
                    track('select_donation_option', {
                        opsional_umum: donationOptionButton.dataset.value || donationOptionButton.value,
                    });
                    return;
                }

                const donationProgramCard = target.closest('#program-cards .program-card');
                if (donationProgramCard) {
                    track('select_donation_program', {
                        source: 'donation_modal',
                        program_id: donationProgramCard.dataset.programId,
                        program_name: donationProgramCard.dataset.program,
                    });
                    return;
                }

                if (target.closest('#donasiCustom, #ourProgramDonasiCustom')) {
                    track('select_custom_donation_amount', {
                        currency: 'IDR',
                    });
                    return;
                }

                const articleShare = target.closest('.share-wa, .share-fb, .copy-link');
                if (articleShare) {
                    track('share_article', {
                        share_method: articleShare.classList.contains('share-wa')
                            ? 'whatsapp'
                            : (articleShare.classList.contains('share-fb') ? 'facebook' : 'copy_link'),
                    });
                    return;
                }

                const documentShare = target.closest('.btn-share');
                if (documentShare) {
                    track('share_document', {
                        document_link: documentShare.dataset.link,
                    });
                    return;
                }

                if (target.closest('#likeBtn')) {
                    track('like_article');
                    return;
                }

                const commentLike = target.closest('.like-komentar');
                if (commentLike) {
                    track('like_article_comment', {
                        comment_id: commentLike.dataset.id,
                    });
                    return;
                }

                const replyButton = target.closest('.reply-btn');
                if (replyButton) {
                    track('reply_article_comment', {
                        comment_id: replyButton.dataset.id,
                    });
                    return;
                }

                if (target.closest('#promptDonasiYa')) {
                    track('click_donation_prompt', {
                        source: 'article_prompt',
                    });
                }
            });

            document.addEventListener('change', function (event) {
                const target = event.target instanceof Element ? event.target.closest('[data-ga-change-event]') : null;

                if (target) {
                    trackDataEvent(target, 'gaChangeEvent');
                }
            });

            document.addEventListener('submit', function (event) {
                const form = event.target instanceof Element ? event.target.closest('[data-ga-submit-event]') : null;

                if (!form) {
                    return;
                }

                const params = datasetParams(form);
                const formData = new FormData(form);

                (form.dataset.gaInclude || '').split(',').map(value => value.trim()).filter(Boolean).forEach(function (name) {
                    const value = formData.get(name);

                    if (value) {
                        params[name] = castValue(value);
                    }
                });

                track(form.dataset.gaSubmitEvent, params);
            }, true);

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[data-ga-modal-event]').forEach(function (modal) {
                    modal.addEventListener('shown.bs.modal', function () {
                        track(modal.dataset.gaModalEvent, datasetParams(modal));
                    });
                });
            });
        })();
    </script>

    @yield('scripts')
    @stack('scripts')

    <script>
document.addEventListener('DOMContentLoaded', function () {
  const donateBtn  = document.getElementById('floating-donate-btn');
  if (!donateBtn) return;

  const programId  = donateBtn.getAttribute('data-program-id') || null;
  const modalEl    = document.getElementById('donasiModal');                // modal yang kamu pakai di index
  const modalAlt   = document.getElementById('ourProgramDonasiModal');      // kalau ada versi lain
  const modalNode  = modalEl || modalAlt;

  const HOME_BASE  = "{{ url('/') }}";
  const DONASI_URL = programId ? `${HOME_BASE}?donasi=${encodeURIComponent(programId)}#donasi`
                               : `${HOME_BASE}#donasi`;

  function openModalAndSelect() {
    if (!modalNode || typeof bootstrap === 'undefined') return false;

    // Saat modal sudah tampil, baru set mode & klik kartu
    const onShown = () => {
      modalNode.removeEventListener('shown.bs.modal', onShown);

      // 1) aktifkan mode "Donasi Program"
      const btnProgram = document.getElementById('donasiProgramBtn');
      if (btnProgram) btnProgram.click();
      else {
        const pc = document.getElementById('program-cards');
        const ou = document.getElementById('opsionalUmum');
        if (pc) pc.style.display = 'block';
        if (ou) ou.style.display = 'none';
      }

      // 2) klik kartu sesuai programId (agar handler index on('click') jalan)
      if (programId) {
        const tryClick = () => {
          // jQuery kalau ada:
          if (window.jQuery) {
            const $card = jQuery(`#program-cards .program-card[data-program-id="${programId}"]`);
            if ($card.length) { $card.addClass('selected-btn').trigger('click'); return true; }
          }
          // DOM murni:
          const card = document.querySelector(`#program-cards .program-card[data-program-id="${programId}"]`);
          if (card) { card.classList.add('selected-btn'); card.click(); return true; }
          return false;
        };

        if (!tryClick()) {
          // retry max 10x tiap 120ms kalau gambar/kartu render agak telat
          let tries = 0;
          const iv = setInterval(() => {
            if (tryClick() || ++tries >= 10) clearInterval(iv);
          }, 120);
        }
      }
    };

    modalNode.addEventListener('shown.bs.modal', onShown, { once: true });
    bootstrap.Modal.getOrCreateInstance(modalNode).show();
    return true;
  }

  donateBtn.addEventListener('click', function (e) {
    e.preventDefault();
    // 1) kalau modal ada di halaman ini → langsung buka & pilih
    if (openModalAndSelect()) return;

    // 2) kalau tidak ada modal → redirect ke index (nanti index init dari query)
    window.location.href = DONASI_URL;
  });
});
</script>

    <script>
      var phoneNumber = '(0234) 7121601'; // Nomor WhatsApp untuk customer service
      var initialMessage = 'Halo, saya ingin bertanya tentang Kilau Digital Platform';
  
      // URL WhatsApp untuk menghubungi CS
      var whatsappURL = 'https://wa.me/' + phoneNumber + '?text=' + encodeURIComponent(initialMessage);
  
      // Fungsi untuk membuka chatbot
      document.getElementById('chatbot-button').addEventListener('click', function () {
          var chatContainer = document.getElementById('chat-container');
          chatContainer.style.display = 'flex';  // Menampilkan chatbot
  
          // Menampilkan pesan selamat datang dengan opsi awal
          displayMessage("Halo, saya adalah Customer Service Bot. Apa yang bisa saya bantu?", 'bot-message');
          setTimeout(() => {
              displayMessage("1. Hubungi Kami (WhatsApp)\n2. Tentang Kilau Digital Platform", 'bot-message');
          }, 500);
      });
  
      // Fungsi untuk menutup chatbot
      document.getElementById('close-chatbot').addEventListener('click', function () {
          document.getElementById('chat-container').style.display = 'none';
      });
  
      // Fungsi untuk menampilkan pesan
      function displayMessage(message, sender) {
          const messageContainer = document.createElement('div');
          messageContainer.classList.add('message', sender);
          messageContainer.innerText = message;
          document.getElementById('chat-box').appendChild(messageContainer);
  
          // Scroll ke bawah setelah pesan baru
          const chatBox = document.getElementById('chat-box');
          chatBox.scrollTop = chatBox.scrollHeight;
      }
  
      // Fungsi untuk mengirim pesan
      function sendMessage() {
          const userMessage = document.getElementById('user-input').value.trim();
          if (userMessage === "") return;
  
          // Tampilkan pesan pengguna
          displayMessage(userMessage, 'user-message');
  
          // Bersihkan input
          document.getElementById('user-input').value = '';
  
          // Simulasi balasan bot setelah beberapa detik
          setTimeout(function () {
              let botReply = "Terima kasih atas pertanyaannya. Kami akan segera membantu Anda.";
  
              // Menambahkan logika untuk respons bot
              if (userMessage.includes("1")) {
                  botReply = "Anda bisa menghubungi kami melalui WhatsApp di nomor " + phoneNumber;
              } else if (userMessage.includes("2")) {
                  botReply = "Platform Digital Kami bisa diakses dan Anda bisa melihat program kami secara online.";
              } else {
                  botReply = "Maaf, saya tidak mengerti pesan Anda. Pilih '1' untuk WhatsApp atau '2' untuk Platform Digital.";
                  // Menambahkan opsi ulang jika pesan tidak valid
                  setTimeout(() => {
                      displayMessage("Silakan pilih kembali: \n1. Hubungi Kami (WhatsApp)\n2. Tentang Kilau Digital Platform", 'bot-message');
                  }, 1000);
              }
  
              displayMessage(botReply, 'bot-message');
          }, 1000);
      }
  </script>
  
</body>

</html>
