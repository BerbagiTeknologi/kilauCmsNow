<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class BeritaController extends Controller
{
    /* public function berita()
    {
        // Ambil daftar kategori dari API
        $response = Http::get("https://berbagipendidikan.org/api/kategori-berita");
        $kategoriBerita = $response->successful() ? $response->json()['data']['data'] ?? [] : [];

        return view('LandingPageKilau.Berita.berita', compact('kategoriBerita'));
    } */

    public function berita(Request $request)
    {
        // Ambil kategori berita
        $responseKategori = Http::get("https://berbagipendidikan.org/api/kategori-berita");
        $kategoriBerita = $responseKategori->successful() ? $responseKategori->json()['data']['data'] ?? [] : [];
    
        // Siapkan default variabel agar tidak undefined
        $page    = (int) $request->query('camp_page', 1);
        $perPage = (int) $request->query('camp_per_page', 5);
        $campaigns = collect();
        $lastPage  = $page;

        try {
            $response = Http::withoutRedirecting()->get(
                'https://berbagibahagia.org/api/getcampung',
                ['page' => $page, 'per_page' => $perPage, 'status' => 1]
            );

            if ($response->successful()) {
                $campaigns = collect($response['data']);

                $lastPage = (int) ($response['last_page']
                        ?? $response->json('meta.last_page')
                        ?? ($campaigns->count() === $perPage ? $page + 1 : $page));
            }
        } catch (\Exception $e) {
            Log::error('Error fetching Campaign: '.$e->getMessage());
        }


        $programs = Program::where('status_program', Program::PROGRAM_AKTIF)
        ->select('id','judul','thumbnail_image')
        ->get();
    
        return view('LandingPageKilau.Berita.berita', compact(
            'kategoriBerita','campaigns','page','perPage','lastPage','programs'
        ));
    }
    
    
    /* public function show($judul)
    {
        // Ganti spasi dengan tanda hubung pada judul (sesuaikan dengan format URL)
        $judul = str_replace('-', ' ', $judul); // Mengganti tanda hubung menjadi spasi

        // Ambil data berita dari API berdasarkan judul
        $response = Http::get("https://berbagipendidikan.org/api/beritajuduls/{$judul}");

        if ($response->successful()) {
            $berita = $response->json()['data'] ?? null; // Ambil data berita dari response API
            return view('LandingPageKilau.Berita.berita', compact('berita'));
        } else {
            abort(404); // Jika berita tidak ditemukan, tampilkan error 404
        }
    } */

    public function show($judul)
    {
        $judul = str_replace('-', ' ', $judul);
        $response = Http::get("https://berbagipendidikan.org/api/beritajuduls/{$judul}");

        if ($response->successful()) {
            $berita = $response->json()['data'] ?? null;

            // Ambil semua campaign TANPA pagination
            $campaigns = collect();
            try {
                $resCamp = Http::withoutRedirecting()->get('https://berbagibahagia.org/api/getcampung', [
                    'page' => 1,
                    'per_page' => 10, // ambil besar agar semua terload
                    'status' => 1,
                ]);
                if ($resCamp->successful()) {
                    $campaigns = collect($resCamp['data']);
                }
            } catch (\Exception $e) {
                Log::error('Error fetching Campaigns for show(): '.$e->getMessage());
            }

            $programs = Program::where('status_program', Program::PROGRAM_AKTIF)
            ->select('id','judul','thumbnail_image')
            ->get();

            return view('LandingPageKilau.Berita.berita', compact(
                'berita','campaigns','programs'
            ));
        }

        abort(404);
    }

    /* Get Berita Users Di dashboardnya */
    public function getBeritaUsers() {
        return view('LandingPageKilau.Components.berita-users');
    }


}
