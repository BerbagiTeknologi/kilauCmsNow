<?php

namespace App\Http\Controllers\AdminPage;

use App\Models\Pimpinan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PimpinanController extends Controller
{
    public function index()
    {
        $pimpinan = Pimpinan::all();
        return view('AdminPage.Profile.Pimpinan.index', compact('pimpinan'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'pendidikan' => 'required|string|max:255',
            'deskripsi_diri' => 'nullable|string|max:500',
            'sequence_tempat' => 'nullable|integer',
            'file_pimpinan' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $pimpinan = new Pimpinan();
        $pimpinan->nama = $request->nama;
        $pimpinan->pendidikan = $request->pendidikan;
        $pimpinan->jabatan = $request->jabatan;
        $pimpinan->deskripsi_diri = $request->deskripsi_diri;
        $pimpinan->sequence_tempat = $request->sequence_tempat;
        $pimpinan->status_pimpinan = Pimpinan::PIMPINAN_AKTIF; // Status default

        if ($request->hasFile('file_pimpinan')) {
            $path = $request->file('file_pimpinan')->store('pimpinan', 'public');
            $pimpinan->file_pimpinan = $path;
        }

        $pimpinan->save();

        return redirect()->route('profil.pimpinan')->with('success', 'Pimpinan berhasil ditambahkan.');
    }

    public function edit(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'pendidikan' => 'required|string|max:255',
            'deskripsi_diri' => 'nullable|string|max:500',
            'sequence_tempat' => 'nullable|integer',
            'file_pimpinan' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $pimpinan = Pimpinan::findOrFail($id);
        $pimpinan->nama = $request->nama;
        $pimpinan->jabatan = $request->jabatan;
        $pimpinan->pendidikan = $request->pendidikan;
        $pimpinan->deskripsi_diri = $request->deskripsi_diri;
        $pimpinan->sequence_tempat = $request->sequence_tempat;

        if ($request->hasFile('file_pimpinan')) {
            if ($pimpinan->file_pimpinan && Storage::disk('public')->exists($pimpinan->file_pimpinan)) {
                Storage::disk('public')->delete($pimpinan->file_pimpinan);
            }

            $path = $request->file('file_pimpinan')->store('pimpinan', 'public');
            $pimpinan->file_pimpinan = $path;
        }

        $pimpinan->save();

        return redirect()->route('profil.pimpinan')->with('success', 'Pimpinan berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_pimpinan' => 'required|in:1,2',
        ]);

        $pimpinan = Pimpinan::findOrFail($id);
        $pimpinan->status_pimpinan = $request->status_pimpinan;
        $pimpinan->save();

        return redirect()->route('profil.pimpinan')->with('success', 'Status Pimpinan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $pimpinan = Pimpinan::findOrFail($id);

        if ($pimpinan->file_pimpinan && Storage::disk('public')->exists($pimpinan->file_pimpinan)) {
            Storage::disk('public')->delete($pimpinan->file_pimpinan);
        }

        $pimpinan->delete();

        return redirect()->route('profil.pimpinan')->with('success', 'Pimpinan berhasil dihapus.');
    }
}
