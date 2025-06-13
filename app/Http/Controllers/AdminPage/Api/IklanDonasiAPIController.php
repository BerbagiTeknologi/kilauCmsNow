<?php

namespace App\Http\Controllers\AdminPage\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KilauIklan;
use App\Http\Resources\Donasi\DonasiCollection;
use App\Http\Requests\DonasiRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UpdateDonasiRequest;
use Illuminate\Support\Facades\Storage;

class IklanDonasiAPIController extends Controller
{
    public function index()
    {
        $data = KilauIklan::where('statuskilauiklan', KilauIklan::DONASI_IKLAN_AKTIVE)->get();

        return response()->json([
            'success' => true,
            'message' => 'Data iklan donasi berhasil diambil',
            'data' => new DonasiCollection($data),
        ], 200);
    }

    public function show($id)
    {
        $item = KilauIklan::find($id);
    
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data iklan donasi tidak ditemukan',
                'data' => null
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Data iklan donasi berhasil ditemukan',
            'data' => new DonasiCollection(collect([$item])), // <-- dibungkus pakai collect()
        ], 200);
    }

    public function create(DonasiRequest $request)
    {
        DB::beginTransaction(); // Mulai transaksi
    
        try {
            $validated = $request->validated();
    
            $donasiiklan = new KilauIklan();
            $donasiiklan->nama = $validated['nama'];
            $donasiiklan->icon_iklan = $validated['icon_iklan'] ?? null;
            $donasiiklan->name_button_iklan = $validated['name_button_iklan'] ?? null;
            $donasiiklan->link = $validated['link'] ?? null;
            $donasiiklan->statuskilauiklan = KilauIklan::DONASI_IKLAN_AKTIVE;
    
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('berbagidonasiiklan', 'public');
                $donasiiklan->file = $path;
            }
    
            $donasiiklan->save();
    
            DB::commit(); // Simpan transaksi jika tidak ada error
    
            return response()->json([
                'success' => true,
                'message' => 'Data iklan donasi berhasil ditambahkan',
                'data' => new DonasiCollection(collect([$donasiiklan])),
            ], 201);
    
        } catch (\Exception $e) {
            DB::rollBack(); // Kembalikan perubahan kalau error
            Log::error("Gagal tambah iklan donasi: " . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data iklan donasi. Silakan coba lagi.',
                'error' => $e->getMessage(), // Opsional, bisa di-hide di production
            ], 500);
        }
    }

    public function edit(UpdateDonasiRequest $request, $id)
    {
        DB::beginTransaction();
    
        try {
            $donasiiklan = KilauIklan::find($id);
    
            if (!$donasiiklan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data iklan donasi tidak ditemukan.',
                ], 404);
            }
    
            $validated = $request->validated();
    
            $donasiiklan->nama = $validated['nama'];
            $donasiiklan->icon_iklan = $validated['icon_iklan'] ?? $donasiiklan->icon_iklan;
            $donasiiklan->name_button_iklan = $validated['name_button_iklan'] ?? $donasiiklan->name_button_iklan;
            $donasiiklan->link = $validated['link'] ?? $donasiiklan->link;

            if ($request->hasFile('file')) {
                // Hapus file lama kalau perlu:
                // Storage::disk('public')->delete($donasiiklan->file);
                
                $path = $request->file('file')->store('berbagidonasiiklan', 'public');
                $donasiiklan->file = $path;
            }
    
            $donasiiklan->save();
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Data iklan donasi berhasil diperbarui',
                'data' => new DonasiCollection(collect([$donasiiklan])),
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal update iklan donasi: " . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data iklan donasi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $donasiiklan = KilauIklan::find($id);

            if (!$donasiiklan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data iklan donasi tidak ditemukan.',
                ], 404);
            }

            // Hapus file dari storage (jika ada)
            if ($donasiiklan->file && Storage::disk('public')->exists($donasiiklan->file)) {
                Storage::disk('public')->delete($donasiiklan->file);
            }

            $donasiiklan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data iklan donasi berhasil dihapus.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal hapus iklan donasi: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data iklan donasi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
