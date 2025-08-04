<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Article;
use App\Models\TagArticle;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\KategoriArticle;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

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
}
