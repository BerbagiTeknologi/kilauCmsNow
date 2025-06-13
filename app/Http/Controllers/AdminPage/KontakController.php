<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    /**
     * Menampilkan daftar kontak
     */
    public function index()
    {
        $kontaks = Kontak::all();
        return view('AdminPage.Kontak.index', compact('kontaks'));
    }

    /**
     * Menyimpan data kontak baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kacab' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:kontak,email',
            'maplink' => 'nullable|string|max:500',
        ]);

        $kontak = new Kontak();
        $kontak->nama_kacab = $request->nama_kacab;
        $kontak->alamat = $request->alamat;
        $kontak->telephone = $request->telephone;
        $kontak->email = $request->email;
        $kontak->maplink = $request->maplink;
        $kontak->status_kontak = Kontak::KONTAK_AKTIF; 

        $kontak->save();

        return redirect()->route('kontak')->with('success', 'Kontak berhasil ditambahkan.');
    }

    /**
     * Mengupdate data kontak berdasarkan ID
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kacab' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:kontak,email,' . $id,
            'maplink' => 'nullable|string|max:500',
        ]);

        $kontak = Kontak::findOrFail($id);
        $kontak->nama_kacab = $request->nama_kacab;
        $kontak->alamat = $request->alamat;
        $kontak->telephone = $request->telephone;
        $kontak->email = $request->email;
        $kontak->maplink = $request->maplink;

        $kontak->save();

        return redirect()->route('kontak')->with('success', 'Kontak berhasil diperbarui.');
    }

    /**
     * Toggle status aktif/nonaktif kontak
     */
    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_kontak' => 'required|in:1,2',
        ]);

        $kontak = Kontak::findOrFail($id);
        $kontak->status_kontak = $request->status_kontak;
        $kontak->save();

        return redirect()->route('kontak')->with('success', 'Status Kontak berhasil diperbarui.');
    }

    /**
     * Menghapus kontak berdasarkan ID
     */
    public function destroy($id)
    {
        $kontak = Kontak::findOrFail($id);
        $kontak->delete();

        return redirect()->route('kontak')->with('success', 'Kontak berhasil dihapus.');
    }
}
