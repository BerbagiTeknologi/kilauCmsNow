<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Sejarah;
use App\Models\VisiMisi;
use App\Models\TentangKami;
use App\Models\SettingsMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TentangKamiAdminController extends Controller
{
    public function tentangkami(){
        $tentangMenu = SettingsMenu::find(8); 
        $tentangs = null;

        if ($tentangMenu && $tentangMenu->status == 'Aktif') {
            $tentangs = TentangKami::where('status_tentang_kami', TentangKami::TENTANG_AKTIF)->get(); 
        }

        $sejarahMenu = SettingsMenu::find(10); 
        $sejarahs = null;
    
        if ($sejarahMenu && $sejarahMenu->status == 'Aktif') {
            $sejarahs = Sejarah::where('status_sejarah', Sejarah::SEJARAH_AKTIF)->get();
        }

        $visimisiMenu = SettingsMenu::find(11);
        $visimisis = null;

        if($visimisiMenu && $visimisiMenu->status == 'Aktif') {
            $visimisis = VisiMisi::where('status_visimisi', VisiMisi::VISI_MISI_AKTIF)->get();
        }

        return view('LandingPageKilau.tentang', compact('tentangMenu', 'tentangs', 'sejarahMenu', 'sejarahs', 'visimisiMenu', 'visimisis'));
    }
}
 