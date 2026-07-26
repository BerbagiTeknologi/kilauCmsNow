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

             'thumbnail'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
         ]);
 
         $filePaths = [];
         if ($request->hasFile('files')) {
             foreach ($request->file('files') as $file) {
                 // Simpan file ke storage dan dapatkan path-nya
                 $filePaths[] = $file->store('documents', 'public');
             }
         }

        $thumbPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbPath = $request->file('thumbnail')->store('documents/thumbnails', 'public');
        }
 
         // Simpan data dokumen ke database (hanya menyimpan file)
         $document = new Document();
         $document->files = json_encode($filePaths);
         $document->thumbnail       = $thumbPath; 
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
        /* ---------- VALIDASI ---------- */
        $request->validate([
            'text_document' => 'required|string|max:1000',

            /* dokumen PDF (boleh kosong) */
            'files'   => 'nullable|array',
            'files.*' => 'file|mimes:pdf|max:5048',

            /* thumbnail gambar (opsional) */
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        /* ---------- AMBIL RECORD ---------- */
        $document = Document::findOrFail($id);

        /* ---------- UPDATE TEKS ---------- */
        $document->text_document = $request->text_document;

        /* ---------- GANTI FILE DOKUMEN ---------- */
        if ($request->hasFile('files')) {
            /* hapus file lama (jika ada) */
            if ($document->files) {
                foreach (json_decode($document->files, true) as $oldFile) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            /* simpan file baru */
            $paths = [];
            foreach ($request->file('files') as $file) {
                $paths[] = $file->store('documents', 'public');
            }
            $document->files = json_encode($paths);
        }

        /* ---------- GANTI THUMBNAIL ---------- */
        if ($request->hasFile('thumbnail')) {
            /* hapus thumbnail lama (jika ada) */
            if ($document->thumbnail) {
                Storage::disk('public')->delete($document->thumbnail);
            }
            /* simpan thumbnail baru */
            $thumbPath          = $request->file('thumbnail')->store('documents/thumbnails', 'public');
            $document->thumbnail = $thumbPath;
        }

        /* ---------- SIMPAN ---------- */
        $document->save();

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
