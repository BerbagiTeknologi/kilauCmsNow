<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Models\Sejarah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SejarahController extends Controller
{
    public function index() {
        $sejarah = Sejarah::all();
        return view('AdminPage.Profile.Sejarah.index', compact('sejarah'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'deskripsi_sejarah' => 'required|string',
            'nama_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
        ]);

        $sejarah = new Sejarah();
        $sejarah->deskripsi_sejarah = $request->deskripsi_sejarah;
        $sejarah->status_sejarah = Sejarah::SEJARAH_AKTIF;

        if ($request->hasFile('nama_file')) {
            $path = $request->file('nama_file')->store('sejarah_files', 'public');
            $sejarah->nama_file = $path;
        }

        $sejarah->save();

        return redirect()->route('profil.sejarah')->with('success', 'Data sejarah berhasil ditambahkan.');
    }

    public function edit(Request $request, $id)
    {
        $request->validate([
            'deskripsi_sejarah' => 'required|string',
            'nama_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
        ]);

        $sejarah = Sejarah::findOrFail($id);
        $sejarah->deskripsi_sejarah = $request->deskripsi_sejarah;

        if ($request->hasFile('nama_file')) {
            if ($sejarah->nama_file && Storage::disk('public')->exists($sejarah->nama_file)) {
                Storage::disk('public')->delete($sejarah->nama_file);
            }

            $path = $request->file('nama_file')->store('sejarah_files', 'public');
            $sejarah->nama_file = $path;
        }

        $sejarah->save();

        return redirect()->route('profil.sejarah')->with('success', 'Data sejarah berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_sejarah' => 'required|in:1,2',
        ]);

        $sejarah = Sejarah::findOrFail($id);
        $sejarah->status_sejarah = $request->status_sejarah;
        $sejarah->save();

        return redirect()->route('profil.sejarah')->with('success', 'Status sejarah berhasil diperbarui.');
    }

    public function delete($id) {
        $sejarah = Sejarah::findOrFail($id);

        if ($sejarah->nama_file && Storage::disk('public')->exists($sejarah->nama_file)) {
            Storage::disk('public')->delete($sejarah->nama_file);
        }

        $sejarah->delete();

        return redirect()->route('profil.sejarah')->with('success', 'Data sejarah berhasil dihapus.');
    }
}
