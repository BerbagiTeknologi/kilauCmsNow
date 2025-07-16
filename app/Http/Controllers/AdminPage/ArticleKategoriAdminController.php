<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriArticle;

class ArticleKategoriAdminController extends Controller
{
    /* ========== VIEW ========= */
    public function getKategoriArticle()
    {
        return view('AdminPage.Artikel.Kategori.index');
    }

    /* ========== LIST (JSON) ========= */
    public function list(Request $r)
    {
        $perPage = $r->per_page ?? 10;          // default 5 baris
        $cats = KategoriArticle::query()
                ->when($r->search, fn ($s,$v) =>
                        $s->where('name_kategori','like',"%$v%"))
                ->orderByDesc('id')
                ->paginate($perPage);

        /* Laravel paginate() sudah berisi:
        data, current_page, last_page, etc. */
        return response()->json($cats);
    }

    /* ========== SHOW ========= */
    public function show($id)
    {
        return response()->json(
            KategoriArticle::findOrFail($id)
        );
    }

    /* ========== STORE ========= */
    public function store(Request $r)
    {
        $r->validate([
            'name_kategori' => 'required|string|max:255|unique:kategori_article,name_kategori',
        ]);

        KategoriArticle::create([
            'name_kategori'           => $r->name_kategori,
            'status_kategori_article' => 'Aktif',
        ]);

        return response()->json(['message'=>'Kategori ditambahkan!']);
    }

    /* ========== UPDATE ========= */
    public function update(Request $r, $id)
    {
        $kat = KategoriArticle::findOrFail($id);

        $r->validate([
            'name_kategori' => 'required|string|max:255|unique:kategori_article,name_kategori,'.$kat->id,
        ]);

        $kat->update(['name_kategori'=>$r->name_kategori]);

        return response()->json(['message'=>'Kategori diperbarui!']);
    }

    public function toggleStatus($id)
    {
        $kat = KategoriArticle::findOrFail($id);

        $kat->status_kategori_article =
            $kat->status_kategori_article === KategoriArticle::STATUS_AKTIF
            ? KategoriArticle::STATUS_NON_AKTIF
            : KategoriArticle::STATUS_AKTIF;

        $kat->save();

        return response()->json(['message'=>'Status diperbarui!']);
    }


    /* ========== DESTROY ========= */
    public function destroy($id)
    {
        KategoriArticle::findOrFail($id)->delete();
        return response()->json(['message'=>'Kategori dihapus!']);
    }
}
