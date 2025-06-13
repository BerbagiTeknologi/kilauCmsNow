<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Models\VisiMisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VisiMisiController extends Controller
{
    public function index()
    {
        $visimisi = VisiMisi::all();
        return view('AdminPage.Profile.VisiMisi.index', compact('visimisi'));
    }

    // Menyimpan data visi & misi baru
    public function create(Request $request)
    {
        $request->validate([
            'visi' => 'required|string',
            'misi' => 'required|string',
        ]);

        VisiMisi::create([
            'visi' => $request->visi,
            'misi' => $request->misi,
            'status_visimisi' => VisiMisi::VISI_MISI_AKTIF, // Set status default
        ]);

        return redirect()->route('profil.visimisi')->with('success', 'Visi dan Misi berhasil ditambahkan.');
    }

    // Mengupdate data visi & misi
    public function edit(Request $request, $id)
    {
        $request->validate([
            'visi' => 'required|string',
            'misi' => 'required|string',
        ]);

        $visimisi = VisiMisi::findOrFail($id);
        $visimisi->update([
            'visi' => $request->visi,
            'misi' => $request->misi,
        ]);

        return redirect()->route('profil.visimisi')->with('success', 'Visi dan Misi berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_visimisi' => 'required|in:1,2',
        ]);

        $visimisi = VisiMisi::findOrFail($id);
        $visimisi->status_visimisi = $request->status_visimisi;
        $visimisi->save();

        return redirect()->route('profil.visimisi')->with('success', 'Status Visi dan Misi berhasil diperbarui.');
    }

    // Menghapus visi & misi
    public function delete($id)
    {
        $visimisi = VisiMisi::findOrFail($id);
        $visimisi->delete();

        return redirect()->route('profil.visimisi')->with('success', 'Visi dan Misi berhasil dihapus.');
    }
}
