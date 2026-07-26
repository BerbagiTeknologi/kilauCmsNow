<?php

namespace App\Http\Controllers\AdminPage;

use App\Models\Program;
use App\Models\MitraDonatur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function index()
    {
        // Mengambil data program dan relasi mitras menggunakan eager loading
        $program = Program::with('mitras')->get();
        $mitras = MitraDonatur::all();  // Mengambil semua data mitra untuk dropdown

        // Mengirimkan data program dan mitras ke view
        return view('AdminPage.Program.index', compact('program', 'mitras'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'program_yang_berhasil_dijalankan' => 'required|integer|min:0',
            'jumlah_target_tercapai' => 'required|integer|min:0',
            'foto_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'thumbnail_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'mitra_ids' => 'required|array', // Pastikan mitra_ids ada dan berupa array
            'mitra_ids.*' => 'exists:mitra_donatur,id', // Pastikan setiap ID mitra yang dipilih valid
        ]);

        // Membuat objek program baru
        $program = new Program();
        $program->judul = $request->judul;
        $program->deskripsi = $request->deskripsi;
        $program->program_yang_berhasil_dijalankan = $request->program_yang_berhasil_dijalankan;
        $program->jumlah_target_tercapai = $request->jumlah_target_tercapai;
        $program->status_program = Program::PROGRAM_AKTIF; // Status default

        // Menangani upload gambar
        if ($request->hasFile('foto_image')) {
            $images = [];
            foreach ($request->file('foto_image') as $image) {
                $path = $image->store('program_images', 'public');
                $images[] = $path;
            }
            $program->foto_image = $images;
        }

        if ($request->hasFile('thumbnail_image')) {
            $path = $request->file('thumbnail_image')->store('program_thumbnail', 'public');
            $program->thumbnail_image = $path;
        }

        // Menyimpan data program
        $program->save();

        // Menyimpan relasi dengan mitra menggunakan pivot table
        $program->mitras()->sync($request->mitra_ids); // Menyinkronkan mitra dengan program

        // Mengarahkan ke halaman program dengan pesan sukses
        return redirect()->route('program')->with('success', 'Program berhasil ditambahkan.');
    }

    // Tambahkan ke dalam ProgramController
    public function getMitraImage($id)
    {
        $mitra = MitraDonatur::find($id);

        if ($mitra && $mitra->file) {
            return response()->json([
                'status' => 'success',
                'image_url' => Storage::url($mitra->file)
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'No image found']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'program_yang_berhasil_dijalankan' =>  'required|integer|min:0',
            'jumlah_target_tercapai' => 'required|integer|min:0',
            'foto_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'thumbnail_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Find the program by id
        $program = Program::findOrFail($id);
        $program->judul = $request->judul;
        $program->deskripsi = $request->deskripsi;
        $program->program_yang_berhasil_dijalankan = $request->program_yang_berhasil_dijalankan;
        $program->jumlah_target_tercapai = $request->jumlah_target_tercapai;

        // Handling file uploads for images
        if ($request->hasFile('foto_image')) {
            $images = [];
            foreach ($request->file('foto_image') as $image) {
                $path = $image->store('program_images', 'public');
                $images[] = $path;
            }
            $program->foto_image = $images;
        }

        if ($request->hasFile('thumbnail_image')) {
            if ($program->thumbnail_image && Storage::disk('public')->exists($program->thumbnail_image)) {
                Storage::disk('public')->delete($program->thumbnail_image);
            }
            $path = $request->file('thumbnail_image')->store('program_thumbnail', 'public');
            $program->thumbnail_image = $path;
        }

        // Sync mitra relationships (many-to-many)
        $program->mitras()->sync($request->mitra_ids);

        // Save the program
        $program->save();

        // Redirect back with success message
        return redirect()->route('program')->with('success', 'Program berhasil diperbarui.');
    }


    public function toggleStatus(Request $request, $id)
    {
        // Validasi status program
        $request->validate([
            'status_program' => 'required|in:1,2', // Validasi status program (Aktif/Tidak Aktif)
        ]);
    
        // Menemukan data program yang sesuai dengan ID
        $program = Program::findOrFail($id);
    
        // Update status program
        $program->status_program = $request->input('status_program');
        $program->save();
    
        // Redirect kembali dengan pesan sukses
        return redirect()->route('program')->with('success', 'Status program berhasil diubah!');
    }
    


    // public function destroy($id)
    // {
    //     // Mengambil data program yang akan dihapus
    //     $timeline = Program::findOrFail($id);

    //     // Menghapus gambar yang ada jika ada
    //     if ($timeline->foto_image) {
    //         foreach ($timeline->foto_image as $image) {
    //             // Menghapus gambar dari storage jika ada
    //             if (Storage::disk('public')->exists($image)) {
    //                 Storage::disk('public')->delete($image);
    //             }
    //         }
    //     }

    //     // Menghapus data program
    //     $timeline->delete();

    //     // Mengarahkan kembali dengan pesan sukses
    //     return redirect()->route('program')->with('success', 'Program berhasil dihapus.');
    // }
    
    public function destroy($id)
    {
        // Mengambil data program yang akan dihapus
        $program = Program::findOrFail($id);
    
        // Hapus semua relasi program di tabel program_mitra
        DB::table('program_mitra')->where('program_id', $id)->delete();
    
        // Menghapus gambar yang ada jika ada
        if (!empty($program->foto_image)) {
            $foto_images = is_string($program->foto_image) ? json_decode($program->foto_image, true) : $program->foto_image;
            
            if (is_array($foto_images)) {
                foreach ($foto_images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }
        }
    
        // Hapus thumbnail jika ada
        if ($program->thumbnail_image && Storage::disk('public')->exists($program->thumbnail_image)) {
            Storage::disk('public')->delete($program->thumbnail_image);
        }
    
        // Hapus program
        $program->delete();
    
        return redirect()->route('program')->with('success', 'Program berhasil dihapus.');
    }
}
