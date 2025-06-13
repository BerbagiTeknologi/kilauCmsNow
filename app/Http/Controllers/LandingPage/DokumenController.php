<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Document;
use App\Models\SettingsMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DokumenController extends Controller
{
    public function dokumen() {
        // Cek apakah menu "Kontak" aktif
        $dokumentMenu = SettingsMenu::find(6); 
        $dokuments = []; // Inisialisasi sebagai array kosong

        if ($dokumentMenu && $dokumentMenu->status == 'Aktif') {
            $dokuments = Document::where('status_document', Document::DOKUMEN_AKTIF)->get();
        }

        return view('LandingPageKilau.dokumen', compact('dokumentMenu', 'dokuments'));
    }
    
    public function dokumenShow($id)
    {
        // Mengambil data dokumen berdasarkan ID
        $document = Document::findOrFail($id);
    
        // Kirimkan data dokumen dalam bentuk JSON atau tampilkan di view
        return view('LandingPageKilau.dokumen', compact('document'));
    }

}
