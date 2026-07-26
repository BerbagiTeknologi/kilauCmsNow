<footer id="footer" class="footer accent-background">
    <div class="container footer-top">
        <div class="row gy-4">
            <!-- Bagian Tentang Kami -->
            <div class="col-lg-5 col-md-12 footer-about">
                {{-- <a href="index.html" class="logo d-flex align-items-center">
                   
                </a> --}}
                <span class="sitename">Kilau Indonesia</span>
                <p>Kami adalah lembaga yang berfokus pada kemanusiaan, membantu masyarakat melalui program-program sosial seperti pendidikan, kesehatan, dan kesejahteraan.</p>
                <div class="social-links d-flex mt-4">
                    <a href="https://www.tiktok.com/@kilauonline?_t=ZS-8t3A76GIhjI&_r=1" target="_blank">
                        <i class="bi bi-tiktok"></i> <!-- Menggunakan Bootstrap Icon TikTok -->
                    </a>

                    <a href="https://www.instagram.com/kilauonline?igsh=MXJ6ZGY3dW5lZGducw==" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com/@kilauonline921?si=XJ5siyFFk9DRNhgk" target="_blank">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Bagian Link Berguna -->
            <div class="col-lg-2 col-6 footer-links">
                <h4>Navigasi</h4>
                <ul>
                    <li><a href="#">Beranda</a></li>
                    <li><a href="#">Dokumen</a></li>
                    <li><a href="#">Galeri</a></li>
                    <li><a href="#">Kontak</a></li>
                </ul>
            </div>

            <!-- Bagian Layanan Kami -->
            <div class="col-lg-2 col-6 footer-links">
                <h4>Program Kami</h4>
                <ul>
                    <li><a href="#">Berbagi Sehat</a></li>
                    <li><a href="#">Berbagi Makan</a></li>
                    <li><a href="#">Berbagi Pendidikan</a></li>
                    <li><a href="#">Berbagi Sejahtera</a></li>
                </ul>
            </div>

            <!-- Bagian Kontak Kami -->
            <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
                <h4>Kontak Kami</h4>
                <p>Jl. Mutiara No. D6 Pabeanudik Indramayu.</p>
                <p class="mt-4"><strong>Telepon:</strong> <span>08112484484</span></p>
                <p><strong>Email:</strong> <span>indonesiakilau@gmail.com</span></p>
            </div>
        </div>
    </div>

    <div class="container copyright text-center mt-4">
        <p style="font-size: 15px;">© <span>Copyright</span> <strong class="px-1 sitename">Berbagi Teknologi</strong> <span>All Rights Reserved</span></p>
    </div>
</footer>

<style>
/* Responsive Design */
@media (max-width: 992px) {
    .footer .row {
        text-align: center;
        justify-content: center;
    }

    .footer-about {
        margin-bottom: 20px;
    }

    .footer-links {
        margin-bottom: 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center; /* Pusatkan konten */
    }

    .footer-links h4 {
        width: 100%;
        text-align: center;
    }

    .footer-links ul {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .footer-links ul li {
        margin-bottom: 10px;
    }

    .footer-contact {
        text-align: center;
    }

    .footer-contact p {
        margin-bottom: 5px;
    }

    .social-links {
        justify-content: center;
    }
}

/* Untuk layar yang lebih kecil (mobile) */
@media (max-width: 576px) {
    .footer .row {
        flex-direction: column;
        text-align: center;
        align-items: center; /* Pusatkan seluruh elemen */
    }

    .footer-links {
        margin-bottom: 20px;
        text-align: center;
        width: 100%;
    }

    .footer-links h4 {
        width: 100%;
        text-align: center;
    }

    .footer-links ul {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0;
    }

    .footer-links ul li {
        margin-bottom: 5px;
        text-align: center;
    }
}

</style>

