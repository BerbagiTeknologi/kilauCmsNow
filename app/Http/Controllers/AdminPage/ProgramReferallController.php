<?php

namespace App\Http\Controllers\AdminPage;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\ProgramReferral;
use App\Models\ReferralWithdrawal;
use App\Models\ReferralWithdrawalHistory;
use App\Http\Controllers\Controller;

class ProgramReferallController extends Controller
{
    public function index()
    {
        $referrals = ProgramReferral::with(['program', 'withdrawals.histories'])->latest()->get();
        return view('AdminPage.Program.ProgramReferral.index', compact('referrals'));
    }

    public function approve($id, Request $request)
    {
        $withdrawal = ReferralWithdrawal::findOrFail($id);

        if ($withdrawal->status !== ReferralWithdrawal::PENDING) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $withdrawal->update([
            'status' => ReferralWithdrawal::APPROVED,
            'processed_at' => Carbon::now()
        ]);

        ReferralWithdrawalHistory::create([
            'referral_withdrawal_id' => $withdrawal->id,
            'status' => 'approved',
            'note' => 'Disetujui oleh admin.',
            'changed_at' => now()
        ]);

        $referral = $withdrawal->programReferral;
        $referral->click_count = 0;
        $referral->save();

        return back()->with('success', 'Pengajuan pencairan berhasil disetujui.');
    }

    public function reject($id, Request $request)
    {
        $withdrawal = ReferralWithdrawal::findOrFail($id);

        if ($withdrawal->status !== ReferralWithdrawal::PENDING) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $withdrawal->update([
            'status' => ReferralWithdrawal::REJECTED,
            'processed_at' => Carbon::now()
        ]);

        ReferralWithdrawalHistory::create([
            'referral_withdrawal_id' => $withdrawal->id,
            'status' => 'rejected',
            'note' => 'Ditolak oleh admin.',
            'changed_at' => now()
        ]);

        return back()->with('success', 'Pengajuan pencairan berhasil ditolak.');
    }
}
