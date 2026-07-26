<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Galeri;

class GaleryController extends Controller
{
    public function galery(Request $request)
    {
        $galeri = Galeri::paginate(3);

        if ($request->ajax()) {
            $galeriHtml = view('LandingPageKilau.galeri-items', compact('galeri'))->render();
            $paginationHtml = $galeri->links()->render();

            return response()->json([
                'galeri' => $galeriHtml,
                'pagination' => $paginationHtml
            ]);
        }

        return view('LandingPageKilau.galeri', compact('galeri'));
    }

}
