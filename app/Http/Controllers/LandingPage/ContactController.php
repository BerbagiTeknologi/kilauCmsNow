<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Kontak;
use App\Models\Program;
use App\Models\Colaborasi;
use App\Models\SettingsMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    public function contact() {
        // Cek apakah menu "Kontak" aktif
        $kontakMenu = SettingsMenu::find(5); 
        $kontaks = []; // Inisialisasi sebagai array kosong

        if ($kontakMenu && $kontakMenu->status == 'Aktif') {
            $kontaks = Kontak::where('status_kontak', Kontak::KONTAK_AKTIF)->get();
        }

        $programs = Program::all();

        return view('LandingPageKilau.kontak', compact('kontakMenu', 'kontaks' , 'programs'));
    }

    public function contactCreate(Request $request)
    {
        // Validasi data input
        $request->validate([
            'id_program' => 'required|exists:programs,id',
            'jenis_kerjasama' => 'nullable|string|max:255',
            'kategori_mitra' => 'required|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'alamat_email' => 'required|email|max:255|unique:colaborasi,alamat_email',
            'nomor_hp' => 'required|string|max:20|regex:/^[0-9]+$/',
            'nomor_hp_organisasi' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'npwp_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'foto_orang_npwp' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'deskripsi_pengajuan_kerjasama' => 'nullable|string',
        ], [
            'id_program.required' => 'Harap pilih salah satu program.',
            'id_program.exists' => 'Program yang dipilih tidak valid.',
            'jenis_kerjasama.required' => 'Harap pilih jenis kerjasama.',
            'alamat_email.unique' => 'Email ini sudah digunakan untuk pengajuan sebelumnya.',
            'npwp_file.mimes' => 'Format file NPWP harus berupa PDF, JPG, JPEG, atau PNG.',
            'foto_orang_npwp.mimes' => 'Format foto harus JPG atau PNG.',
            'nomor_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'nomor_hp_organisasi.regex' => 'Nomor HP Organisasi hanya boleh berisi angka.',
        ]);

        $npwpFilePath = null;
        $fotoOrangNpwpPath = null;

        if ($request->hasFile('npwp_file')) {
            $npwpFilePath = $request->file('npwp_file')->store('npwp', 'public');
        }

        if ($request->hasFile('foto_orang_npwp')) {
            $fotoOrangNpwpPath = $request->file('foto_orang_npwp')->store('npwp_foto', 'public');
        }

        // Simpan data ke database
        Colaborasi::create([
            'id_program' => $request->id_program,
            'jenis_kerjasama' => $request->jenis_kerjasama ?? null, 
            'kategori_mitra' => $request->kategori_mitra,
            'nama_lengkap' => $request->nama_lengkap,
            'alamat_email' => $request->alamat_email,
            'nama_perusahaan' => $request->nama_perusahaan,
            'jabatan' => ($request->kategori_mitra === "Perusahaan" || $request->kategori_mitra === "Instansi/Lembaga/Komunitas") ? $request->jabatan : null,
            'nomor_hp' => $request->nomor_hp,
            'nomor_hp_organisasi' => $request->nomor_hp_organisasi ?? null,
            'npwp_file' => $npwpFilePath,
            'foto_orang_npwp' => $fotoOrangNpwpPath,
            'deskripsi_pengajuan_kerjasama' => $request->deskripsi_pengajuan_kerjasama,
            'status_progress_kerjasama' => Colaborasi::COLABORASI_PROGRES_PENDING,
            'status_kerjasama' => Colaborasi::COLABORASI_TIDAK_AKTIF,
        ]);

        return redirect()->route('contact')->with('success', 'Pengajuan Kerjasama telah berhasil dikirim.');
    }

    public function checkNamaPerusahaan(Request $request)
    {
        $exists = Colaborasi::where('nama_perusahaan', $request->nama_perusahaan)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkEmail(Request $request)
    {
        $exists = Colaborasi::where('alamat_email', $request->alamat_email)->exists();
        return response()->json(['exists' => $exists]);
    }

}
