<?php

namespace App\Http\Controllers\AdminPage;

use Carbon\Carbon;
use App\Models\Kontak;
use App\Models\Testimoni;
use App\Models\DonasiKilau;
use App\Models\ViewTraffic;
use App\Models\MitraDonatur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB; 

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

        // Ambil semua data donasi (baik umum maupun program)
        $donasi = DonasiKilau::orderBy('created_at', 'desc')->get();

        // Format tanggal dan menambahkan informasi tambahan jika diperlukan
        $donasi = $donasi->map(function($data) {
                $data->formatted_date = $data->created_at->format('d M Y');  // Format to '28 Feb 2025'
                return $data;
        });

        // Ambil data donasi berdasarkan bulan untuk grafik
        $donasiBulan = DonasiKilau::selectRaw('SUM(total_donasi) as total_donasi, MONTH(created_at) as bulan, YEAR(created_at) as tahun')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('bulan', 'tahun')
            ->orderBy('bulan', 'asc')
            ->get();

        // Format data untuk grafik
        $bulan = $donasiBulan->pluck('bulan')->toArray();
        $totalDonasi = $donasiBulan->pluck('total_donasi')->toArray();

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



        return view('AdminPage.dashboard', compact('totalTestimoni', 'totalBerita', 'totalMitraDonatur',
        'totalKantorCabang',   'donasi',   'bulan', 'totalDonasi',  'bulanKunjungan', 'totalKunjungan',    'landingLogs',  'totalLandingPage',
    'totalFormDonasi', 'totalFormDonasiProgram'));
    }
    
    public function donasiData(Request $request)
    {
        $group = $request->input('group', 'monthly');   // daily|monthly|yearly
        $q     = DB::table('donasikilau');              // ← BUKAN model
    
        switch ($group) {
            case 'daily':        // 2025-06-17
                $rows = $q->selectRaw('DATE(created_at)  AS label,
                                       SUM(total_donasi) AS total')
                            ->groupBy('label')
                            ->orderBy('label')
                            ->get();
                break;
    
            case 'yearly':       // 2025
                $rows = $q->selectRaw('YEAR(created_at)  AS label,
                                       SUM(total_donasi) AS total')
                            ->groupBy('label')
                            ->orderBy('label')
                            ->get();
                break;
    
            default:             // monthly  ⇒  2025-06
                $rows = $q->selectRaw("DATE_FORMAT(created_at,'%Y-%m') AS label,
                                       SUM(total_donasi)              AS total")
                            ->groupBy('label')
                            ->orderBy('label')
                            ->get();
                break;
        }
    
        return Response::json($rows);
    }

    
     public function deleteDonasi($id)
    {
        DonasiKilau::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Donasi berhasil dihapus.');
    }

    public function filterDonasi(Request $request)
    {
        $month = $request->month;
        $status = $request->status;

        // Filter the Donasi data based on selected month and status
        $query = DonasiKilau::query();

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        if ($status) {
            $query->where('status_donasi', $status);
        }

        $donasi = $query->get();

        // Get unique months and total donation per month for the chart
        $bulan = $donasi->pluck('bulan')->unique();
        $totalDonasi = $donasi->groupBy('bulan')->map(function($donasiGroup) {
            return $donasiGroup->sum('total_donasi');
        });

        return response()->json([
            'data' => $donasi,
            'bulan' => $bulan,
            'totalDonasi' => $totalDonasi
        ]);
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
