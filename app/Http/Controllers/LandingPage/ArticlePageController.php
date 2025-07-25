<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\KomentarArticles;
use App\Http\Controllers\Controller;

class ArticlePageController extends Controller
{
    /* ---------------- halaman utama ---------------- */
    public function index()
    {
        return view('LandingPageKilau.Article.index');
    }

    /* ---------------- JSON list -------------------- */
    public function list()
    {
        $perPage = 6;

        $data = Article::with('kategori')
            ->where('status_artikel', Article::STATUS_AKTIF)
            ->latest('created_at')
            ->paginate($perPage);

        $data->getCollection()->transform(function ($a) {
            $thumbs = collect($a->photo ?? [])
                ->take(2)
                ->map(fn ($p) => asset('storage/' . $p))
                ->values();

            if ($thumbs->isEmpty() &&
                preg_match('/<img[^>]+src="([^">]+)"/i', $a->content, $m)) {
                $thumbs->push($m[1]);
            }

            return [
                'id'      => $a->id,
                'slug'    => $a->slug,                     // ← untuk link
                'title'   => $a->title,
                'kategori'=> $a->kategori?->name_kategori,
                'created' => $a->created_at->format('d M Y'),
                'thumbs'  => $thumbs->all(),
            ];
        });

        return response()->json($data);
    }

    /* ---------- halaman detail ---------- */
    public function show(Article $article)
    {
        $article->increment('views');

        /* foto utama (maks 3) */
        $photos = collect($article->photo ?? [])
                    ->take(3)
                    ->map(fn ($p) => asset('storage/'.$p))
                    ->values();

        if ($photos->isEmpty() &&
            preg_match('/<img[^>]+src="([^">]+)"/i', $article->content, $m)) {
            $photos->push($m[1]);
        }

        /* 5 artikel terbaru (kecuali yg sedang dibuka) + kategori */
        $latest = Article::with('kategori:id,name_kategori')           // ← eager load
                ->where('status_artikel', Article::STATUS_AKTIF)
                ->where('id', '!=', $article->id)
                ->latest('created_at')
                ->take(5)
                ->get(['id','title','slug','photo','content','kategori_article_id'])
                ->map(function ($a) {
                    $thumb = collect($a->photo ?? [])->first()
                            ?? (preg_match('/<img[^>]+src="([^">]+)"/i',$a->content,$m) ? $m[1] : null);

                    return [
                        'title' => $a->title,
                        'slug'  => $a->slug,
                        'thumb' => $thumb ? asset('storage/'.$thumb)
                                            : asset('assets_admin/img/noimage.jpg'),
                        'kategori' => $a->kategori->name_kategori ?? '-',
                    ];
                });

        return view('LandingPageKilau.Article.show',[
            'article'     => $article->load('tags','kategori'),
            'photos'      => $photos,
            'photo_author'=> $article->photo_author, 
            'placeholder' => asset('assets_admin/img/noimage.jpg'),
            'latest'      => $latest,
        ]);
    }

    public function like(Request $request, Article $article)
    {
        $key = 'liked_'.$article->id;          // simpan flag di session

        if (!session()->has($key)) {          // hanya boleh 1× per sesi
            $article->increment('likes');
            session([$key => true]);
        }

        return response()->json(['likes' => $article->likes]);
    }

    // Bagian Function Komentar
     /* =====================================================
       BAGIAN KOMENTAR (digabung di controller yang sama)
       ===================================================== */

    /* GET daftar komentar (nesting replies) */
    public function komentar(Article $article)
    {
        $comments = KomentarArticles::where('id_articles', $article->id)
            ->whereNull('parent_id')
            ->with('replies')          // sekarang rekursif penuh
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $comments
        ]);
    }

    /* POST kirim komentar / balasan */
    public function komentarStore(Request $request, Article $article)
    {
        $validated = $request->validate([
            'nama_pengirim' => 'required|string|max:255',
            'isi_komentar'  => 'required|string',
            'parent_id'     => 'nullable|exists:komentar_articles,id_komentar',
        ]);

        $comment = KomentarArticles::create([
            'id_articles'     => $article->id,
            'nama_pengirim'   => $validated['nama_pengirim'],
            'isi_komentar'    => $validated['isi_komentar'],
            'parent_id'       => $validated['parent_id'] ?? null,
            'status_komentar' => KomentarArticles::STATUS_AKTIF,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Komentar berhasil dikirim',
            'data'    => $comment
        ]);
    }

    /* POST like komentar */
    public function komentarLike(KomentarArticles $comment)
    {
        $key = 'liked_comment_'.$comment->id_komentar;

        if (!session()->has($key)) {
            $comment->increment('likes_komentar');
            session([$key => true]);
        }

        return response()->json([
            'success' => true,
            'likes'   => $comment->likes_komentar
        ]);
    }

}
