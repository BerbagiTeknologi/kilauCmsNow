<?php

namespace App\Http\Controllers\AdminPage;

use App\Models\Struktur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class StrukturController extends Controller
{
    // Menampilkan daftar struktur
    public function index() {
        $struktur = Struktur::all();
        return view('AdminPage.Profile.Struktur.index', compact('struktur'));
    }

    // Menyimpan data struktur baru
    public function create(Request $request)
    {
        $request->validate([
            'name_judul' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $struktur = new Struktur();
        $struktur->name_judul = $request->name_judul;
        $struktur->status_struktur = Struktur::STRUKTUR_AKTIF; // Set status default

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('struktur', 'public');
            $struktur->file = $path;
        }

        $struktur->save();

        return redirect()->route('profil.struktur')->with('success', 'Struktur berhasil ditambahkan.');
    }

    // Mengupdate data struktur
    public function edit(Request $request, $id)
    {
        $request->validate([
            'name_judul' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $struktur = Struktur::findOrFail($id);
        $struktur->name_judul = $request->name_judul;

        // Jika ada file baru diunggah
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($struktur->file && Storage::disk('public')->exists($struktur->file)) {
                Storage::disk('public')->delete($struktur->file);
            }

            // Simpan file baru ke storage
            $path = $request->file('file')->store('struktur', 'public');
            $struktur->file = $path;
        }

        $struktur->save();

        return redirect()->route('profil.struktur')->with('success', 'Struktur berhasil diperbarui.');
    }

    // Mengubah status struktur (Aktif / Tidak Aktif)
    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_struktur' => 'required|in:1,2',
        ]);

        $struktur = Struktur::findOrFail($id);
        $struktur->status_struktur = $request->status_struktur;
        $struktur->save();

        return redirect()->route('profil.struktur')->with('success', 'Status Struktur berhasil diperbarui.');
    }

    // Menghapus struktur
    public function delete($id) {
        $struktur = Struktur::find($id);
        if ($struktur->file && Storage::disk('public')->exists($struktur->file)) {
            Storage::disk('public')->delete($struktur->file);
        }
        $struktur->delete();

        return redirect()->route('profil.struktur')->with('success', 'Struktur berhasil dihapus.');
    }
}
