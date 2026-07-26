<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Requests\MidtransNotificationRequest;
use App\Http\Requests\StoreDonationRequest;
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
use App\Models\DonasiHistory;
use App\Models\IklanKilauList;
use App\Models\ProgramReferral;
use App\Services\DonationIdentityService;
use App\Services\MidtransNotificationService;
use App\Services\ReferralCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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

        // Redirect ke halaman utama dengan parameter afiliasi agar modal donasi menangkap referer.
        return redirect()->route('home', ['aff' => $referer])->with('scrollToProgram', $id);
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

    public function cekDonatur(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'no_hp' => 'nullable|string|max:20',
        ]);

        if (!$request->filled('email') && !$request->filled('no_hp')) {
            return response()->json(['found' => false]);
        }

        // Prioritas: email dulu, lalu no_hp
        if ($request->filled('email')) {
            $byEmail = DonasiKilau::where('email', $request->email)
                ->orderByDesc('created_at')
                ->first();
            if ($byEmail) {
                return response()->json([
                    'found'  => true,
                    'source' => 'email',
                    'data'   => [
                        'nama'  => $byEmail->nama,
                        'email' => $byEmail->email,
                        'no_hp' => $byEmail->no_hp,
                    ],
                ]);
            }
        }

        if ($request->filled('no_hp')) {
            // Pastikan front-end mengirim no_hp yang sudah dibersihkan dari non-digit (lihat script)
            $byPhone = DonasiKilau::where('no_hp', $request->no_hp)
                ->orderByDesc('created_at')
                ->first();
            if ($byPhone) {
                return response()->json([
                    'found'  => true,
                    'source' => 'no_hp',
                    'data'   => [
                        'nama'  => $byPhone->nama,
                        'email' => $byPhone->email,
                        'no_hp' => $byPhone->no_hp,
                    ],
                ]);
            }
        }

        return response()->json(['found' => false]);
    }

    public function donasi(
        StoreDonationRequest $request,
        ReferralCodeService $referralCodeService,
        DonationIdentityService $donationIdentityService
    )
    {
        $data = $request->validated();

        if ((int) $data['type_donasi'] === DonasiKilau::TYPE_DONASI_PROGRAM && empty($data['id_program'])) {
            return response()->json(['message' => 'Program donasi belum dipilih.'], 400);
        }

        $donasi = DB::transaction(function () use (
            $request,
            $data,
            $referralCodeService,
            $donationIdentityService
        ): DonasiKilau {
            $identity = $donationIdentityService->resolve($request, [
                'nama' => $data['nama'],
                'email' => $data['email'] ?? null,
                'no_hp' => $data['no_hp'] ?? null,
            ]);

            $donasi = new DonasiKilau();
            $donasi->type_donasi = $data['type_donasi'];
            $donasi->nama = $identity['transaction_name'];
            $donasi->total_donasi = $data['total'];
            $donasi->status_donasi = DonasiKilau::DONASI_PENDING;
            $donasi->donor_source = $identity['source'];
            $donasi->external_donor_id = $identity['external_id'];
            $donasi->is_anonymous = $identity['is_anonymous'];

            if ((int) $data['type_donasi'] === DonasiKilau::TYPE_DONASI_PROGRAM) {
                $donasi->id_program = $data['id_program'];
            }

            if ((int) $data['type_donasi'] === DonasiKilau::TYPE_DONASI_UMUM) {
                $donasi->opsional_umum = $data['opsional_umum'] ?? null;
            }

            foreach (['no_hp', 'email', 'feedback'] as $field) {
                if (isset($data[$field]) && $data[$field] !== '') {
                    $donasi->$field = $data[$field];
                }
            }

            $affiliateSub = trim((string) (
                ($data['referral_code'] ?? null) ?: ($data['affiliate_sub'] ?? '')
            ));
            if ($affiliateSub !== '') {
                $referralCodeService->applyToDonation($donasi, $affiliateSub);
            }

            $donasi->save();

            DonasiHistory::create([
                'donasikilau_id' => $donasi->id,
                'external_user_id' => $identity['external_id'],
                'status_donasi' => $donasi->status_donasi,
                'total_donasi' => $donasi->total_donasi,
                'feedback' => $data['feedback'] ?? null,
                'token' => null,
            ]);

            return $donasi;
        });

        return response()->json([
            'message' => 'Donasi berhasil disimpan, silakan lanjutkan pembayaran.',
            'donasi_id' => $donasi->id,
            'payment_verification_url' => $this->localPaymentVerificationEnabled()
                ? URL::temporarySignedRoute(
                    'donasi.verify-payment',
                    now()->addHours(2),
                    ['donasi' => $donasi->id],
                )
                : null,
        ]);
    }

    public function verifyMidtransPayment(
        DonasiKilau $donasi,
        MidtransNotificationService $midtransNotificationService
    )
    {
        if (! $this->localPaymentVerificationEnabled()) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        try {
            $result = $midtransNotificationService->verifyDonation($donasi);
        } catch (HttpExceptionInterface $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        }

        return response()->json([
            'message' => 'Midtrans payment status verified.',
            'is_paid' => $result['is_paid'],
            'donation_status' => (int) $result['donasi']->status_donasi,
        ]);
    }

    public function handleMidtransCallback(
        MidtransNotificationRequest $request,
        MidtransNotificationService $midtransNotificationService
    )
    {
        try {
            $midtransNotificationService->process($request->validated());
        } catch (HttpExceptionInterface $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        }

        return response()->json([
            'message' => 'Midtrans notification processed.',
        ]);
    }

    private function localPaymentVerificationEnabled(): bool
    {
        return app()->environment(['local', 'testing'])
            && config('services.midtrans.local_verification_enabled', false);
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
