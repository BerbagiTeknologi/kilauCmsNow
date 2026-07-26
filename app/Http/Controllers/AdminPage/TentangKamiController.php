<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Models\TentangKami;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TentangKamiController extends Controller
{
    public function tentangkami() {
        $tentangKami = TentangKami::all();
        return view('AdminPage.Profile.TentangKami.index', compact('tentangKami'));
    }

    public function tentangkamiCreate(Request $request)
    {
        // Validasi input form
        $request->validate([
            'judul_tentang_kami' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Membuat objek baru untuk tabel TentangKami
        $tentangKami = new TentangKami();
        $tentangKami->judul_tentang_kami = $request->judul_tentang_kami;
        $tentangKami->deskripsi = $request->deskripsi;
        $tentangKami->status_tentang_kami = TentangKami::TENTANG_AKTIF;
    
        // Menangani pengunggahan file jika ada
        if ($request->hasFile('file')) {
            // Menyimpan file di folder 'tentang_kami' dalam storage 'public'
            $path = $request->file('file')->store('tentang_kami', 'public');
            // Menyimpan path file ke kolom 'file'
            $tentangKami->file = $path;
        } else {
            // Jika tidak ada file yang diunggah, set kolom 'file' menjadi null
            $tentangKami->file = null;
        }
    
        // Menyimpan data ke dalam tabel tentang_kami
        $tentangKami->save();
    
        // Mengarahkan kembali ke halaman dengan pesan sukses
        return redirect()->route('profil.tentangkami')->with('success', 'Tentang Kami berhasil ditambahkan.');
    }


    // Mengupdate data tentang kami
    public function tentangkamiEdit(Request $request, $id)
    {
        $request->validate([
            'judul_tentang_kami' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $tentangKami = TentangKami::findOrFail($id);
        $tentangKami->judul_tentang_kami = $request->judul_tentang_kami;
        $tentangKami->deskripsi = $request->deskripsi;

        // Jika ada file baru diunggah
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($tentangKami->file && Storage::disk('public')->exists($tentangKami->file)) {
                Storage::disk('public')->delete($tentangKami->file);
            }

            // Simpan file baru ke storage
            $path = $request->file('file')->store('tentang_kami', 'public');
            $tentangKami->file = $path;
        }

        $tentangKami->save();

        return redirect()->route('profil.tentangkami')->with('success', 'Tentang Kami berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_tentang_kami' => 'required|in:1,2',
        ]);

        $tentangKami = TentangKami::findOrFail($id);
        $tentangKami->status_tentang_kami = $request->status_tentang_kami;
        $tentangKami->save();

        return redirect()->route('profil.tentangkami')->with('success', 'Status Tentang Kami berhasil diperbarui.');
    }

    public function tentangkamiDelete($id) {
        $tentangKami = TentangKami::find($id);
        if ($tentangKami->file && Storage::disk('public')->exists($tentangKami->file)) {
            Storage::disk('public')->delete($tentangKami->file);
        }
        $tentangKami->delete();

        return redirect()->route('profil.tentangkami')->with('success', 'Tentang Kami berhasil dihapus.');
    }
}
