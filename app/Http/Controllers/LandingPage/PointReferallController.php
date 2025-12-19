<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Models\DonasiKilau;
use App\Models\Program;
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

    public function pointReferall()
    {
        $affiliateSub = session('user_sub') ?? session('user_id');
        abort_if(!$affiliateSub, 403, 'Silakan login');

        $affiliateSub = (string) $affiliateSub;
        $affiliateSubEncoded = urlencode($affiliateSub);

        $baseUrl = url('/');

        $shareLinkUmum = $baseUrl . '?aff=' . $affiliateSubEncoded;

        $programs = Program::query()
            ->select('id', 'judul', 'status_program')
            ->where('status_program', Program::PROGRAM_AKTIF)
            ->orderByDesc('id')
            ->get();

        $affiliateColumnReady = Schema::hasColumn('donasikilau', 'affiliate_sub');

        $totalDonasi = 0;
        $totalTransaksi = 0;
        $totalPending = 0;
        $totalAktif = 0;
        $donasiTerbaru = collect();

        if ($affiliateColumnReady) {
            $totalDonasi = (float) DonasiKilau::where('affiliate_sub', $affiliateSub)->sum('total_donasi');
            $totalTransaksi = DonasiKilau::where('affiliate_sub', $affiliateSub)->count();
            $totalPending = DonasiKilau::where('affiliate_sub', $affiliateSub)
                ->where('status_donasi', DonasiKilau::DONASI_PENDING)
                ->count();
            $totalAktif = DonasiKilau::where('affiliate_sub', $affiliateSub)
                ->where('status_donasi', DonasiKilau::DONASI_AKTIVE)
                ->count();

            $donasiTerbaru = DonasiKilau::with(['program:id,judul'])
                ->where('affiliate_sub', $affiliateSub)
                ->select('id', 'type_donasi', 'opsional_umum', 'id_program', 'nama', 'total_donasi', 'status_donasi', 'created_at')
                ->latest('created_at')
                ->take(20)
                ->get();
        }

        return view('LandingPageKilau.Components.point-referal', [
            'affiliateSub' => $affiliateSub,
            'shareLinkUmum' => $shareLinkUmum,
            'programs' => $programs,
            'affiliateColumnReady' => $affiliateColumnReady,
            'totalDonasi' => $totalDonasi,
            'totalTransaksi' => $totalTransaksi,
            'totalPending' => $totalPending,
            'totalAktif' => $totalAktif,
            'donasiTerbaru' => $donasiTerbaru,
            'userName' => session('user_name'),
            'userEmail' => session('user_email'),
        ]);
    }

    public function storeWithdrawal(Request $request)
    {
        return back()->with('error', 'Fitur pencairan referral tidak digunakan lagi.');
    }
}
