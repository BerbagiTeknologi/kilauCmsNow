<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Faq;
use App\Models\Kontak;
use App\Models\Program;
use App\Models\Struktur;
use App\Models\HomeKilau;
use App\Models\Testimoni;
use App\Models\IklanKilau;
use App\Models\KilauIklan;
use App\Models\DonasiKilau;
use App\Models\TentangKami;
use App\Models\ViewTraffic;
use App\Models\MitraDonatur;
use App\Models\SettingsMenu;
use App\Models\TimlineKilau;
use Illuminate\Http\Request;
use App\Models\IklanKilauList;
use App\Models\ProgramReferral;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
   
    public function trackDonasiModalProgram(Request $request) {
          $exists = ViewTraffic::where('session_id', session()->getId())
                ->where('type', ViewTraffic::TYPE_FORM_DONASI_PROGRAM)
                ->whereDate('viewed_at', today())
                ->exists();

        if (! $exists) {
            ViewTraffic::create([
                'session_id' => session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'type'       => ViewTraffic::TYPE_FORM_DONASI_PROGRAM,
                'viewed_at'  => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function trackDonasiModal(Request $request)
    {
        // hanya catat sekali per‑session per‑hari
        $exists = ViewTraffic::where('session_id', session()->getId())
                ->where('type', ViewTraffic::TYPE_FORM_DONASI)
                ->whereDate('viewed_at', today())
                ->exists();

        if (! $exists) {
            ViewTraffic::create([
                'session_id' => session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'type'       => ViewTraffic::TYPE_FORM_DONASI,
                'viewed_at'  => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function programReferral(Request $request, $id, $referer)
    {
        // Simpan click referral (jika belum ada dalam session hari ini)
        $sessionKey = "referral_clicked_{$id}_{$referer}";
        if (!session()->has($sessionKey)) {
            ProgramReferral::updateOrCreate(
                ['program_id' => $id, 'referer_name' => $referer],
                ['click_count' => DB::raw('click_count + 1')]
            );
            session()->put($sessionKey, true);
        }

        // Redirect ke halaman utama dengan scroll ke modal program
        return redirect()->route('home')->with('scrollToProgram', $id);
    }


    public function home(Request $request)

    {
        $alreadyLogged = ViewTraffic::where('session_id', session()->getId())
        ->whereDate('viewed_at', today())          // hanya tanggal hari ini
        ->where('type', ViewTraffic::TYPE_LANDINGPAGE)
        ->exists();

    if (! $alreadyLogged) {
        // -----------------------------------------------------
        // 2. Jika belum, baru simpan baris baru
        // -----------------------------------------------------
        ViewTraffic::create([
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'type'       => ViewTraffic::TYPE_LANDINGPAGE,
            'viewed_at'  => now(),
        ]);
    }

    // ---------------------------------------------------------
    // 3. Hitung total kunjungan landing page (unik per baris)
    // ---------------------------------------------------------
    $jumlahViewLanding = ViewTraffic::where('type', ViewTraffic::TYPE_LANDINGPAGE)->count();

        $testimoniMenu = SettingsMenu::find(2); 
        $testimonis = null;

        if ($testimoniMenu && $testimoniMenu->status == 'Aktif') {
            $testimonis = Testimoni::where('statuss_testimoni', Testimoni::TESTIMONI_AKTIF)->get();
        }

        $faqMenu = SettingsMenu::find(3); 
        $faqs = null;

        if ($faqMenu && $faqMenu->status == 'Aktif') {
            $faqs = Faq::where('status_faqs', Faq::FAQ_AKTIF)->get();
        }

        $mitraMenu = SettingsMenu::find(4); 
        $mitras = null;

        if ($mitraMenu && $mitraMenu->status == 'Aktif') {
            $mitras = MitraDonatur::where('status_mitra', MitraDonatur::MITRA_AKTIF)->get();
        }

        $tentangMenu = SettingsMenu::find(8); 
        $tentangs = null;

        if ($tentangMenu && $tentangMenu->status == 'Aktif') {
            $tentangs = TentangKami::where('status_tentang_kami', TentangKami::TENTANG_AKTIF)->get(); 
        }

       // Ambil daftar program yang aktif
        $programMenu = SettingsMenu::find(13);
        $programs = null;
    
       /*  if ($programMenu && $programMenu->status == 'Aktif') {
            $programs = Program::with('mitras') // Eager Load mitras relasi
                        ->where('status_program', Program::PROGRAM_AKTIF)
                        ->get();
        } */

        /* if ($programMenu && $programMenu->status == 'Aktif') {
            $programs = Program::with('mitras')
                        ->where('status_program', Program::PROGRAM_AKTIF)
                        ->get(); */

        if ($programMenu && $programMenu->status == 'Aktif') {
            $programs = Program::with([
                                'mitras',
                                'feedbacks' => function ($q) { // batasi agar ringan
                                                $q->take(10); // mis. ambil 10 feedback terbaru / program
                                }
                                ])
                        ->where('status_program', Program::PROGRAM_AKTIF)
                        ->get();
                        
        
            // Ambil data jumlah pelayanan dari API
            $berbagiSehat = $programs->firstWhere('id', 8);
            if ($berbagiSehat) {
                try {
                    $response = Http::timeout(5)->get('https://kl.kliniqta.id/jumlah/pelayanan');
                    if ($response->ok() && isset($response['jumlah'])) {
                        $berbagiSehat->setAttribute('program_yang_berhasil_dijalankan', $response['jumlah']);
                    }
                } catch (\Throwable $th) {
                    // Bisa log error jika perlu
                    Log::error('Gagal mengambil data pelayanan Berbagi Sehat: ' . $th->getMessage());
                }
            }
        }
    
        // Tangkap parameter dari URL
        $judulSlug = $request->query('judul');
        $modalId = $request->query('modal');
    
        // Ambil program yang sesuai dengan parameter URL
        $selectedProgram = null;
        if ($judulSlug) {
            $selectedProgram = Program::whereRaw("LOWER(REPLACE(judul, ' ', '-')) = ?", [$judulSlug])->first();
        } elseif ($modalId) {
            $selectedProgram = Program::find($modalId);
        }

        $timelineMenu = SettingsMenu::find(14); 
        $timelins = null;

        if ($timelineMenu && $timelineMenu->status == 'Aktif') {
            $timelins = TimlineKilau::where('status_timline', TimlineKilau::TIMELINE_AKTIF) 
                ->orderBy('sequence_timeline', 'asc')
                ->get(); 
        }

        $beritaMenu = SettingsMenu::find(7);
        $beritas = null;

        if ($beritaMenu && $beritaMenu->status == 'Aktif') {
            // Ambil data berita dari API hanya jika status menu "Aktif"
            try {
                $response = Http::withoutVerifying()->get('https://berbagipendidikan.org/api/berita');
    
                if ($response->successful() && isset($response['data'])) {
                    $beritas = $response['data']; // Data berita dari API
                }
            } catch (\Exception $e) {
                // Menangani jika ada error pada API
                Log::error('Error fetching berita: ' . $e->getMessage());
                $beritas = []; // Mengisi dengan array kosong jika gagal mengambil data
            }
        }

        $page    = (int) $request->query('camp_page', 1);   // param unik → camp_page
        $perPage = (int) $request->query('camp_per_page', 5);

        $campaignMenu = SettingsMenu::find(16);
        $campaigns    = collect();
        $lastPage     = $page;  // fallback

        if ($campaignMenu && $campaignMenu->status == 'Aktif') {
            try {
                // kirim page & per_page ke API
                $response = Http::withoutRedirecting()->get(
                    'https://berbagibahagia.org/api/getcampung',
                    [ 'page' => $page, 'per_page' => $perPage ]
                );

                if ($response->successful()) {
                    $campaigns = collect($response['data']);

                    // cek apakah ada meta halaman dari API
                    $meta = $response->json('meta');
                    if ($meta && isset($meta['last_page'])) {
                        $lastPage = (int) $meta['last_page'];
                    } else {
                        // perkiraan kasar bila API tdk kirim meta
                        $lastPage = $campaigns->count() === $perPage ? $page + 4 : $page;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error fetching Campaign : '.$e->getMessage());
            }
        }

        $homeKilau = HomeKilau::where('status_home_kilau', HomeKilau::HOME_KILAU_AKTIF)->get();

        $iklanKilau = IklanKilau::where('status_kilau', IklanKilau::IKLAN_KILAU_AKTIF)
                        ->with(['iklanKilauLists' => function($query) {
                            $query->where('status_iklan_kilau_list', IklanKilauList::IKLAN_KILAU_LIST_AKTIF); // hanya mengambil yang aktif
                        }])
                        ->get();
        
        $donasiiklan = KilauIklan::where('statuskilauiklan', KilauIklan::DONASI_IKLAN_AKTIVE)->get();


        return view('LandingPageKilau.index', compact('testimonis', 'faqs', 'mitras', 'testimoniMenu', 'faqMenu', 'mitraMenu', 'donasiiklan', 'programMenu', 'programs', 'beritaMenu', 'beritas', 'campaignMenu', 'campaigns', 'timelineMenu', 'timelins', 'tentangMenu', 'tentangs', 'homeKilau', 'iklanKilau', 'selectedProgram', 'page', 'perPage', 'lastPage', 'jumlahViewLanding'));
    }
    
    public function getProgramInfo(Request $request)
    {
        $programJudul = $request->program;  // Menggunakan judul untuk pencarian

        // Ambil informasi program berdasarkan judul
        $program = Program::where('judul', $programJudul)->first();

        if ($program) {
            return response()->json([
                'judul' => $program->judul,
                'description' => $program->deskripsi,
                'success_percentage' => $program->program_yang_berhasil_dijalankan,
                'target' => $program->jumlah_target_tercapai
            ]);
        }

        return response()->json(['message' => 'Program tidak ditemukan'], 404);
    }

    public function donasi(Request $request)
    {
        // Validasi input form
        $request->validate([
            'nama' => 'required|string|max:255',
            'type_donasi' => 'required|in:1,2', 
            'total' => 'required|numeric|min:1',
            'id_program' => 'nullable|exists:programs,id',
            'opsional_umum' => 'nullable|in:1,2', 

            'no_hp'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'feedback'     => 'nullable|string|max:500',
        ]);

        if ($request->type_donasi == 1 && !$request->id_program) {
            return response()->json(['message' => 'Program donasi belum dipilih.'], 400);
        }

        // Simpan donasi ke tabel donasikilau
        $donasi = new DonasiKilau();
        $donasi->type_donasi = $request->input('type_donasi');
        $donasi->nama = $request->input('nama');
        $donasi->total_donasi = $request->input('total');
        // Status awal adalah pending (menunggu pembayaran)
        $donasi->status_donasi = DonasiKilau::DONASI_PENDING;
        
        // Jika donasi jenis program, simpan id_program
        if ($request->input('type_donasi') == DonasiKilau::TYPE_DONASI_PROGRAM) {
            $donasi->id_program = $request->input('id_program');
        }
        
        // Jika donasi jenis umum, simpan opsional_umum jika ada
        if ($request->input('type_donasi') == DonasiKilau::TYPE_DONASI_UMUM) {
            $donasi->opsional_umum = $request->input('opsional_umum'); // Pastikan nilai opsional_umum disertakan
        }
        
        foreach (['no_hp','email','feedback'] as $field) {
            if ($request->filled($field)) {
                $donasi->$field = $request->$field;
            }
        }
        
        // Simpan data donasi
        $donasi->save();

        return response()->json([
            'message' => 'Donasi berhasil disimpan, silakan lanjutkan pembayaran.',
            'donasi_id' => $donasi->id
        ]);
    }
    
    public function updateStatusDonasi(Request $request, $id)  // Menambahkan parameter $id untuk menerima parameter URL
    {
        Log::info('Update status donasi request', $request->all());

        $request->validate([
            'status' => 'required|in:' . DonasiKilau::DONASI_PENDING . ',' . DonasiKilau::DONASI_AKTIVE,
        ]);

        // Mencari donasi berdasarkan parameter $id
        $donasi = DonasiKilau::find($id);  // Menggunakan $id dari URL

        if (!$donasi) {
            return response()->json(['message' => 'Donasi tidak ditemukan.'], 404);
        }

        $donasi->status_donasi = $request->status;  // Menetapkan status berdasarkan input
        $donasi->save();

        return response()->json([
            'message' => 'Status donasi berhasil diperbarui.'
        ]);
    }


    public function handleMidtransCallback(Request $request)
    {
        // Konfigurasi untuk memverifikasi signature
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $orderId = $request['order_id'];
        $statusCode = $request['status_code'];
        $grossAmount = $request['gross_amount'];

        // Buat hashed signature untuk validasi
        $hashed = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        // Validasi jika signature sesuai
        if ($hashed == $request['signature_key']) {
            // Proses jika status transaksi adalah settlement (berhasil)
            if ($request['transaction_status'] == 'settlement') {
                // Temukan transaksi berdasarkan order_id
                // $donasi = DonasiKilau::where('id', $orderId)->first();
                $orderId = str_replace('donasi-', '', $request['order_id']);
                $donasi = DonasiKilau::find($orderId);

                if ($donasi) {
                    // Perbarui status donasi menjadi Aktif
                    $donasi->status_donasi = DonasiKilau::DONASI_AKTIVE; 
                    $donasi->save();
                }
            } else if ($request['transaction_status'] == 'pending') {
                // Jika status transaksi masih pending
                $donasi = DonasiKilau::where('id', $orderId)->first();
                if ($donasi) {
                    $donasi->status_donasi = DonasiKilau::DONASI_PENDING; // Menunggu pembayaran
                    $donasi->save();
                }
            } else if ($request['transaction_status'] == 'failed') {
                // Jika transaksi gagal
                $donasi = DonasiKilau::where('id', $orderId)->first();
                if ($donasi) {
                    $donasi->status_donasi = DonasiKilau::DONASI_PENDING; // Kembalikan status ke pending
                    $donasi->save();
                }
            }
        }
    }

    public function testimoniCreate(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'pekerjaan' => 'nullable',
            'komentar' => 'required',
            'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $testimoni = new Testimoni;
        $testimoni->nama = $request->nama;
        $testimoni->pekerjaan = $request->pekerjaan;
        $testimoni->komentar = $request->komentar;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('testimoni', 'public'); 
            $testimoni->file = $path;
        }

        $testimoni->statuss_testimoni = Testimoni::TESTIMONI_TIDAK_AKTIF; // Default Tidak Aktif
        $testimoni->save();

        return redirect()->route('home')->with('success', 'Testimoni created successfully.');
    }
}