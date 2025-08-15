<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>KILAU</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Kilau Digital Platform" name="description" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
                @if(isset($selectedProgram)) data-program-id="{{ $selectedProgram->id }}" @endif>
            <i class="bi bi-heart-fill"></i>
            </a>

        <!-- Tombol Chat Bot -->
        <a href="#" class="chatbot-button" id="chatbot-button">
            <i class="bi bi-chat-dots" id="chatbot-icon"></i>
        </a>
    </div>

    <!-- Chatbot Container -->
    <div class="chat-container" id="chat-container">
        <div class="chat-header">
            Customer Service
            <!-- Tombol Close Chatbot -->
            <span class="close-chatbot" id="close-chatbot">×</span>
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
