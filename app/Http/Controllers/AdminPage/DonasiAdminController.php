<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPage\UpdateKm12MappingRequest;
use App\Models\CmsProgramKm12Mapping;
use App\Models\DonasiKilau;
use App\Models\Program;
use App\Services\DonationExpirationService;
use App\Services\IntegrationOutboxService;
use App\Services\Km12ProgramClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;

class DonasiAdminController extends Controller
{
    public function index(Km12ProgramClient $km12ProgramClient)
    {
        app(DonationExpirationService::class)->expirePendingOlderThanOneHour();

        $donasi = $this->donasiQuery()
            ->latest('created_at')
            ->get();

        $summary = [
            'total_transaksi' => DonasiKilau::count(),
            'total_nominal' => DonasiKilau::sum('total_donasi'),
            'total_pending' => DonasiKilau::where('status_donasi', DonasiKilau::DONASI_PENDING)->count(),
            'total_aktif' => DonasiKilau::where('status_donasi', DonasiKilau::DONASI_AKTIVE)->count(),
            'km12_failed' => Schema::hasColumn('donasikilau', 'km12_sync_status')
                ? DonasiKilau::where('km12_sync_status', 'failed')->count()
                : 0,
            'km12_unsynced' => Schema::hasColumn('donasikilau', 'km12_sync_status')
                ? DonasiKilau::where('status_donasi', DonasiKilau::DONASI_AKTIVE)
                    ->where(function ($query) {
                        $query->whereNull('km12_sync_status')
                            ->orWhere('km12_sync_status', '!=', 'synced');
                    })
                    ->count()
                : 0,
        ];

        $years = DonasiKilau::selectRaw('YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $programMappings = Program::query()
            ->when(Schema::hasTable('cms_program_km12_mappings'), fn ($query) => $query->with('km12Mapping'))
            ->select('id', 'judul', 'status_program')
            ->orderByDesc('id')
            ->get();

        $km12ProgramOptions = $km12ProgramClient->options(limit: 200);

        return view('AdminPage.Donasi.index', compact(
            'donasi',
            'summary',
            'years',
            'programMappings',
            'km12ProgramOptions',
        ));
    }

    public function chartData(Request $request)
    {
        app(DonationExpirationService::class)->expirePendingOlderThanOneHour();

        $group = $request->input('group', 'monthly');
        $query = DB::table('donasikilau');

        switch ($group) {
            case 'daily':
                $rows = $query->selectRaw('DATE(created_at) AS label, SUM(total_donasi) AS total')
                    ->groupBy('label')
                    ->orderBy('label')
                    ->get();
                break;

            case 'yearly':
                $rows = $query->selectRaw('YEAR(created_at) AS label, SUM(total_donasi) AS total')
                    ->groupBy('label')
                    ->orderBy('label')
                    ->get();
                break;

            default:
                $rows = $query->selectRaw("DATE_FORMAT(created_at, '%Y-%m') AS label, SUM(total_donasi) AS total")
                    ->groupBy('label')
                    ->orderBy('label')
                    ->get();
                break;
        }

        return Response::json($rows);
    }

    public function filter(Request $request)
    {
        $data = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'status' => ['nullable', 'integer', 'in:1,2,3'],
        ]);

        app(DonationExpirationService::class)->expirePendingOlderThanOneHour();

        $query = $this->donasiQuery();
        $keyword = trim((string) ($data['keyword'] ?? ''));

        if ($keyword !== '') {
            $query->where(function ($query) use ($keyword) {
                $query->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('no_hp', 'like', "%{$keyword}%")
                    ->orWhere('feedback', 'like', "%{$keyword}%")
                    ->orWhereHas('program', function ($query) use ($keyword) {
                        $query->where('judul', 'like', "%{$keyword}%");
                    });
            });
        }

        if (!empty($data['month'])) {
            $query->whereMonth('created_at', $data['month']);
        }

        if (!empty($data['year'])) {
            $query->whereYear('created_at', $data['year']);
        }

        if (!empty($data['status'])) {
            $query->where('status_donasi', $data['status']);
        }

        $donasi = $query->latest('created_at')->get();
        $totals = $donasi
            ->groupBy(fn (DonasiKilau $item) => $item->created_at->format('Y-m'))
            ->map(fn ($items) => $items->sum('total_donasi'));

        return response()->json([
            'data' => $donasi->values(),
            'labels' => $totals->keys()->values(),
            'totals' => $totals->values(),
        ]);
    }

    public function destroy(DonasiKilau $donasi)
    {
        $donasi->delete();

        return redirect()->route('admin.donasi.index')->with('success', 'Donasi berhasil dihapus.');
    }

    public function retryKm12Sync(DonasiKilau $donasi, IntegrationOutboxService $outboxService)
    {
        if ((int) $donasi->status_donasi !== DonasiKilau::DONASI_AKTIVE) {
            return back()->with('error', 'Hanya donasi berdonasi yang bisa disinkronkan ke KM12.');
        }

        if (! config('km12_service.donation_sync_enabled', false)) {
            return back()->with('error', 'Pengiriman donasi KM12 masih dinonaktifkan.');
        }

        if (! Schema::hasTable('integration_outbox_messages')) {
            return back()->with('error', 'Tabel outbox integrasi belum tersedia.');
        }

        $retried = $outboxService->retryAggregate('donation.paid', 'donation', $donasi->id);

        if ($retried) {
            $donasi->forceFill([
                'km12_sync_status' => 'pending',
                'km12_sync_error' => null,
            ])->save();
        }

        return $retried
            ? back()->with('success', 'Pengiriman KM12 dijadwalkan ulang melalui outbox.')
            : back()->with('error', 'Pesan outbox belum tersedia atau sudah terkirim.');
    }

    public function updateProgramMapping(UpdateKm12MappingRequest $request, Program $program)
    {
        if (! Schema::hasTable('cms_program_km12_mappings')) {
            return back()->with('error', 'Tabel mapping program KM12 belum tersedia. Jalankan migration CMS.');
        }

        $validated = $request->validated();

        $programId = $validated['km12_program_penerimaan_id'] ?? null;

        if (! $programId) {
            CmsProgramKm12Mapping::query()
                ->where('cms_program_id', $program->id)
                ->delete();

            return back()->with('success', 'Mapping program KM12 dihapus.');
        }

        CmsProgramKm12Mapping::updateOrCreate(
            ['cms_program_id' => $program->id],
            [
                'km12_program_penerimaan_id' => $programId,
                'km12_sumber_dana_id' => $validated['km12_sumber_dana_id'] ?? null,
                'km12_program_name' => $validated['km12_program_name'] ?? null,
                'km12_sumber_dana_name' => $validated['km12_sumber_dana_name'] ?? null,
                'is_active' => $request->boolean('is_active', true),
                'synced_at' => now(),
            ],
        );

        return back()->with('success', 'Mapping program KM12 berhasil disimpan.');
    }

    private function donasiQuery()
    {
        return DonasiKilau::with('program:id,judul');
    }
}
