# Panduan Repository (AGENTS.md)

## Bahasa
- Semua penjelasan, komentar kode, dan dokumentasi WAJIB menggunakan **Bahasa Indonesia**.
- Jangan gunakan Bahasa Inggris kecuali untuk nama variabel, fungsi, atau istilah teknis standar.

## Struktur & Organisasi Proyek
- Backend Laravel berada di folder `app/`, `routes/`, dan `database/`.
- Simpan controller baru di `app/Http/Controllers/`, model di `app/Models/`, dan service khusus di `app/Services/` jika perlu.
- Route didefinisikan di `routes/web.php` (web) dan `routes/api.php` (API).
- Aset front-end (jika ada) berada di `resources/js` dan `resources/views`; hasil bundling ada di `public/build/`.
- Hindari mengubah folder `vendor/`, `node_modules/`, `storage/`, kecuali instruksi eksplisit.

## Gaya Penulisan Kode
- Ikuti gaya kode yang sudah ada di repository ini.
- Jangan memaksakan PSR-12 atau format baru kecuali ada permintaan eksplisit untuk refactor.
- Pertahankan konsistensi indentasi, posisi kurung `{}`, dan style komentar yang sudah ada.
- Tambahkan komentar kode dalam Bahasa Indonesia agar mudah dipahami oleh tim internal.

## Proses Kerja Codex
1. **Analisis Dulu**  
   - Buat PLAN singkat: sebutkan file yang akan diubah dan perubahan yang diusulkan.
2. **Klarifikasi**  
   - Ajukan 3–5 pertanyaan untuk memastikan pemahaman benar sebelum mengedit.
3. **Persetujuan**  
   - Tunggu konfirmasi sebelum membuat patch atau menulis kode.
4. **Patch Minimal**  
   - Ubah hanya baris yang relevan, jangan rewrite seluruh file.
   - Gunakan format diff sehingga saya bisa review sebelum apply.

## Testing & Validasi
- Setelah membuat patch, berikan instruksi cara mengetes hasil (contoh: jalankan `php artisan serve` dan akses endpoint baru).
- Sertakan contoh request/response API jika membuat endpoint.

## Commit & Dokumentasi
- Gunakan pesan commit yang jelas dan singkat (`feat: tambah fitur lihat barang`).
- Jangan commit file `.env`, folder `storage/app`, atau file build otomatis.
- Tambahkan catatan perubahan (CHANGELOG) jika fitur baru berdampak besar.

## Catatan Tambahan
- Jika menemukan error, sertakan saran perbaikan + penjelasan penyebab.
- Jika ada beberapa cara implementasi, tawarkan opsi dan jelaskan kelebihan/kekurangan sebelum memilih.
