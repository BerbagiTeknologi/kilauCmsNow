# Rencana Implementasi Dashboard Analytics GA4

Dokumen ini dibuat sebagai pegangan implementasi di chat baru. Targetnya menambah halaman analytics baru di admin CMS Kilau tanpa mengubah halaman `/admin/dashboard` lama.

## Tujuan

- Memasang Google Analytics 4 untuk website `https://home.kilauindonesia.org/`.
- Menampilkan dashboard analytics baru di admin: `/admin/dashboard-analytics`.
- Menambah menu sidebar baru `Dashboard Analytics` tepat di bawah menu `Dashboard`.
- Mengambil data analytics asli dari GA4 Data API, bukan dari tabel lokal `view_traffics`.
- Tetap mempertahankan dashboard lama dan route lama.

## Data GA4 Yang Sudah Diketahui

```env
GOOGLE_ANALYTICS_MEASUREMENT_ID=G-LDPVZ3R7MY
GOOGLE_ANALYTICS_PROPERTY_ID=483022450
```

Catatan:

- `Measurement ID` dipakai untuk script tracking di halaman publik.
- `Property ID` dipakai Laravel untuk membaca report dari GA4 Data API.
- `Stream ID` `14593308793` tidak perlu dipakai untuk implementasi dashboard ini.

## Dependency Yang Dibutuhkan

Sesuai aturan repo, perintah ini jangan dijalankan otomatis oleh assistant. User perlu menjalankan sendiri:

```bash
composer require google/analytics-data
```

Setelah package terpasang, lanjut implementasi service GA4.

## Credential GA4 Data API

Dashboard Laravel butuh service account Google Cloud.

Langkah umum:

1. Buka Google Cloud Console.
2. Buat atau pilih project.
3. Enable `Google Analytics Data API`.
4. Buat service account.
5. Download credential JSON.
6. Simpan file JSON di server, contoh:

```text
storage/app/google-analytics/service-account.json
```

7. Tambahkan email service account sebagai viewer di Google Analytics property:

```text
Admin > Property access management > Add users
```

Jangan commit file JSON credential ke Git.

Env yang disarankan:

```env
GOOGLE_ANALYTICS_MEASUREMENT_ID=G-LDPVZ3R7MY
GOOGLE_ANALYTICS_PROPERTY_ID=483022450
GOOGLE_ANALYTICS_CREDENTIALS_PATH=storage/app/google-analytics/service-account.json
```

## File Yang Akan Ditambah atau Diubah

File baru:

```text
app/Services/GoogleAnalyticsService.php
app/Http/Controllers/AdminPage/AnalyticsDashboardController.php
resources/views/AdminPage/Analytics/dashboard.blade.php
```

File yang diubah minimal:

```text
config/services.php
routes/web.php
resources/views/AdminPage/App/sidebard.blade.php
resources/views/App/master.blade.php
.env.example
```

## Tracking Script GA4

Pasang tracking di layout publik saja:

```text
resources/views/App/master.blade.php
```

Jangan pasang di:

```text
resources/views/AdminPage/App/master.blade.php
```

Alasannya: kunjungan admin jangan ikut masuk analytics pengunjung website.

Contoh Blade:

```blade
@if (config('services.google_analytics.measurement_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.measurement_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.measurement_id') }}');
    </script>
@endif
```

Tambahkan konfigurasi:

```php
'google_analytics' => [
    'measurement_id' => env('GOOGLE_ANALYTICS_MEASUREMENT_ID'),
    'property_id' => env('GOOGLE_ANALYTICS_PROPERTY_ID'),
    'credentials_path' => env('GOOGLE_ANALYTICS_CREDENTIALS_PATH'),
],
```

## Route Baru

Tambahkan import controller:

```php
use App\Http\Controllers\AdminPage\AnalyticsDashboardController;
```

Di dalam group admin existing:

```php
Route::get('/dashboard-analytics', [AnalyticsDashboardController::class, 'index'])
    ->name('dashboard.analytics');
```

Jangan mengubah route ini:

```php
Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
```

## Sidebar

File:

```text
resources/views/AdminPage/App/sidebard.blade.php
```

Tambahkan menu baru tepat di bawah menu `Dashboard` lama:

```blade
<li class="nav-item {{ Request::routeIs('dashboard.analytics') ? 'active' : '' }}">
    <a href="{{ route('dashboard.analytics') }}">
        <i class="fas fa-chart-line"></i>
        <p>Dashboard Analytics</p>
    </a>
</li>
```

Update active state bagian Home supaya ikut aktif:

```blade
Request::routeIs('dashboard.analytics')
```

## Isi Dashboard Analytics

Default periode: 28 hari terakhir.

### 1. Ringkasan Utama

Tampilkan card:

- Total pengguna: `activeUsers`
- Pengguna baru: `newUsers`
- Returning users: dari dimension `newVsReturning`
- Sessions: `sessions`
- Page views: `screenPageViews`
- Bounce rate: `bounceRate`
- Avg session duration: `averageSessionDuration`

### 2. Grafik Tren Pengunjung

Grafik line per hari untuk 28 hari terakhir.

Metric:

- `activeUsers`
- `sessions`
- `screenPageViews`

Dimension:

- `date`

### 3. New vs Returning

Chart pie atau bar.

Dimension:

- `newVsReturning`

