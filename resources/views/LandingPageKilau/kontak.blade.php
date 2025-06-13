@extends('App.master')

@section('style')
    <style>
        /* Tambahkan CSS khusus jika diperlukan */
        .contact-section {
            padding: 60px 0;
        }

        .contact-info {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .contact-info h3 {
            color: #1363c6;
            margin-bottom: 20px;
        }

        .contact-info p {
            font-size: 1rem;
            color: #333;
            margin-bottom: 10px;
        }

        .contact-info i {
            color: #1363c6;
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .map-container {
            height: 300px;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 20px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        .text-section {
            text-align: center;
            /* Pusatkan teks judul */
        }

        .btn-sejarah {
            background-color: #1363c6;
            /* Warna tombol */
            color: #fff;
            /* Warna teks tombol */
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-sejarah:hover {
            background-color: #0e4a9e;
            /* Warna tombol saat hover */
        }
    </style>
@endsection

@section('content')
    <!-- Hero Start -->
    <div class="container-fluid pt-5 bg-primary hero-header">
        <div class="container pt-5">
            <div class="row g-5 pt-5">
                <!-- Kolom untuk Teks dan Breadcrumb -->
                <div class="col-12 text-center" style="margin-top: 100px !important;">
                    <h1 class="display-4 text-white mb-4">Hubungi Kami</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Beranda</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Profil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Kontak Kami</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- Kontak Start -->
    <div class="container-fluid py-5 contact-section">
        <div class="container py-5">
            <!-- Form Ajukan Kolaborasi -->
            <h2 class="text-center">Ajukan Pengajuan Kerjasama</h2>
            <div class="form-container mt-4 p-4 border rounded">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Notifikasi jika ada error -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.create') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Pilih Program dalam Bentuk Button -->
                    <div class="mb-3">
                        <h5 class="fw-bold">Pilih Program</h5>
                        <div class="row">
                            @foreach ($programs as $index => $program)
                                <div class="col-md-4 mb-3">
                                    <input type="radio" id="program_{{ $program->id }}" name="id_program"
                                        value="{{ $program->id }}" class="btn-check program-radio" required
                                        data-judul="{{ $program->judul }}">
                                    <label for="program_{{ $program->id }}" class="btn btn-outline-primary w-100 py-2">
                                        {{ $program->judul }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Jenis Kerjasama dalam Bentuk Button -->
                    <div class="mb-3 jenis-kerjasama-container">
                        <h5 class="fw-bold">Jenis Kerjasama</h5>
                        <div class="row" id="jenisKerjasamaOptions">
                            <div class="text-muted">Silakan pilih program terlebih dahulu.</div>
                        </div>
                    </div>

                    <!-- Kategori Mitra dalam Bentuk Button -->
                    <div class="mb-3">
                        <h5 class="fw-bold">Kategori Mitra</h5>
                        <div class="row">
                            @php
                                $kategoriMitra = ['Perusahaan', 'Instansi/Lembaga/Komunitas', 'Perorangan'];
                            @endphp
                            @foreach ($kategoriMitra as $index => $kategori)
                                <div class="col-md-4 mb-2">
                                    <input type="radio" id="kategori_{{ $index }}" name="kategori_mitra"
                                        value="{{ $kategori }}" class="btn-check kategori-mitra-radio" required>
                                    <label for="kategori_{{ $index }}" class="btn btn-outline-primary w-100 py-2">
                                        {{ $kategori }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Dokumen Organisasi/Perusahaan -->
                    <h5 class="fw-bold">Dokumen Perusahaan/Instansi</h5>
                    <div id="npwpContainer">
                        <p id="npwpText" class="text-muted">Silakan pilih Kategori Mitra terlebih dahulu.</p>

                        <div id="npwpFields" class="d-none">

                            <div class="mb-3" id="namaPerusahaanField" style="display: none;">
                                <label for="nama_perusahaan" class="form-label">Nama Perusahaan/Instansi<span
                                        class="text-danger"> *</span></label>
                                <input type="text" name="nama_perusahaan" id="nama_perusahaan"
                                    class="form-control border-primary"
                                    placeholder="Masukkan nama perusahaan atau instansi">
                            </div>

                            <!-- Input Jabatan (Hanya untuk Perusahaan/Instansi) -->
                            <div class="mb-3" id="jabatanField" style="display: none;">
                                <label for="jabatan" class="form-label">Jabatan di Perusahaan/Instansi<span
                                        class="text-danger"> *</span></label>
                                <input type="text" name="jabatan" id="jabatan" class="form-control border-primary"
                                    placeholder="Masukkan jabatan Anda di perusahaan atau instansi">
                            </div>

                            <!-- Input Nomor HP Organisasi -->
                            <div class="mb-3">
                                <label for="nomor_hp_organisasi" class="form-label">Nomor HP Perusahaan/Instansi<span
                                        class="text-danger"> *</span></label>
                                <input type="tel" name="nomor_hp_organisasi" id="nomor_hp_organisasi"
                                    class="form-control border-primary" placeholder="Masukkan nomor HP perusahaan"
                                    pattern="[0-9]+" inputmode="numeric">
                            </div>

                            <!-- Upload File NPWP -->
                            <div class="mb-3">
                                <label for="npwp_file" class="form-label fw-bold">Upload NPWP</label>
                                <input type="file" name="npwp_file" id="npwp_file" class="form-control border-primary"
                                    accept="image/*">
                                <p class="text-muted mt-1">Format file: JPG, PNG (Max 2MB)</p>
                                <img id="previewNpwp" class="mt-2 d-none" width="350" alt="Preview NPWP">
                            </div>

                            <!-- Upload Foto Orang di NPWP -->
                            <div class="mb-3">
                                <label for="foto_orang_npwp" class="form-label fw-bold">Selfie dengan NPWP</label>
                                <div class="d-flex flex-column align-items-start">
                                    <video id="cameraPreview" class="d-none" width="350" autoplay></video>
                                    <button type="button" id="takePhotoBtn" class="btn btn-primary mt-2">Ambil
                                        Foto</button>
                                </div>
                                <input type="file" name="foto_orang_npwp" id="foto_orang_npwp"
                                    class="form-control border-primary d-none" accept="image/*">
                                <p class="text-muted mt-1">Pastikan wajah dan NPWP terlihat jelas.</p>
                                <canvas id="photoCanvas" class="d-none"></canvas>
                                <img id="previewSelfie" class="mt-2 d-none" width="350" alt="Preview Selfie NPWP">
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi Diri -->
                    <h5 class="fw-bold mt-4">Deskripsi Diri</h5>
                    <p class="text-muted">Silakan isi informasi pribadi Anda dengan lengkap.</p>


                    <div class="mb-3">
                        <label for="alamat_email" class="form-label">Alamat Email Anda<span class="text-danger">
                                *</span></label>
                        <input type="email" name="alamat_email" id="alamat_email" class="form-control border-primary"
                            autocomplete="email" required placeholder="Gunakan email yang aktif." disabled>
                    </div>


                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap<span class="text-danger">
                                *</span></label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control border-primary"
                            placeholder="Masukkan nama lengkap Anda" disabled required>
                    </div>

                    <div class="mb-3">
                        <label for="nomor_hp" class="form-label">Nomor HP<span class="text-danger"> *</span></label>
                        <input type="tel" name="nomor_hp" id="nomor_hp" class="form-control border-primary"
                            placeholder="Masukkan nomor HP yang aktif" disabled required pattern="[0-9]+"
                            inputmode="numeric">
                    </div>


                    <div class="mb-3">
                        <label for="deskripsi_pengajuan_kerjasama" class="form-label">Deskripsi Pengajuan</label>
                        <textarea name="deskripsi_pengajuan_kerjasama" id="deskripsi_pengajuan_kerjasama" class="form-control border-primary"
                            placeholder="Jelaskan secara singkat mengenai pengajuan kerjasama" disabled></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" disabled>Ajukan Kolaborasi</button>
                </form>
            </div>

            <!-- Informasi Kontak -->
            <div class="row g-5 py-5">
                <div class="col-12 text-section wow fadeIn" data-wow-delay="0.1s">
                    {{-- <div class="btn btn-sm border rounded-pill text-primary px-3 mb-3">Kontak</div> --}}
                    <h1 class="mb-3">{{ $kontakMenu->judul }}</h1>
                    <p class="mb-3">{{ $kontakMenu->subjudul }}</p>
                </div>
            </div>

            <!-- Daftar Kantor Cabang -->
            <div class="row g-5">
                @if (count($kontaks) > 0)
                    @foreach ($kontaks as $kontak)
                        <div class="col-lg-6">
                            <div class="contact-info">
                                <h3>{{ $kontak->nama_kacab }}</h3>
                                <p><i class="fas fa-map-marker-alt"></i> {{ $kontak->alamat }}</p>
                                <p><i class="fas fa-phone"></i> {{ $kontak->telephone }}</p>
                                <p><i class="fas fa-envelope"></i> {{ $kontak->email }}</p>
                                <div class="map-container">
                                    <div id="map-preview{{ $kontak->id }}" style="width: 100%; height: 300px;"></div>
                                    <input type="hidden" name="maplink" id="maplink{{ $kontak->id }}"
                                        value="{{ $kontak->maplink }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center">
                        <p>Tidak ada data kontak yang tersedia.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
    <!-- Kontak End -->
@endsection

@section('scripts')
    <!-- Memuat Google Maps API dengan Callback -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxYq6wdf9FuMW3AUI7GKEgO9SlHvaht8c&region=ID&language=id&libraries=places&callback=initAllMaps">
        //AIzaSyBrRnVvMaOH_I3hRykpUb3YFUH3_FyMu8Q
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const npwpFileInput = document.getElementById("npwp_file");
            const previewNpwp = document.getElementById("previewNpwp");

            const takePhotoBtn = document.getElementById("takePhotoBtn");
            const cameraPreview = document.getElementById("cameraPreview");
            const photoCanvas = document.getElementById("photoCanvas");
            const previewSelfie = document.getElementById("previewSelfie");
            const selfieInput = document.getElementById("foto_orang_npwp");

            // Preview NPWP sebelum upload
            npwpFileInput.addEventListener("change", function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewNpwp.src = e.target.result;
                        previewNpwp.classList.remove("d-none");
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Akses kamera untuk foto selfie dengan NPWP
            let stream;
            takePhotoBtn.addEventListener("click", async function() {
                if (!stream) {
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({
                            video: true
                        });
                        cameraPreview.srcObject = stream;
                        cameraPreview.classList.remove("d-none");
                        takePhotoBtn.textContent = "Ambil Foto Sekarang";
                    } catch (error) {
                        console.error("Akses kamera ditolak:", error);
                        alert("Gagal mengakses kamera. Pastikan izin kamera sudah diaktifkan.");
                        return;
                    }
                } else {
                    const context = photoCanvas.getContext("2d");
                    photoCanvas.width = cameraPreview.videoWidth;
                    photoCanvas.height = cameraPreview.videoHeight;
                    context.drawImage(cameraPreview, 0, 0, photoCanvas.width, photoCanvas.height);

                    const imageData = photoCanvas.toDataURL("image/png");
                    previewSelfie.src = imageData;
                    previewSelfie.classList.remove("d-none");

                    // Simpan gambar dalam input file tersembunyi
                    fetch(imageData)
                        .then(res => res.blob())
                        .then(blob => {
                            const file = new File([blob], "selfie_npwp.png", {
                                type: "image/png"
                            });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            selfieInput.files = dataTransfer.files;
                        });

                    // Matikan kamera setelah ambil foto
                    stream.getTracks().forEach(track => track.stop());
                    cameraPreview.classList.add("d-none");
                    takePhotoBtn.textContent = "Ambil Foto Ulang";
                    stream = null;
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const inputsNumericOnly = document.querySelectorAll("#nomor_hp, #nomor_hp_organisasi");

            inputsNumericOnly.forEach(input => {
                // Mencegah karakter non-numeric dimasukkan
                input.addEventListener("input", function() {
                    this.value = this.value.replace(/\D/g, ""); // Menghapus karakter selain angka
                });

                // Mencegah paste karakter non-numeric
                input.addEventListener("paste", function(event) {
                    event.preventDefault();
                    let pastedData = (event.clipboardData || window.clipboardData).getData("text");
                    this.value = pastedData.replace(/\D/g, ""); // Menghapus karakter selain angka
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const namaPerusahaanInput = document.getElementById("nama_perusahaan");
            const alamatEmailInput = document.getElementById("alamat_email");
            const namaPerusahaanWarning = document.createElement("div");
            const emailWarning = document.createElement("div");

            namaPerusahaanWarning.className = "text-danger mt-1";
            emailWarning.className = "text-danger mt-1";

            namaPerusahaanInput.parentNode.appendChild(namaPerusahaanWarning);
            alamatEmailInput.parentNode.appendChild(emailWarning);

            // Cek Nama Perusahaan
            namaPerusahaanInput.addEventListener("blur", function() {
                const namaPerusahaan = this.value.trim();
                if (namaPerusahaan !== "") {
                    fetch(`/check-nama-perusahaan?nama_perusahaan=${encodeURIComponent(namaPerusahaan)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                namaPerusahaanWarning.innerHTML =
                                    "Nama perusahaan ini sudah terdaftar!";
                                namaPerusahaanInput.classList.add("is-invalid");
                            } else {
                                namaPerusahaanWarning.innerHTML = "";
                                namaPerusahaanInput.classList.remove("is-invalid");
                            }
                        })
                        .catch(error => console.error("Error:", error));
                }
            });

            // Cek Alamat Email
            alamatEmailInput.addEventListener("blur", function() {
                const email = this.value.trim();
                if (email !== "") {
                    fetch(`/check-email?alamat_email=${encodeURIComponent(email)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                emailWarning.innerHTML = "Email ini sudah digunakan!";
                                alamatEmailInput.classList.add("is-invalid");
                            } else {
                                emailWarning.innerHTML = "";
                                alamatEmailInput.classList.remove("is-invalid");
                            }
                        })
                        .catch(error => console.error("Error:", error));
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const programRadios = document.querySelectorAll(".program-radio");
            const jenisKerjasamaOptions = document.getElementById("jenisKerjasamaOptions");
            const kategoriMitraRadios = document.querySelectorAll(".kategori-mitra-radio");
            const npwpContainer = document.getElementById("npwpContainer");
            const npwpText = document.getElementById("npwpText");
            const npwpFields = document.getElementById("npwpFields");
            const deskripsiPengajuan = document.getElementById("deskripsi_pengajuan_kerjasama");
            const submitButton = document.querySelector("button[type='submit']");
            const namaPerusahaanField = document.getElementById("namaPerusahaanField");
            const jabatanField = document.getElementById("jabatanField");

            // Input yang sebelumnya disabled
            const namaLengkap = document.getElementById("nama_lengkap");
            const alamatEmail = document.getElementById("alamat_email");
            const nomorHp = document.getElementById("nomor_hp");

            // Menampilkan form NPWP hanya jika kategori mitra adalah Perusahaan atau Instansi
            kategoriMitraRadios.forEach(radio => {
                radio.addEventListener("change", function() {
                    if (this.value === "Perusahaan" || this.value ===
                        "Instansi/Lembaga/Komunitas") {
                        npwpFields.classList.remove("d-none"); // Tampilkan form NPWP
                        npwpText.innerHTML = "Silakan unggah dokumen terkait.";
                        namaPerusahaanField.style.display =
                        "block"; // Tampilkan input Nama Perusahaan
                        jabatanField.style.display = "block"; // Tampilkan input Jabatan
                    } else {
                        npwpFields.classList.add("d-none"); // Sembunyikan form NPWP
                        npwpText.innerHTML =
                        "Kategori mitra ini tidak memerlukan dokumen tambahan.";
                        namaPerusahaanField.style.display =
                        "none"; // Sembunyikan input Nama Perusahaan
                        jabatanField.style.display = "none"; // Sembunyikan input Jabatan
                    }

                    // Setelah kategori mitra dipilih, aktifkan semua field yang sebelumnya disabled
                    deskripsiPengajuan.removeAttribute("disabled");
                    submitButton.removeAttribute("disabled");
                    namaLengkap.removeAttribute("disabled");
                    alamatEmail.removeAttribute("disabled");
                    nomorHp.removeAttribute("disabled");
                });
            });

            // Data Jenis Kerjasama Berdasarkan Program
            const jenisKerjasamaMapping = {
                "Berbagi Sehat": [
                    "Pendampingan pasien lanjutan",
                    "Mitra penyelenggara pelayanan medical check-up",
                    "Pengadaan obat-obatan",
                    "Pengadaan alat kesehatan",
                    "Pengadaan tenaga kesehatan"
                ],
                "Berbagi Makan": [
                    "Pengadaan paket makanan/minuman"
                ],
                "Berbagi Sejahtera": [
                    "Pemberian hibah modal",
                    "Pengadaan sarana/tempat/alat usaha",
                    "Pembinaan UMKM",
                    "Pemberian paket sembako"
                ],
                "Berbagi Pendidikan": [
                    "Pemberian beasiswa pendidikan untuk dhuafa",
                    "Pemberian beasiswa untuk hafidz qur'an",
                    "Pengadaan kelengkapan sarana belajar mengajar",
                    "Pengadaan tutor bimbel gratis",
                    "Pembiayaan operasional shelter bimbel"
                ]
            };

            // Menampilkan jenis kerjasama berdasarkan program yang dipilih
            programRadios.forEach(radio => {
                radio.addEventListener("change", function() {
                    const selectedProgramJudul = this.getAttribute(
                        "data-judul"); // Ambil judul program
                    jenisKerjasamaOptions.innerHTML = ""; // Reset opsi sebelumnya

                    if (jenisKerjasamaMapping[selectedProgramJudul]) {
                        jenisKerjasamaMapping[selectedProgramJudul].forEach((jenis, index) => {
                            let inputRadio = document.createElement("input");
                            inputRadio.type = "radio";
                            inputRadio.id = `jenis_${index}`;
                            inputRadio.name = "jenis_kerjasama";
                            inputRadio.value = jenis;
                            inputRadio.className = "btn-check jenis-kerjasama-radio";
                            inputRadio.required = true;

                            let label = document.createElement("label");
                            label.htmlFor = `jenis_${index}`;
                            label.className = "btn btn-outline-primary w-100 py-2";
                            label.textContent = jenis;

                            let div = document.createElement("div");
                            div.className = "col-md-6 mb-2";
                            div.appendChild(inputRadio);
                            div.appendChild(label);

                            jenisKerjasamaOptions.appendChild(div);
                        });
                    } else {
                        jenisKerjasamaOptions.innerHTML =
                            `<div class="text-muted">Jenis kerjasama tidak tersedia untuk program ini.</div>`;
                    }
                });
            });
        });

        function initMap(mapPreviewId, maplink) {
            if (!maplink.includes('=')) {
                console.error("Format maplink tidak valid:", maplink);
                return;
            }

            // Ekstrak latitude dan longitude dari maplink
            var coords = maplink.split('=')[1].split(',');
            var latitude = parseFloat(coords[0]);
            var longitude = parseFloat(coords[1]);

            // Cek apakah koordinat valid
            if (isNaN(latitude) || isNaN(longitude)) {
                console.error("Koordinat tidak valid:", maplink);
                return;
            }

            // Inisialisasi peta
            var mapContainer = document.getElementById(mapPreviewId);
            var map = new google.maps.Map(mapContainer, {
                zoom: 14,
                center: {
                    lat: latitude,
                    lng: longitude
                }
            });

            // Tambahkan marker
            var marker = new google.maps.Marker({
                position: {
                    lat: latitude,
                    lng: longitude
                },
                map: map
            });

            // Klik marker untuk membuka Google Maps
            marker.addListener('click', function() {
                window.open(maplink, '_blank');
            });

            console.log("Peta berhasil ditampilkan untuk:", mapPreviewId);
        }

        function initAllMaps() {
            console.log("Google Maps API siap, memuat peta...");
            @if (count($kontaks) > 0)
                @foreach ($kontaks as $kontak)
                    initMap('map-preview{{ $kontak->id }}', '{{ $kontak->maplink }}');
                @endforeach
            @endif
        }
    </script>
@endsection
