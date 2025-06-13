<?php

namespace App\Http\Controllers\AdminPage;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DokumenAdminController extends Controller
{
    /**
     * Menampilkan daftar dokumen
     */
    public function index()
    {
        $documents = Document::all(); 
        return view('AdminPage.Dokumen.index', compact('documents')); 
    }

    /**
     * Menyimpan data dokumen baru
     */
    
     public function store(Request $request)
     {
         // Validasi input hanya untuk file PDF
         $request->validate([
            
             'files' => 'required|array',
             'files.*' => 'file|mimes:pdf|max:5048', // Hanya menerima file PDF
             'text_document' => 'nullable|string|max:1000',
         ]);
 
         $filePaths = [];
         if ($request->hasFile('files')) {
             foreach ($request->file('files') as $file) {
                 // Simpan file ke storage dan dapatkan path-nya
                 $filePaths[] = $file->store('documents', 'public');
             }
         }
 
         // Simpan data dokumen ke database (hanya menyimpan file)
         $document = new Document();
         $document->files = json_encode($filePaths);
         $document->text_document = $request->input('text_document');
         $document->status_document = Document::DOKUMEN_AKTIF;
         $document->save();
 
         return redirect()->route('document')->with('success', 'Dokumen berhasil ditambahkan.');
     }

    /**
     * Mengupdate data dokumen berdasarkan ID
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'text_document' => 'required|string|max:1000',
            'files' => 'nullable|array', // File bisa kosong jika hanya update judul
            'files.*' => 'file|mimes:pdf|max:5048', // Validasi setiap file (pdf)
        ]);
    
        // Cari dokumen berdasarkan ID
        $document = Document::findOrFail($id);
    
        // Update judul dokumen
        $document->update([
            'text_document' => $request->text_document,
        ]);
    
        // Jika ada file yang diunggah, hapus file lama dan simpan yang baru
        if ($request->hasFile('files')) {
            // Hapus file lama jika ada
            if ($document->files) {
                $oldFiles = json_decode($document->files, true);
                foreach ($oldFiles as $oldFile) {
                    if (Storage::disk('public')->exists($oldFile)) {
                        Storage::disk('public')->delete($oldFile);
                    }
                }
            }
    
            // Simpan file baru
            $filePaths = [];
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('documents', 'public');
                $filePaths[] = $filePath;
            }
    
            // Simpan path file baru ke database
            $document->update([
                'files' => json_encode($filePaths),
            ]);
        }
    
        return redirect()->route('document')->with('success', 'Dokumen berhasil diperbarui.');
    }
    


    /**
     * Menghapus dokumen berdasarkan ID
     */
    public function destroy($id)
    {
        // Mencari dokumen berdasarkan ID
        $document = Document::findOrFail($id);

        // Hapus file jika ada
        if ($document->file && Storage::disk('public')->exists($document->file)) {
            Storage::disk('public')->delete($document->file);
        }

        // Hapus dokumen dari database
        $document->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('document')->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Mengubah status aktif/nonaktif dokumen
     */
    public function toggleStatus(Request $request, $id)
    {
        // Validasi status
        $request->validate([
            'status_document' => 'required|in:1,2',
        ]);

        // Mencari dokumen berdasarkan ID
        $document = Document::findOrFail($id);
        $document->status_document = $request->status_document; 
        $document->save();

        // Redirect dengan pesan sukses
        return redirect()->route('document')->with('success', 'Status dokumen berhasil diperbarui.');
    }
}
