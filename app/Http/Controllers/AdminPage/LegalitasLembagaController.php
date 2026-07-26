<?php

namespace App\Http\Controllers\AdminPage;

use Illuminate\Http\Request;
use App\Models\LegalitasLembaga;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class LegalitasLembagaController extends Controller
{
    public function index()
    {
        $legalitas = LegalitasLembaga::all(); 
        return view('AdminPage.Profile.LegalitasLembaga.index', compact('legalitas')); 
    }

    /**
     * Menyimpan data dokumen baru
     */
    
     public function store(Request $request)
     {
         // Validasi input hanya untuk file PDF
         $request->validate([
             'file_legalitas' => 'required|array',
             'file_legalitas.*' => 'file|mimes:pdf|max:5048', // Hanya menerima file PDF
             'judul' => 'nullable|string|max:1000',
         ]);
 
         $filePaths = [];
         if ($request->hasFile('file_legalitas')) {
             foreach ($request->file('file_legalitas') as $file) {
                 // Simpan file ke storage dan dapatkan path-nya
                 $filePaths[] = $file->store('legalitas', 'public');
             }
         }
 
         // Simpan data dokumen ke database (hanya menyimpan file)
         $legalitas = new LegalitasLembaga();
         $legalitas->file_legalitas = json_encode($filePaths);
         $legalitas->judul = $request->input('judul');
         $legalitas->status_legalitas = LegalitasLembaga::LEGALITAS_AKTIF;
         $legalitas->save();
 
         return redirect()->route('profil.legalitaslembaga')->with('success', 'Legalitas Lembaga berhasil ditambahkan.');
     }

    /**
     * Mengupdate data dokumen berdasarkan ID
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'judul' => 'required|string|max:1000',
            'file_legalitas' => 'nullable|array',
            'file_legalitas.*' => 'file|mimes:pdf|max:5048', // Hanya menerima file PDF
        ]);
    
        // Cari dokumen berdasarkan ID
        $legalitas = LegalitasLembaga::findOrFail($id);
    
        // Update judul
        $legalitas->update([
            'judul' => $request->judul,
        ]);
    
        // Jika ada file yang diunggah, hapus file lama dan simpan yang baru
        if ($request->hasFile('file_legalitas')) {
            // Hapus file lama jika ada
            if ($legalitas->file_legalitas) {
                $oldFiles = json_decode($legalitas->file_legalitas, true);
                foreach ($oldFiles as $oldFile) {
                    if (Storage::disk('public')->exists($oldFile)) {
                        Storage::disk('public')->delete($oldFile);
                    }
                }
            }
    
            // Simpan file baru
            $filePaths = [];
            foreach ($request->file('file_legalitas') as $file) {
                $filePath = $file->store('legalitas', 'public');
                $filePaths[] = $filePath;
            }
    
            // Simpan path file baru ke database
            $legalitas->update([
                'file_legalitas' => json_encode($filePaths),
            ]);
        }
    
        return redirect()->route('profil.legalitaslembaga')->with('success', 'Legalitas Lembaga berhasil diperbarui.');
    }
    

    /**
     * Menghapus dokumen berdasarkan ID
     */
    public function destroy($id)
    {
        // Mencari dokumen berdasarkan ID
        $legalitas = LegalitasLembaga::findOrFail($id);

        // Hapus file jika ada
        if ($legalitas->file && Storage::disk('public')->exists($legalitas->file)) {
            Storage::disk('public')->delete($legalitas->file);
        }

        // Hapus dokumen dari database
        $legalitas->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('profil.legalitaslembaga')->with('success', 'Legalitas Lembaga berhasil dihapus.');
    }

    /**
     * Mengubah status aktif/nonaktif dokumen
     */
    public function toggleStatus(Request $request, $id)
    {
        // Validasi status
        $request->validate([
            'status_legalitas' => 'required|in:1,2',
        ]);

        // Mencari dokumen berdasarkan ID
        $legalitas = LegalitasLembaga::findOrFail($id);
        $legalitas->status_legalitas = $request->status_legalitas; 
        $legalitas->save();

        // Redirect dengan pesan sukses
        return redirect()->route('profil.legalitaslembaga')->with('success', 'Status Legalitas Lembaga berhasil diperbarui.');
    }
}
