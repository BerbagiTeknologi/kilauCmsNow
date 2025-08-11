<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Article;
use App\Models\TagArticle;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\KategoriArticle;
use App\Models\ArticleUserHistory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ArticlePageExternalController extends Controller
{
    /* ——— PAGE ——— */
    public function index()
    {
        return view('LandingPageKilau.Components.article-users');
    }

    /* ——— LIST JSON ——— */
    public function list(Request $request)
    {
        $perPage = (int) $request->input('per_page', 6);
        $search  = $request->input('search', '');

        $paginator = Article::with('kategori:id,name_kategori')
            ->where('status_artikel', Article::STATUS_AKTIF)
            ->when($search, fn ($q) => $q->where('title','like',"%{$search}%"))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $data = $paginator->getCollection()->transform(function ($a) {
            return [
                'id'      => $a->id,
                'title'   => $a->title,
                'slug'    => $a->slug,
                'author'  => $a->author,
                'tanggal' => $a->created_at->toDateString(),
                'status_artikel' => $a->status_artikel,
                'photos'  => collect($a->photo)->map(fn ($p) => asset('storage/'.$p)),
                'kategori'       => $a->kategori
                                ? ['id' => $a->kategori->id,
                                   'name_kategori' => $a->kategori->name_kategori]
                                : null,
            ];
        });

        return response()->json([
            'data'       => $data,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }

    public function myArticles(Request $request)
    {
        $userId = session('user_id');          // ← di‐set saat login
        if (!$userId) {
            return response()->json([
                'data' => [],
                'pagination' => null,
                'error' => 'Unauthenticated',
            ], 401);
        }

        $perPage = (int) $request->input('per_page', 6);
        $search  = $request->input('search', '');

        /* ambil artikel yang ada di tabel history milik user */
        $paginator = Article::with('kategori:id,name_kategori')
            ->whereHas('histories', fn ($q) => $q->where('external_user_id', $userId))
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        /* transform sama persis dgn list() lama */
        $data = $paginator->getCollection()->transform(function ($a) {
            return [
                'id'      => $a->id,
                'title'   => $a->title,
                'slug'    => $a->slug,
                'author'  => $a->author,
                'tanggal' => $a->created_at->toDateString(),
                'status_artikel' => $a->status_artikel,
                'feedback' => $a->feedback,
                'photos'  => collect($a->photo)
                            ->map(fn ($p) => asset('storage/' . $p)),
                'kategori' => $a->kategori
                    ? ['id' => $a->kategori->id,
                    'name_kategori' => $a->kategori->name_kategori]
                    : null,
            ];
        });

        return response()->json([
            'data'       => $data,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }

    public function categories()
    {
        $cats = KategoriArticle::where('status_kategori_article', KategoriArticle::STATUS_AKTIF)
                ->orderBy('name_kategori')
                ->get(['id','name_kategori']);

        return response()->json(['data' => $cats]);
    }

     public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        /* simpan ke storage/app/public/articles_content */
        $file   = $request->file('image');
        $folder = 'articles_content';
        $name   = time().'-'.Str::random(10).'.'.$file->getClientOriginalExtension();

        $manager = ImageManager::gd();               // kualitas resize lebih mudah
        $img     = $manager->read($file)->scaleDown(width:500);
        Storage::disk('public')->put($folder.'/'.$name, (string) $img->encode());

        return response()->json([
            'success'   => true,
            'image_url' => asset('storage/'.$folder.'/'.$name),
        ]);
    }

    /* ——— STORE ——— */
    public function store(Request $request)
    {
        $val = $request->validate([
            'kategori_article_id' => 'required|exists:kategori_article,id',
            'judul'   => 'required|string|max:255',
            'author'  => 'nullable|string|max:255',
            'konten'  => 'required|string',
            'tanggal' => 'required|date',
            'photo'   => 'nullable|array|max:3',
            'photo.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'tags'            => 'nullable|array',
            'tags.*.nama'     => 'required_with:tags|string|max:255',
            'tags.*.link'     => 'required_with:tags|url|max:255',
        ]);

        /* simpan foto */
        $paths = [];
        if ($request->hasFile('photo')) {
            foreach ($request->file('photo') as $file) {
                $paths[] = $file->store('articles', 'public');
            }
        }

        /* simpan artikel */
        $article = Article::create([
            'kategori_article_id' => $val['kategori_article_id'],
            'title'          => $val['judul'],
            'author'         => $val['author'] ?? auth()->user()->name ?? null,
            'content'        => $val['konten'],
            'photo'          => $paths,
            'status_artikel' => Article::STATUS_NON_AKTIF,
            'created_at'     => $val['tanggal'],
        ]);

         $externalUserId = session('user_id');
        if (!$externalUserId) {
            return response()->json([
                'error' => 'User tidak terautentikasi',
            ], 401);
        }

        ArticleUserHistory::create([
            'article_id'       => $article->id,
            'external_user_id' => $externalUserId,
            'token'            => session('user_token'),
        ]);

        /* pivot tags */
        if (!empty($val['tags'])) {
            $tagIds = collect($val['tags'])->map(function ($t) {
                return TagArticle::updateOrCreate(
                    ['nama_tags' => $t['nama']],
                    ['link'      => $t['link']]
                )->id;
            });
            $article->tags()->sync($tagIds);
        }

        return response()->json(['success' => true, 'msg' => 'Artikel eksternal tersimpan!']);
    }

    public function show($id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $article = Article::with(['tags:id,nama_tags,link', 'kategori:id,name_kategori'])
            ->where('id', $id)
            ->whereHas('histories', fn ($q) => $q->where('external_user_id', $userId))
            ->firstOrFail();

        // foto: [{path,url}, ...]
        $photos = collect($article->photo ?? [])->map(fn ($p) => [
            'path' => $p,
            'url'  => asset('storage/'.$p),
        ]);

        return response()->json([
            'id'        => $article->id,
            'title'     => $article->title,
            'author'    => $article->author,
            'content'   => $article->content,
            'photos'    => $photos,
            'photo_author' => $article->photo_author,
            'tags'      => $article->tags->map(fn ($t) => [
                'nama_tags' => $t->nama_tags,
                'link'      => $t->link,
            ]),
            'kategori'            => $article->kategori?->name_kategori,
            'kategori_article_id' => $article->kategori_article_id,
            'status_artikel'      => $article->status_artikel,  // ← penting
            'feedback'            => $article->feedback,        // ← penting
            'tanggal'             => optional($article->created_at)->toDateString(),
        ]);
    }


    public function edit($id)
    {
        $userId = session('user_id');

        $article = Article::with(['kategori:id,name_kategori', 'tags:id,nama_tags,link'])
            ->where('id', $id)
            ->whereHas('histories', fn ($q) => $q->where('external_user_id', $userId))
            ->firstOrFail();

        return response()->json([
            'id'       => $article->id,
            'kategori' => $article->kategori_article_id,
            'title'    => $article->title,
            'author'   => $article->author,
            'tanggal'  => $article->created_at->toDateString(),
            'content'  => $article->content,
            'photos'   => collect($article->photo)->map(fn ($p) => asset('storage/'.$p)),
            'tags'     => $article->tags->map(fn ($t) => [
                            'nama' => $t->nama_tags,
                            'link' => $t->link,
                        ]),
            'status_artikel' => $article->status_artikel, // ← tambahan
            'feedback'       => $article->feedback,       // ← tambahan
        ]);
    }


    /* ---------- UPDATE ---------- */
    public function update(Request $request, $id)
    {
        $userId = session('user_id');

        $article = Article::where('id', $id)
            ->whereHas('histories', fn ($q) => $q->where('external_user_id', $userId))
            ->firstOrFail();

        $val = $request->validate([
            'kategori_article_id' => ['required', Rule::exists('kategori_article', 'id')],
            'judul'   => 'required|string|max:255',
            'author'  => 'nullable|string|max:255',
            'konten'  => 'required|string',
            'tanggal' => 'required|date',
            'photo'   => 'nullable|array|max:3',
            'photo.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'tags'            => 'nullable|array',
            'tags.*.nama'     => 'required_with:tags|string|max:255',
            'tags.*.link'     => 'required_with:tags|url|max:255',
        ]);

        DB::transaction(function () use ($article, $val, $request) {

            if ($request->hasFile('photo')) {
                $paths = [];
                foreach ($request->file('photo') as $file) {
                    $paths[] = $file->store('articles', 'public');
                }
                $article->photo = $paths;
            }

            // update + RESET status & feedback
            $article->fill([
                'kategori_article_id' => $val['kategori_article_id'],
                'title'               => $val['judul'],
                'author'              => $val['author'] ?? $article->author,
                'content'             => $val['konten'],
                'created_at'          => $val['tanggal'],
                'status_artikel'      => Article::STATUS_NON_AKTIF, // ← reset
                'feedback'            => null,                      // ← kosongkan
            ])->save();

            if (array_key_exists('tags', $val)) {
                $tagIds = collect($val['tags'])->map(function ($t) {
                    return TagArticle::updateOrCreate(
                        ['nama_tags' => $t['nama']],
                        ['link'      => $t['link']]
                    )->id;
                });
                $article->tags()->sync($tagIds);
            }
        });

        return response()->json(['success' => true, 'msg' => 'Artikel berhasil di-update!']);
    }

}

