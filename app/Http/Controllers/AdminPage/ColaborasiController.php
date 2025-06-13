<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Models\Colaborasi;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ColaborasiController extends Controller
{
    public function colaborasi() {
        $colaborasi = Colaborasi::with('program')->get();
        return view('AdminPage.Colaborasi.index', compact('colaborasi'));
    }

    public function colaborasiShow($id)
    {
        $colaborasi = Colaborasi::with('program')->findOrFail($id);
        return response()->json($colaborasi);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'balasan' => 'required|string|max:1000',
        ]);

        $colaborasi = Colaborasi::findOrFail($id);
        $colaborasi->balasan = $request->balasan;
        $colaborasi->status_progress_kerjasama = 'Closed'; 
        $colaborasi->save();

        $data = [
            'nama' => $colaborasi->nama_lengkap,
            'balasan' => $request->balasan,
        ];

        Mail::send('Emails.balasan', $data, function ($message) use ($colaborasi) {
            $message->to($colaborasi->alamat_email)
                    ->subject("Balasan dari Kilau Indonesia - Kolaborasi")
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });

        return redirect()->route('colaborasi')->with('success', 'Balasan berhasil dikirim dan status kerja sama diperbarui.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_kerjasama' => 'required|in:1,2',
        ]);

        $colaborasi = Colaborasi::findOrFail($id);
        $colaborasi->status_kerjasama = $request->status_kerjasama;
        $colaborasi->save();

        return redirect()->route('colaborasi')->with('success', 'Status Kolaborasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $colaborasi = Colaborasi::findOrFail($id);
        $colaborasi->delete();

        return redirect()->route('colaborasi')->with('success', 'Data kolaborasi berhasil dihapus.');
    }

   

}
