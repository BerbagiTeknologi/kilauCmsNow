<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\MitraDonatur;

class MitraDonaturController extends Controller
{
    // Menampilkan daftar mitra donatur
    public function index()
    {
        $mitras = MitraDonatur::all();

        return view('AdminPage.MitraDonatur.index', compact('mitras'));
    }

    // Menambahkan mitra donatur baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Buat mitra baru
        $mitra = new MitraDonatur();
        $mitra->nama_mitra = $request->nama_mitra;
        $mitra->status_mitra = MitraDonatur::MITRA_AKTIF; // Default status aktif

        // Jika ada file yang diunggah, simpan ke storage
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('mitra_donatur', 'public');
            $mitra->file = $path;
        }

        $mitra->save();

        return redirect()->route('mitra')->with('success', 'Mitra Donatur berhasil ditambahkan.');
    }

    // Mengupdate data mitra donatur
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $mitra = MitraDonatur::findOrFail($id);
        $mitra->nama_mitra = $request->nama_mitra;

        // Jika ada file baru diunggah
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($mitra->file && Storage::disk('public')->exists($mitra->file)) {
                Storage::disk('public')->delete($mitra->file);
            }

            // Simpan file baru ke storage
            $path = $request->file('file')->store('mitra_donatur', 'public');
            $mitra->file = $path;
        }

        $mitra->save();

        return redirect()->route('mitra')->with('success', 'Mitra Donatur berhasil diperbarui.');
    }

    // Toggle status aktif/nonaktif mitra
    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_mitra' => 'required|in:1,2',
        ]);

        $mitra = MitraDonatur::findOrFail($id);
        $mitra->status_mitra = $request->status_mitra;
        $mitra->save();

        return redirect()->route('mitra')->with('success', 'Status Mitra Donatur berhasil diperbarui.');
    }

    // Menghapus mitra donatur
    public function destroy($id)
    {
        $mitra = MitraDonatur::findOrFail($id);

        // Hapus file jika ada
        if ($mitra->file && Storage::disk('public')->exists($mitra->file)) {
            Storage::disk('public')->delete($mitra->file);
        }

        $mitra->delete();

        return redirect()->route('mitra')->with('success', 'Mitra Donatur berhasil dihapus.');
    }
}
