<?php

namespace App\Http\Controllers\AdminPage;

use App\Models\Galeri;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class GaleriAdminController extends Controller
{
    public function index()
    {
        $galeri = Galeri::all();
        return view('AdminPage.Galeri.index', compact('galeri'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_kegiatan' => 'required|string|max:255',
            'deskripsi_kegiatan' => 'required|string',
            'nama_kantor_cabang' => 'required|string',
            'file_galeri.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ]);

        // Membuat objek program baru
        $galeri = new Galeri();
        $galeri->judul_kegiatan = $request->judul_kegiatan;
        $galeri->deskripsi_kegiatan = $request->deskripsi_kegiatan;
        $galeri->nama_kantor_cabang = $request->nama_kantor_cabang;
        $galeri->status_galeri = Galeri::GALERI_AKTIF; 

        // Menangani upload gambar
        if ($request->hasFile('file_galeri')) {
            $images = [];
            foreach ($request->file('file_galeri') as $image) {
                // Menyimpan gambar di storage public dan menambah path ke array
                $path = $image->store('galeri_file', 'public');
                $images[] = $path;
            }
            $galeri->file_galeri = $images;
        }

        // Menyimpan data program
        $galeri->save();

        // Mengarahkan ke halaman program dengan pesan sukses
        return redirect()->route('galeryAdmin')->with('success', 'Program berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_kegiatan' => 'required|string|max:255',
            'deskripsi_kegiatan' => 'required|string',
            'nama_kantor_cabang' => 'required|string',
            'file_galeri.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi gambar
        ]);
    
        // Menemukan data galeri yang akan diperbarui
        $galeri = Galeri::findOrFail($id);
        $galeri->judul_kegiatan = $request->judul_kegiatan;
        $galeri->deskripsi_kegiatan = $request->deskripsi_kegiatan;
        $galeri->nama_kantor_cabang = $request->nama_kantor_cabang;

        // Menangani upload gambar baru jika ada
        if ($request->hasFile('file_galeri')) {
            $images = [];
            foreach ($request->file('file_galeri') as $image) {
                // Menyimpan gambar di storage public dan menambah path ke array
                $path = $image->store('galeri_file', 'public');
                $images[] = $path;
            }
            // Menyimpan array path gambar ke kolom foto_image
            $galeri->file_galeri = $images;
        } else {
            // Jika tidak ada gambar baru, tetap menyimpan gambar lama
            $galeri->file_galeri = $galeri->file_galeri; 
        }


        // Menyimpan data program setelah diperbarui
        $galeri->save();

        // Mengarahkan ke halaman program dengan pesan sukses
        return redirect()->route('galeryAdmin')->with('success', 'Program berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_galeri' => 'required|in:1,2', 
        ]);
    
        $galeri = Galeri::findOrFail($id);
    
        $galeri->status_galeri = $request->input('status_galeri');
        $galeri->save();
    
        return redirect()->route('galeryAdmin')->with('success', 'Status program berhasil diubah!');
    }

    public function destroy($id)
    {
        // Mengambil data program yang akan dihapus
        $galeri = Galeri::findOrFail($id);

        // Menghapus gambar yang ada jika ada
        if ($galeri->file_galeri) {
            foreach ($galeri->file_galeri as $image) {
                // Menghapus gambar dari storage jika ada
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        // Menghapus data program
        $galeri->delete();

        // Mengarahkan kembali dengan pesan sukses
        return redirect()->route('galeryAdmin')->with('success', 'Program berhasil dihapus.');
    } 
}
