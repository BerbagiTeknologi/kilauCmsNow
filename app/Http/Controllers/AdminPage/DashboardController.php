<?php

namespace App\Http\Controllers\AdminPage;

use Carbon\Carbon;
use App\Models\Kontak;
use App\Models\Testimoni;
use App\Models\ViewTraffic;
use App\Models\MitraDonatur;
use Illuminate\Http\Request;
use App\Models\ProgramReferral;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function dashboard(Request $request) {
        // Menghitung jumlah Testimoni yang Aktif
        $totalTestimoni = Testimoni::where('statuss_testimoni', Testimoni::TESTIMONI_AKTIF)->count();

        // Mengambil jumlah berita dari API
        $totalBerita = 0; // Default jika API gagal
        try {
            $response = Http::get('https://berbagipendidikan.org/api/berita/counting');
            if ($response->successful() && isset($response['total_berita'])) {
                $totalBerita = $response['total_berita'];
            }
        } catch (\Exception $e) {
            $totalBerita = 'Error fetching data'; // Jika terjadi error saat mengambil API
        }

        // Menghitung jumlah Mitra Donatur
        $totalMitraDonatur = MitraDonatur::count();

        // Menghitung jumlah Kantor Cabang (dari tabel Kontak)
        $totalKantorCabang = Kontak::count();

        $rekapKunjungan = ViewTraffic::selectRaw('MONTH(viewed_at) as bulan, COUNT(*) as total')
            ->whereYear('viewed_at', now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $bulanKunjungan = $rekapKunjungan->pluck('bulan')->toArray();
        $totalKunjungan = $rekapKunjungan->pluck('total')->toArray();
        
        $perPage = $request->input('per_page', 10);
        $trafficQuery = ViewTraffic::orderByDesc('viewed_at');
        
        $start = $end = null;
        if ($request->filled('date')) {
            $start = Carbon::createFromFormat('Y-m-d', $request->date)
                           ->startOfDay();          // 00:00:00 lokal
            $end   = $start->copy()->endOfDay();    // 23:59:59
            $trafficQuery->whereBetween('viewed_at', [$start, $end]);
        }
        
        /* ------- detail log ------- */
        $landingLogs = $trafficQuery->paginate($perPage)
                                    ->withQueryString();
        
        /* ------- rekap per tipe & per bulanâ€”ikut filter ------- */
        $base = ViewTraffic::query();
        if ($start && $end) {
            $base->whereBetween('viewed_at', [$start, $end]);
        }
                    
        $totalLandingPage       = ViewTraffic::where('type', ViewTraffic::TYPE_LANDINGPAGE)->count();
        $totalFormDonasi        = ViewTraffic::where('type', ViewTraffic::TYPE_FORM_DONASI)->count();
        $totalFormDonasiProgram = ViewTraffic::where('type', ViewTraffic::TYPE_FORM_DONASI_PROGRAM)->count();

        $referralList = ProgramReferral::with(['program:id,judul'])
            ->orderByDesc('created_at')
            ->paginate(10)               // ubah per halaman sesuai kebutuhan
            ->withQueryString();

        return view('AdminPage.dashboard', compact('totalTestimoni', 'totalBerita', 'totalMitraDonatur',
        'totalKantorCabang', 'bulanKunjungan', 'totalKunjungan', 'landingLogs', 'totalLandingPage',
    'totalFormDonasi', 'totalFormDonasiProgram', 'referralList'));
    }
    
    public function trafficData(Request $request)
    {
        $group = $request->input('group', 'monthly');   // daily | monthly | yearly
        $type  = $request->input('type');              // optional: landingpage, form_donasi, dst
    
        $query = ViewTraffic::query();
    
        if ($type) {
            $query->where('type', $type);
        }
    
        switch ($group) {
            case 'daily':
                $data = $query->selectRaw("
                            DATE(viewed_at)   AS label,
                            COUNT(*)          AS total
                        ")
                        ->groupByRaw('DATE(viewed_at)')
                        ->orderByRaw('DATE(viewed_at)')
                        ->get();
                break;
    
            case 'yearly':
                $data = $query->selectRaw("
                            YEAR(viewed_at)   AS label,
                            COUNT(*)          AS total
                        ")
                        ->groupByRaw('YEAR(viewed_at)')
                        ->orderByRaw('YEAR(viewed_at)')
                        ->get();
                break;
    
            default: // monthly
                $data = $query->selectRaw("
                            DATE_FORMAT(viewed_at,'%Y-%m') AS label,
                            COUNT(*)                       AS total
                        ")
                        ->groupByRaw("DATE_FORMAT(viewed_at,'%Y-%m')")
                        ->orderByRaw("DATE_FORMAT(viewed_at,'%Y-%m')")
                        ->get();
                break;
        }
    
        return Response::json($data);  // [{label:'2025-06-17', total:123}, ...]
    }
    
    


    
}
