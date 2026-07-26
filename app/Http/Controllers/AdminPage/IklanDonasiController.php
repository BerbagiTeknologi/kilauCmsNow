<?php

namespace App\Http\Controllers\AdminPage;

use App\Models\KilauIklan;
use App\Models\DonasiKilau;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class IklanDonasiController extends Controller
{
    public function iklandonasi() {
        $donasiiklan = KilauIklan::all();
        return view('AdminPage.Profile.DonasiIklan.index', compact('donasiiklan'));
    }

    // Menyimpan data struktur baru
    public function iklandonasiCreate(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'icon_iklan' => 'nullable|string|max:50',
            'name_button_iklan' => 'nullable|string|max:100',
            'link' => 'nullable|string|max:100' 
        ]); 

        $donasiiklan = new KilauIklan();
        $donasiiklan->nama = $request->nama;
        $donasiiklan->icon_iklan = $request->icon_iklan;
        $donasiiklan->name_button_iklan = $request->name_button_iklan;
        $donasiiklan->link = $request->link;
        $donasiiklan->statuskilauiklan = KilauIklan::DONASI_IKLAN_AKTIVE; // Set status default

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('berbagidonasiiklan', 'public');
            $donasiiklan->file = $path;
        }

        $donasiiklan->save();

        return redirect()->route('profil.iklandonasi')->with('success', 'Struktur berhasil ditambahkan.');
    }

    // Mengupdate data struktur
    public function iklandonasiEdit(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'icon_iklan' => 'nullable|string|max:50',
            'name_button_iklan' => 'nullable|string|max:100',
            'link' => 'nullable|string|max:100'
        ]);
    
        $donasiiklan = KilauIklan::findOrFail($id);
        $donasiiklan->nama = $request->nama;
        $donasiiklan->icon_iklan = $request->icon_iklan;
        $donasiiklan->name_button_iklan = $request->name_button_iklan;
        $donasiiklan->link = $request->link;

        // Jika ada file baru diunggah
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($donasiiklan->file && Storage::disk('public')->exists($donasiiklan->file)) {
                Storage::disk('public')->delete($donasiiklan->file);
            }

            // Simpan file baru ke storage
            $path = $request->file('file')->store('berbagidonasiiklan', 'public');
            $donasiiklan->file = $path;
        }

        $donasiiklan->save();

        return redirect()->route('profil.iklandonasi')->with('success', 'Struktur berhasil diperbarui.');
    }

    // Mengubah status struktur (Aktif / Tidak Aktif)
    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'statuskilauiklan' => 'required|in:1,2',
        ]);

        $donasiiklan = KilauIklan::findOrFail($id);
        $donasiiklan->statuskilauiklan = $request->statuskilauiklan;
        $donasiiklan->save();

        return redirect()->route('profil.iklandonasi')->with('success', 'Status Struktur berhasil diperbarui.');
    }

    // Menghapus struktur
    public function iklandonasiDelete($id) {
        $donasiiklan = KilauIklan::find($id);
        if ($donasiiklan->file && Storage::disk('public')->exists($donasiiklan->file)) {
            Storage::disk('public')->delete($donasiiklan->file);
        }
        $donasiiklan->delete();

        return redirect()->route('profil.iklandonasi')->with('success', 'Struktur berhasil dihapus.');
    }
}