Metric:

- `activeUsers`

### 4. Asal Traffic

Tampilkan tabel atau chart.

Dimension:

- `sessionPrimaryChannelGroup`
- `sessionSourceMedium`

Metric:

- `sessions`
- `activeUsers`

Contoh hasil:

- `Organic Search`
- `Direct`
- `Referral`
- `Organic Social`
- `google / organic`
- `instagram.com / referral`

### 5. Lokasi Pengunjung

Tampilkan negara dan kota teratas.

Dimension:

- `country`
- `city`

Metric:

- `activeUsers`
- `sessions`

### 6. Perangkat Pengunjung

Tampilkan chart device.

Dimension:

- `deviceCategory`

Metric:

- `activeUsers`
- `sessions`

Opsional tahap lanjut:

- `browser`
- `operatingSystem`

### 7. Halaman Terpopuler

Tampilkan tabel halaman paling banyak dikunjungi.

Dimension:

- `pageTitle`
- `pagePath`

Metric:

- `screenPageViews`
- `activeUsers`
- `averageSessionDuration`
- `bounceRate`

### 8. Halaman Dengan Bounce Rate Tinggi

Tampilkan halaman dengan traffic cukup besar dan bounce rate tinggi.

Tujuan:

- mencari halaman yang perlu diperbaiki copywriting, CTA, layout, atau speed.

## Event Custom Tahap Kedua

Setelah dashboard dasar jalan, event custom yang bagus untuk CMS Kilau:

- Klik tombol donasi floating.
- Buka modal donasi.
- Pilih program donasi.
- Submit form donasi.
- Klik WhatsApp atau contact.
- Klik referral program.
- Download dokumen.
- Submit komentar artikel.

Contoh helper JavaScript:

```js
function trackKilauEvent(name, params = {}) {
    if (typeof gtag !== 'function') {
        return;
    }

    gtag('event', name, params);
}
```

Contoh event:

```js
trackKilauEvent('open_donation_modal', {
    source: 'floating_button'
});
```

## Struktur Service Yang Disarankan

File:

```text
app/Services/GoogleAnalyticsService.php
```

Tanggung jawab:

- Membaca config `property_id` dan `credentials_path`.
- Membuat client GA4 Data API.
- Menyediakan method kecil untuk query report.
- Mengembalikan array siap pakai untuk controller.
- Jika config belum lengkap, return data kosong dengan pesan error yang aman.

Method yang disarankan:

```php
summary(string $startDate = '28daysAgo', string $endDate = 'today'): array
dailyTrend(string $startDate = '28daysAgo', string $endDate = 'today'): array
newVsReturning(string $startDate = '28daysAgo', string $endDate = 'today'): array
trafficSources(string $startDate = '28daysAgo', string $endDate = 'today'): array
locations(string $startDate = '28daysAgo', string $endDate = 'today'): array
devices(string $startDate = '28daysAgo', string $endDate = 'today'): array
topPages(string $startDate = '28daysAgo', string $endDate = 'today'): array
```

## Struktur Controller Yang Disarankan

File:

```text
app/Http/Controllers/AdminPage/AnalyticsDashboardController.php
```

Tanggung jawab:

- Thin controller.
- Ambil query `start_date` dan `end_date` jika nanti ingin filter tanggal.
- Default 28 hari terakhir.
- Panggil `GoogleAnalyticsService`.
- Return view `AdminPage.Analytics.dashboard`.

## Tampilan View

File:

```text
resources/views/AdminPage/Analytics/dashboard.blade.php
```

Gunakan layout:

```blade
@extends('AdminPage.App.master')
```

Komponen UI:

- Card ringkasan di atas.
- Chart trend harian.
- Chart new vs returning.
- Chart device.
- Tabel traffic source.
- Tabel top pages.
- Tabel lokasi.

Chart bisa memakai Chart.js yang sudah tersedia di admin master.

## Fallback Jika GA Belum Aktif

Jika GA belum menerima data atau credential belum siap, tampilkan alert:

```text
Google Analytics belum mengirim data atau konfigurasi belum lengkap.
Pastikan Measurement ID sudah terpasang dan service account sudah diberi akses ke property GA4.
```

Dashboard tetap harus render, jangan sampai error 500.

## Validasi Manual

Setelah implementasi:

1. Pastikan env sudah diisi.
2. Pastikan credential JSON ada di path yang benar.
3. Pastikan service account punya akses viewer di property GA4.
4. Buka website publik `https://home.kilauindonesia.org/`.
5. Cek source halaman, pastikan ada `G-LDPVZ3R7MY`.
6. Cek Google Analytics Realtime, pastikan ada kunjungan masuk.
7. Buka `/admin/dashboard-analytics`.
8. Pastikan tidak ada error.
9. Pastikan data akan mulai muncul setelah GA menerima traffic.

## Catatan Penting

- GA4 tidak selalu real-time untuk report dashboard; sebagian data bisa delay.
- Realtime report berbeda dengan report standar.
- Ad blocker bisa membuat sebagian traffic tidak tercatat.
- Jangan gunakan data `view_traffics` untuk metrik seperti lokasi, source traffic, bounce rate, atau device karena datanya tidak lengkap.
- Tabel lokal `view_traffics` masih boleh dipakai sebagai log internal sederhana, tetapi bukan sumber utama analytics.
