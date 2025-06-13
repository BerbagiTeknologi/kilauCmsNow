<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Models\ProgramReferral;
use App\Models\ReferralWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $referralKey = session('user_referral_code');
        abort_if(!$referralKey, 403, 'Silakan login');

        /* ──────────────────────────
        * 1. Referral milik user
        * ────────────────────────── */
        $referrals = ProgramReferral::with('program')
                    ->where('referer_name', $referralKey)
                    ->get();

        $totalUang = $referrals->sum(fn ($r) => $r->click_count * 1000);

        /* ──────────────────────────
        * 2. Withdrawal terbaru per-referral
        *    (id terbesar / requested_at terbaru)
        * ────────────────────────── */
        $withdrawals = ReferralWithdrawal::whereIn(
                            'program_referral_id',
                            $referrals->pluck('id')
                        )
                        ->orderByDesc('requested_at')   // urutkan dari yang paling baru
                        ->get()
                        ->groupBy('program_referral_id') // kumpulkan per referral
                        ->map->first();                 // ambil elemen pertama (terbaru)

        /*  $withdrawals kini berupa Collection
            key   = program_referral_id
            value = 1 objek ReferralWithdrawal (terbaru)  */

        return view('LandingPageKilau.Components.point-referal', [
            'referrals'   => $referrals,
            'withdrawals' => $withdrawals,
            'totalUang'   => $totalUang,
            'userName'    => session('user_name'),
            'userEmail'   => session('user_email'),
        ]);
    }

    public function storeWithdrawal(Request $request)
    {
        $rules = [
            'program_referral_id' => 'required|exists:program_referrals,id',
            'nama_lengkap'        => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'no_hp'               => 'required|string|max:30',
            'no_rekening'         => 'required|string|max:50',
            'nama_bank'           => 'required|string|max:50',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // pastikan referral milik user
        ProgramReferral::where('id', $request->program_referral_id)
            ->where('referer_name', session('user_referral_code'))
            ->firstOrFail();

        ReferralWithdrawal::create([
            'program_referral_id' => $request->program_referral_id,
            'nama_lengkap'        => $request->nama_lengkap,
            'email'               => $request->email,
            'no_hp'               => $request->no_hp,
            'no_rekening'         => $request->no_rekening,
            'nama_bank'           => $request->nama_bank,
            'status'              => ReferralWithdrawal::PENDING,
            'requested_at'        => now(), 
        ]);

        return back()->with('success', 'Pengajuan pencairan berhasil dikirim. Tunggu verifikasi.');
    }
}
