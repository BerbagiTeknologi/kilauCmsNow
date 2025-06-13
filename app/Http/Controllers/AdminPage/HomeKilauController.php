<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Models\HomeKilau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeKilauController extends Controller
{
    // Menampilkan data HomeKilau
    public function homekilau() {
        $homeKilau = HomeKilau::all(); // Mengambil semua data dari HomeKilau
        return view('AdminPage.Profile.HomeKilau.index', compact('homeKilau')); // Menampilkan view dengan data
    }

    // Menambahkan data HomeKilau
    public function homekilauCreate(Request $request)
    {
        $request->validate([
            'judul_home' => 'required|string|max:255',
            'file_home' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,cdr|max:5048', // Validasi file gambar
        ]);

        $homeKilau = new HomeKilau();
        $homeKilau->judul_home = $request->judul_home;
        $homeKilau->status_home_kilau = HomeKilau::HOME_KILAU_AKTIF; // Status default adalah Aktif

        // Jika ada file gambar
        if ($request->hasFile('file_home')) {
            $path = $request->file('file_home')->store('home_kilau', 'public'); // Menyimpan gambar di folder 'home_kilau' pada storage public
            $homeKilau->file_home = $path; // Menyimpan path gambar ke database
        }

        $homeKilau->save(); // Menyimpan data ke database

        return redirect()->route('profil.homekilau')->with('success', 'Home Kilau berhasil ditambahkan.'); // Redirect dengan pesan sukses
    }

    // Mengupdate data HomeKilau
    public function homekilauEdit(Request $request, $id)
    {
        $request->validate([
            'judul_home' => 'required|string|max:255',
            'file_home' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,cdr|max:5048', // Validasi file gambar
        ]);

        $homeKilau = HomeKilau::findOrFail($id); // Mengambil data HomeKilau berdasarkan ID
        $homeKilau->judul_home = $request->judul_home;

        // Jika ada file gambar baru yang diunggah
        if ($request->hasFile('file_home')) {
            // Hapus gambar lama jika ada
            if ($homeKilau->file_home && Storage::disk('public')->exists($homeKilau->file_home)) {
                Storage::disk('public')->delete($homeKilau->file_home);
            }

            // Menyimpan gambar baru
            $path = $request->file('file_home')->store('home_kilau', 'public');
            $homeKilau->file_home = $path; // Menyimpan path gambar baru ke database
        }

        $homeKilau->save(); // Menyimpan perubahan ke database

        return redirect()->route('profil.homekilau')->with('success', 'Home Kilau berhasil diperbarui.'); // Redirect dengan pesan sukses
    }

    // Mengubah status HomeKilau
    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_home_kilau' => 'required|in:1,2', // Status hanya bisa 1 (Aktif) atau 2 (Tidak Aktif)
        ]);

        $homeKilau = HomeKilau::findOrFail($id); // Mencari HomeKilau berdasarkan ID
        $homeKilau->status_home_kilau = $request->status_home_kilau; // Mengubah status
        $homeKilau->save(); // Menyimpan perubahan ke database

        return redirect()->route('profil.homekilau')->with('success', 'Status Home Kilau berhasil diperbarui.'); // Redirect dengan pesan sukses
    }

    // Menghapus data HomeKilau
    public function homekilauDelete($id) {
        $homeKilau = HomeKilau::find($id); // Mencari HomeKilau berdasarkan ID
        if ($homeKilau->file_home && Storage::disk('public')->exists($homeKilau->file_home)) {
            Storage::disk('public')->delete($homeKilau->file_home); // Menghapus file gambar dari storage jika ada
        }
        $homeKilau->delete(); // Menghapus data dari database

        return redirect()->route('profil.homekilau')->with('success', 'Home Kilau berhasil dihapus.'); // Redirect dengan pesan sukses
    }
}
