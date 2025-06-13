<?php

namespace App\Http\Controllers\AdminPage;

use App\Models\Testimoni;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class TestimoniController extends Controller
{
    public function testimoni()
    {
        // Ambil semua data testimoni
        $testimonis = Testimoni::all();

        // Proses setiap testimoni untuk mendapatkan ID video dari video_link
        foreach ($testimonis as $testimoni) {
            if ($testimoni->video_link) {
                $testimoni->video_id = $this->getYouTubeVideoID($testimoni->video_link);
            }
        }

        // Kirim data ke view
        return view('AdminPage.Testimoni.index', compact('testimonis'));
    }
    
    public function getYouTubeVideoID($url)
    {
        // Menghapus query parameter (seperti ?si=...) jika ada
        $url = preg_replace('/\?[^\/]+$/', '', $url); 

        // Regex untuk menangani URL YouTube panjang dan pendek
        preg_match("/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/)([\w\-]{11})|youtu\.be\/([\w\-]{11}))/i", $url, $matches);

        // Mengembalikan ID video, baik dari youtube.com atau youtu.be
        return $matches[1] ?? $matches[2] ?? null;
    }
    
    public function testimoniCreate(Request $request) {
        $request->validate([
            'nama' => 'required',
            'pekerjaan' => 'nullable',
            'komentar' => 'required',
            'video_link' => 'nullable',
            'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $testimoni = new Testimoni;
        $testimoni->nama = $request->nama;
        $testimoni->pekerjaan = $request->pekerjaan;
        $testimoni->komentar = $request->komentar;
        $testimoni->video_link = $request->video_link;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('testimoni', 'public'); 
            $testimoni->file = $path;
        }

        $testimoni->save();

        return redirect()->route('testimoni')->with('success', 'Testimoni created successfully.');
    }

    public function toggleStatus($id)
    {
        $testimoni = Testimoni::findOrFail($id);
    
        // Toggle status: jika aktif jadi tidak aktif, dan sebaliknya
        $testimoni->statuss_testimoni = $testimoni->statuss_testimoni == Testimoni::TESTIMONI_AKTIF
            ? Testimoni::TESTIMONI_TIDAK_AKTIF
            : Testimoni::TESTIMONI_AKTIF;
    
        $testimoni->save();
    
        return redirect()->route('testimoni')->with('success', 'Status Testimoni berhasil diperbarui.');
    }    

    public function testimoniEdit(Request $request, $id) {
        $request->validate([
            'nama' => 'required',
            'pekerjaan' => 'required',
            'komentar' => 'nullable',
            'video_link' => 'nullable',
            'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $testimoni = Testimoni::find($id);
        $testimoni->nama = $request->nama;
        $testimoni->pekerjaan = $request->pekerjaan;
        $testimoni->komentar = $request->komentar;
        
        if ($request->video_link) {
            $testimoni->video_link = $request->video_link;
        }

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($testimoni->file && Storage::disk('public')->exists($testimoni->file)) {
                Storage::disk('public')->delete($testimoni->file);
            }

            // Simpan file baru ke storage
            $path = $request->file('file')->store('testimoni', 'public'); 
            $testimoni->file = $path;
        }

        $testimoni->save();

        return redirect()->route('testimoni')->with('success', 'Testimoni updated successfully.');
    }

    public function testimoniDelete($id) {
        $testimoni = Testimoni::find($id);
        if ($testimoni->file && file_exists(public_path('uploads/testimoni/' . $testimoni->file))) {
            unlink(public_path('uploads/testimoni/' . $testimoni->file));
        }
        $testimoni->delete();

        return redirect()->route('testimoni')->with('success', 'Testimoni deleted successfully.');
    }
}