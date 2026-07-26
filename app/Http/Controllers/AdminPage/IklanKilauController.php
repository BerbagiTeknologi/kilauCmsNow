<?php

namespace App\Http\Controllers\AdminPage;

use App\Models\IklanKilau;
use Illuminate\Http\Request;
use App\Models\IklanKilauList;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class IklanKilauController extends Controller
{
    // Menampilkan halaman IklanKilau
    public function iklankilau() {
        $iklanKilau = IklanKilau::all(); // Mengambil semua data iklan kilau
        return view('AdminPage.Profile.Iklan.index', compact('iklanKilau')); // Mengirim variabel ke view
    }

    // Fungsi untuk membuat data IklanKilau dan IklanKilauList
    public function iklankilauCreate(Request $request)
    {
        // Validasi input
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'iklan_kilau_lists' => 'nullable|array',
            'iklan_kilau_lists.*.name' => 'required|string|max:255',
            'jumlah_yayasan' => 'required|integer|min:0', 
            'jumlah_donatur' => 'nullable|integer|min:0', 
        ]);

        // Buat data IklanKilau
        $iklanKilau = new IklanKilau();
        $iklanKilau->judul = $request->judul;
        $iklanKilau->deskripsi = $request->deskripsi;
        $iklanKilau->jumlah_yayasan = $request->jumlah_yayasan;
        $iklanKilau->jumlah_donatur = $request->jumlah_donatur;
        $iklanKilau->status_kilau = IklanKilau::IKLAN_KILAU_AKTIF; 
      

        // Jika ada file, simpan file dan ambil path-nya
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('iklan_kilau', 'public');
            $iklanKilau->file = $path;
        }

        // Simpan IklanKilau
        $iklanKilau->save();

        // Jika ada data iklan_kilau_lists, buat data terkait
        if ($request->has('iklan_kilau_lists')) {
            foreach ($request->iklan_kilau_lists as $list) {
                // Menambahkan IklanKilauList yang terhubung ke IklanKilau
                $iklanKilau->iklanKilauLists()->create([
                    'name' => $list['name'],
                    'status_iklan_kilau_list' => IklanKilauList::IKLAN_KILAU_LIST_AKTIF, // Atur status default
                ]);
            }
        }

        return redirect()->route('profil.iklankilau')->with('success', 'Iklan Kilau berhasil ditambahkan.');
    }

    public function iklankilauEdit(Request $request, $id)
    {
        Log::info($request->all()); // Debugging

        // Validasi form
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'jumlah_yayasan' => 'required|integer|min:0', 
            'jumlah_donatur' => 'nullable|integer|min:0', 
        ]);

        $iklanKilau = IklanKilau::findOrFail($id);
        $iklanKilau->judul = $request->judul;
        $iklanKilau->deskripsi = $request->deskripsi;
        $iklanKilau->jumlah_yayasan = $request->jumlah_yayasan;
        $iklanKilau->jumlah_donatur = $request->jumlah_donatur;

        if ($request->hasFile('file')) {
            if ($iklanKilau->file && Storage::disk('public')->exists($iklanKilau->file)) {
                Storage::disk('public')->delete($iklanKilau->file);
            }
            $path = $request->file('file')->store('iklan_kilau', 'public');
            $iklanKilau->file = $path;
        }

        $iklanKilau->save();

        // **Penyimpanan iklan_kilau_lists**
        if ($request->has('iklan_kilau_lists')) {
            $existingIds = $iklanKilau->iklanKilauLists()->pluck('id')->toArray();
            $submittedIds = [];

            foreach ($request->iklan_kilau_lists as $index => $list) {
                if (!isset($list['name']) || !isset($list['status']) || empty($list['name'])) {
                    continue;
                }

                $data = [
                    'name' => $list['name'],
                    'status_iklan_kilau_list' => $list['status'] == 'Aktif' ? 1 : 2,
                    'iklan_kilau_id' => $iklanKilau->id
                ];

                if (!empty($list['id'])) {
                    $iklanKilauList = IklanKilauList::updateOrCreate(
                        ['id' => $list['id'], 'iklan_kilau_id' => $iklanKilau->id], 
                        $data
                    );
                    $submittedIds[] = $iklanKilauList->id;
                } else {
                    $newEntry = $iklanKilau->iklanKilauLists()->create($data);
                    if ($newEntry) {
                        $submittedIds[] = $newEntry->id;
                    }
                }
            }

            // Hapus data yang tidak ada dalam request
            $deletedIds = array_diff($existingIds, $submittedIds);
            if (!empty($deletedIds)) {
                IklanKilauList::whereIn('id', $deletedIds)->delete();
            }
        }

        return redirect()->route('profil.iklankilau')->with('success', 'Iklan Kilau berhasil diperbarui.');
    }



    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_kilau' => 'required|in:1,2',
        ]);

        $iklanKilau = IklanKilau::findOrFail($id);
        $iklanKilau->status_kilau = $request->status_kilau;
        $iklanKilau->save();

        return redirect()->route('profil.iklankilau')->with('success', 'Status Iklan Kilau berhasil diperbarui.');
    }

    public function iklankilauDelete($id) {
        $iklanKilau = IklanKilau::find($id);
        if ($iklanKilau->file && Storage::disk('public')->exists($iklanKilau->file)) {
            Storage::disk('public')->delete($iklanKilau->file);
        }
        $iklanKilau->delete();

        return redirect()->route('profil.iklankilau')->with('success', 'Iklan Kilau berhasil dihapus.');
    }
}
