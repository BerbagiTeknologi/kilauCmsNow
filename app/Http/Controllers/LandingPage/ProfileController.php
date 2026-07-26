<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Struktur;
use App\Models\SettingsMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LegalitasLembaga;
use App\Models\Pimpinan;
use App\Models\Sejarah;
use App\Models\VisiMisi;

class ProfileController extends Controller
{
    public function profilPimpinan() {
        $pimpinanMenu = SettingsMenu::find(9); 
        $pimpinans = null;
    
        if ($pimpinanMenu && $pimpinanMenu->status == 'Aktif') {
            $pimpinans = Pimpinan::where('status_pimpinan', Pimpinan::PIMPINAN_AKTIF)->get();
        }
        
        return view('LandingPageKilau.Profile.profilePimpinan', compact('pimpinanMenu', 'pimpinans'));
    }

    public function profilStruktur() {
        $strukturMenu = SettingsMenu::find(9); 
        $strukturs = null;
    
        if ($strukturMenu && $strukturMenu->status == 'Aktif') {
            $strukturs = Struktur::where('status_struktur', Struktur::STRUKTUR_AKTIF)->get();
        }
    
        return view('LandingPageKilau.Profile.profilStruktur', compact('strukturMenu', 'strukturs'));
    }

    public function profilSejarah() {
        $sejarahMenu = SettingsMenu::find(10); 
        $sejarahs = null;
    
        if ($sejarahMenu && $sejarahMenu->status == 'Aktif') {
            $sejarahs = Sejarah::where('status_sejarah', Sejarah::SEJARAH_AKTIF)->get();
        }

        return view('LandingPageKilau.Profile.profilSejarah', compact('sejarahMenu', 'sejarahs'));
    }

    public function profilVisiMisi() {
        $visimisiMenu = SettingsMenu::find(11);
        $visimisis = null;

        if($visimisiMenu && $visimisiMenu->status == 'Aktif') {
            $visimisis = VisiMisi::where('status_visimisi', VisiMisi::VISI_MISI_AKTIF)->get();
        }

        return view('LandingPageKilau.Profile.profilVisiMisi', compact('visimisiMenu', 'visimisis'));
    }

    public function profilLegalitas() {
        // Cek apakah menu "Kontak" aktif
        $legalitasMenu = SettingsMenu::find(15); 
        $legalitas = []; // Inisialisasi sebagai array kosong

        if ($legalitasMenu && $legalitasMenu->status == 'Aktif') {
            $legalitas = LegalitasLembaga::where('status_legalitas', LegalitasLembaga::LEGALITAS_AKTIF)->get();
        }

        return view('LandingPageKilau.Profile.profilLegalitas', compact('legalitasMenu', 'legalitas'));
    }
}
