<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Models\DonasiKilau;
use App\Models\Program;
use App\Models\ReferralCode;
use App\Models\User;
use App\Services\ReferralCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PointReferallController extends Controller
{
    /* ─────────────────────────────────────────────── */
    // public function pointReferall()
    // {
    //     $referralKey = session('user_referral_code');
    //     abort_if(!$referralKey, 403, 'Silakan login');

    //     $referrals = ProgramReferral::with('program')
    //                 ->where('referer_name', $referralKey)
    //                 ->get();

    //     $totalUang = $referrals->sum(fn($r) => $r->click_count * 1000);

    //     // Ambil status withdrawal pending dari setiap referral
    //     $withdrawals = ReferralWithdrawal::whereIn('program_referral_id', $referrals->pluck('id'))
    //                     ->where('status', ReferralWithdrawal::PENDING)
    //                     ->get()
    //                     ->keyBy('program_referral_id');

    //     return view('LandingPageKilau.Components.point-referal', [
    //         'referrals'   => $referrals,
    //         'withdrawals' => $withdrawals,
    //         'totalUang'   => $totalUang,
    //         'userName'    => session('user_name'),
    //         'userEmail'   => session('user_email'),
    //     ]);
    // }
    
    // public function pointReferall()
    // {
    //     $referralKey = session('user_referral_code');
    //     abort_if(!$referralKey, 403, 'Silakan login');
    
    //     // ambil semua referral milik user
    //     $referrals = ProgramReferral::with('program')
    //                 ->where('referer_name', $referralKey)
    //                 ->get();
    
    //     $totalUang = $referrals->sum(fn ($r) => $r->click_count * 1000);
    
    //     /**
    //      * Ambil withdrawal TERBARU tiap-referral, apa pun statusnya.
    //      * ->latest() di-group pakai keyBy → mudah diakses di blade.
    //      */
    //     $withdrawals = ReferralWithdrawal::whereIn(
    //                         'program_referral_id',
    //                         $referrals->pluck('id')
    //                     )
    //                     ->latest('requested_at')
    //                     ->get()
    //                     ->keyBy('program_referral_id');
    
    //     return view('LandingPageKilau.Components.point-referal', [
    //         'referrals'   => $referrals,
    //         'withdrawals' => $withdrawals,
    //         'totalUang'   => $totalUang,
    //         'userName'    => session('user_name'),
    //         'userEmail'   => session('user_email'),
    //     ]);
    // }

    public function pointReferall(ReferralCodeService $referralCodeService)
    {
        $localUser = User::query()->find(session('local_user_id'));
        $referralCode = $localUser
            ? $referralCodeService->getOrCreateForUser($localUser)
            : null;
        $affiliateSub = $referralCode?->code ?: (session('user_sub') ?? session('user_id'));
        abort_if(!$affiliateSub, 403, 'Silakan login');

        $affiliateSub = (string) $affiliateSub;
        $affiliateSubEncoded = urlencode($affiliateSub);
        $trackingKeys = array_values(array_unique(array_filter([
            $affiliateSub,
            (string) session('user_sub'),
            (string) session('user_id'),
            (string) session('local_user_id'),
        ], fn ($value) => trim((string) $value) !== '')));
        $referralType = $referralCode?->referral_type ?? ReferralCode::TYPE_CMS_USER;
        $referralTypeLabel = $referralType === ReferralCode::TYPE_KILAU_EMPLOYEE
            ? 'Karyawan Kilau'
            : 'User CMS';
        $employeeReferral = $referralCode?->isKilauEmployee()
            ? [
                'name' => $referralCode->name_snapshot,
                'position' => $referralCode->position_snapshot,
                'photo_url' => $referralCode->photo_url_snapshot,
            ]
            : null;

        $baseUrl = url('/');

        $shareLinkUmum = $baseUrl . '?aff=' . $affiliateSubEncoded;

        $programs = Program::query()
            ->select('id', 'judul', 'status_program')
            ->where('status_program', Program::PROGRAM_AKTIF)
            ->orderByDesc('id')
            ->get();

        $affiliateColumnReady = Schema::hasColumn('donasikilau', 'affiliate_sub');
        $referralColumnReady = Schema::hasColumn('donasikilau', 'referral_code');

        $totalDonasi = 0;
        $totalTransaksi = 0;
        $totalPending = 0;
        $totalAktif = 0;
        $donasiTerbaru = collect();
        $donasiTerbaruLimit = 20;

        if ($affiliateColumnReady) {
            $query = function () use ($trackingKeys, $referralColumnReady) {
                return DonasiKilau::query()
                    ->where(function ($query) use ($trackingKeys, $referralColumnReady) {
                        if ($referralColumnReady) {
                            $query
                                ->whereIn('referral_code', $trackingKeys)
                                ->orWhereIn('affiliate_sub', $trackingKeys);

                            return;
                        }

                        $query->whereIn('affiliate_sub', $trackingKeys);
                    });
            };

            $totalDonasi = (float) $query()
                ->where('status_donasi', DonasiKilau::DONASI_AKTIVE)
                ->sum('total_donasi');
            $totalTransaksi = $query()->count();
            $totalPending = $query()
                ->where('status_donasi', DonasiKilau::DONASI_PENDING)
                ->count();
            $totalAktif = $query()
                ->where('status_donasi', DonasiKilau::DONASI_AKTIVE)
                ->count();

            $donasiTerbaru = $query()
                ->with(['program:id,judul'])
                ->select('id', 'type_donasi', 'opsional_umum', 'id_program', 'nama', 'total_donasi', 'status_donasi', 'created_at')
                ->latest('created_at')
                ->take($donasiTerbaruLimit)
                ->get();
        }

        return view('LandingPageKilau.Components.point-referal', [
            'affiliateSub' => $affiliateSub,
            'referralType' => $referralType,
            'referralTypeLabel' => $referralTypeLabel,
            'employeeReferral' => $employeeReferral,
            'shareLinkUmum' => $shareLinkUmum,
            'programs' => $programs,
            'affiliateColumnReady' => $affiliateColumnReady,
            'totalDonasi' => $totalDonasi,
            'totalTransaksi' => $totalTransaksi,
            'totalPending' => $totalPending,
            'totalAktif' => $totalAktif,
            'donasiTerbaru' => $donasiTerbaru,
            'donasiTerbaruLimit' => $donasiTerbaruLimit,
            'userName' => session('user_name'),
            'userEmail' => session('user_email'),
        ]);
    }

    public function storeWithdrawal(Request $request)
    {
        return back()->with('error', 'Fitur pencairan referral tidak digunakan lagi.');
    }
}
