<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BeritaAdminController extends Controller
{
    // Menampilkan halaman berita di admin
    public function index()
    {
        return view('AdminPage.Berita.index'); // Tidak perlu mengirimkan data karena akan di-load via AJAX
    }

    public function getKategoriBerita() {
        return view('AdminPage.Berita.Kategori.index');
    }

    public function getKomentarBerita() {
        return view('AdminPage.Berita.Komentar.index');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $path = storage_path('app/public/uploads/' . $filename);

            // **Resize gambar ke 500px tanpa Intervention**
            list($width, $height) = getimagesize($file);
            $new_width = 500; // Set lebar maksimum ke 500px
            $new_height = ($height / $width) * $new_width; // Menjaga rasio aspek

            $new_image = imagecreatetruecolor($new_width, $new_height);
            if ($extension == 'jpg' || $extension == 'jpeg') {
                $source = imagecreatefromjpeg($file);
            } elseif ($extension == 'png') {
                $source = imagecreatefrompng($file);
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
            } elseif ($extension == 'gif') {
                $source = imagecreatefromgif($file);
            } else {
                return response()->json(['success' => false, 'message' => 'Format gambar tidak didukung'], 400);
            }

            // Resize gambar
            imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            // Simpan gambar ke dalam folder `storage`
            if ($extension == 'jpg' || $extension == 'jpeg') {
                imagejpeg($new_image, $path, 90);
            } elseif ($extension == 'png') {
                imagepng($new_image, $path, 9);
            } elseif ($extension == 'gif') {
                imagegif($new_image, $path);
            }

            // Bersihkan memori
            imagedestroy($new_image);
            imagedestroy($source);

            return response()->json([
                'success' => true,
                'image_url' => asset('storage/uploads/' . $filename)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Upload gagal'], 400);
    }

    public function show($id)
    {
        // Ambil data berita dari API berdasarkan ID
        $response = Http::get("https://berbagipendidikan.org/api/berita/{$id}");

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'data' => $response->json()['data'] ?? null
            ]);
        } else {
            return response()->json(['success' => false], 404);
        }
    }

}
