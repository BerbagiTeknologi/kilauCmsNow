<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\SettingsMenu;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    /* ---------- list / detail ---------- */
    public function dokumen(Request $request)
    {
        $dokumentMenu = SettingsMenu::find(6);
        $idParam      = $request->query('id');
        $fullscreen   = $request->boolean('fullscreen');

        $dokuments = $idParam
            ? Document::whereKey($idParam)->where('status_document', Document::DOKUMEN_AKTIF)->get()
            : Document::where('status_document', Document::DOKUMEN_AKTIF)->latest()->paginate(6);

        return view('LandingPageKilau.dokumen', compact('dokumentMenu','dokuments'))
               ->with(['singleId'=>$idParam,'fullscreen'=>$fullscreen]);
    }

    /* ---------- share ---------- */
    public function share(string $slug)
    {
        $doc           = Document::where('slug', $slug)->firstOrFail();
        $dokumentMenu  = SettingsMenu::find(6);
        $dokuments     = collect([$doc]);

        // tampilkan halaman yang sama agar crawler membaca meta OG
        return view('LandingPageKilau.dokumen', compact('dokumentMenu','dokuments'))
               ->with(['singleId'=>$doc->id,'fullscreen'=>true]);
    }
}
