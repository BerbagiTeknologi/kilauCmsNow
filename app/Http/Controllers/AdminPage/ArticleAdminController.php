<?php

namespace App\Http\Controllers\AdminPage;

use App\Models\Article;
use App\Models\TagArticle;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ArticleAdminController extends Controller
{
    /* ---------- Page ---------- */
    public function index()
    {
        return view('AdminPage.Artikel.index');
    }

    /* ---------- JSON list ---------- */
    public function list(Request $request): JsonResponse
    {
        $search = $request->input('search', '');

        /* eager-load relasi kategori (hanya id & name_kategori) */
        $articles = Article::with('kategori:id,name_kategori')
            ->when($search, fn ($q) =>
                $q->where('title','like',"%{$search}%"))
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($a) {
                return [
                    'id'             => $a->id,
                    'title'          => $a->title,
                    'author'         => $a->author,
                    'created_at'     => $a->created_at,
                    'status_artikel' => $a->status_artikel,
                    /* ambil satu foto pertama (jika ada) */
                    'photo'          => $a->photo[0] ?? null,
                    /* relasi kategori → bisa null */
                    'kategori'       => $a->kategori
                        ? ['name_kategori' => $a->kategori->name_kategori]
                        : null,
                ];
            });

        return response()->json($articles);
    }


    /* ---------- Store ---------- */
    public function createArticle(Request $request)
    {
        /* ---------- VALIDASI ---------- */
        $request->validate([
            'kategori_article_id' => 'required|exists:kategori_article,id',
            'title'   => 'required|string|max:255',
            'author'  => 'nullable|string|max:255',
            'content' => 'required|string',

            // foto utama (multi)
            'photo'   => 'nullable|array',
            'photo.*' => 'image|mimes:jpg,jpeg,png|max:2048',

            // tags (opsional, dinamis)
            'tags'               => 'nullable|array',
            'tags.*.nama_tags'   => 'required_with:tags|string|max:255',
            'tags.*.link'        => 'required_with:tags|string|max:255',
        ]);

        /* ---------- SIMPAN FOTO ---------- */
        $paths = [];
        if ($request->hasFile('photo')) {
            foreach ($request->file('photo') as $file) {
                $paths[] = $file->store('articles', 'public');
            }
        }

        /* ---------- SIMPAN ARTIKEL ---------- */
        $article = Article::create([
            'kategori_article_id' => $request->kategori_article_id, 
            'title'          => $request->title,
            'author'         => $request->author ?? auth()->user()->name ?? null,
            'content'        => $request->content,
            'photo'          => $paths,          // cast → array
            'status_artikel' => Article::STATUS_AKTIF,
        ]);

        /* ---------- TAGS & PIVOT ---------- */
        if ($request->filled('tags')) {
            $tagIds = collect($request->input('tags'))
                ->map(function ($tag) {
                    // jika nama tag sudah ada → update link-nya
                    $record = TagArticle::updateOrCreate(
                        ['nama_tags' => $tag['nama_tags']],
                        ['link'      => $tag['link']]
                    );
                    return $record->id;
                })
                ->all();

            // isi tabel article_tag
            $article->tags()->sync($tagIds);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Artikel berhasil ditambahkan!',
        ]);
    }

    public function uploadImage(Request $request)
    {
        /* ---------- validasi ---------- */
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        /* ---------- info dasar ---------- */
        $file = $request->file('image');
        $ext  = $file->getClientOriginalExtension();          // jpg | png | gif
        $name = time() . '-' . Str::random(10) . '.' . $ext;  // nama unik

        $disk   = Storage::disk('public');                    // storage/app/public
        $folder = 'articles_content';
        if (!$disk->exists($folder)) {
            $disk->makeDirectory($folder);                    // buat folder jika belum ada
        }

        /* ---------- resize sederhana (± 500 px) ---------- */
        [$w, $h] = getimagesize($file);
        $maxW = 500;
        $newW = $w > $maxW ? $maxW : $w;
        $newH = $w > $maxW ? intval($h * $newW / $w) : $h;

        $dst = imagecreatetruecolor($newW, $newH);
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $src = imagecreatefromjpeg($file);
                break;
            case 'png':
                $src = imagecreatefrompng($file);
                /* pertahankan transparansi PNG */
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                break;
            case 'gif':
                $src = imagecreatefromgif($file);
                break;
            default:
                return response()->json(['success' => false, 'msg' => 'Format tidak didukung'], 400);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

        /* ---------- tulis ke file sementara ---------- */
        $tempPath = tempnam(sys_get_temp_dir(), 'art_');      // ← SEKARANG ADA
        match ($ext) {
            'jpg', 'jpeg' => imagejpeg($dst, $tempPath, 90),
            'png'         => imagepng($dst, $tempPath, 9),
            'gif'         => imagegif($dst, $tempPath),
        };

        /* ---------- pindahkan ke storage/public ---------- */
        $disk->put($folder . '/' . $name, file_get_contents($tempPath));

        /* ---------- bersih-bersih ---------- */
        imagedestroy($dst);
        imagedestroy($src);
        @unlink($tempPath);

        return response()->json([
            'success'   => true,
            'image_url' => asset('storage/' . $folder . '/' . $name),
        ]);
    }


    public function showArticle($id)
    {
        // eager-load relasi tags agar hanya 1 query
        $article = Article::with('tags','kategori')->findOrFail($id);

        // foto → [{path, url}, …]
        $photos = collect($article->photo ?? [])
            ->map(fn ($p) => ['path' => $p, 'url' => asset('storage/'.$p)]);

        return response()->json([
            'id'      => $article->id,
            'title'   => $article->title,
            'author'  => $article->author,
            'content' => $article->content,
            'photos'  => $photos,
             'tags'    => $article->tags->map(fn ($t) => [
                        'nama_tags' => $t->nama_tags,
                        'link'      => $t->link,
                    ]),
             'kategori'            => $article->kategori?->name_kategori,
            'kategori_article_id'=> $article->kategori_article_id, 
        ]);
    }

    /* ---------------- hapus 1 foto lama ---------------- */
    public function deletePhoto(Request $req, $id)
    {
        $req->validate(['path' => 'required|string']);
        $path = $req->path;                       // "articles/xxx.jpg"

        $article = Article::findOrFail($id);
        if (!in_array($path, $article->photo ?? [])) {
            return response()->json(['message' => 'Foto tidak ditemukan.'], 404);
        }

        Storage::disk('public')->delete($path);   // hapus file
        $article->photo = array_values(array_diff($article->photo, [$path]));
        $article->save();

        return response()->json(['message' => 'Foto dihapus.']);
    }

    /* ---------------- update artikel ------------------ */
    public function updateArticle(Request $req, $id)
    {
        $article = Article::findOrFail($id);

        /* ---------- VALIDASI ---------- */
        $req->validate([
            'kategori_article_id'=> 'required|exists:kategori_article,id',
            'title'   => 'required|string|max:255',
            'author'  => 'nullable|string|max:255',
            'content' => 'required|string',

            'photo'   => 'nullable|array',
            'photo.*' => 'image|mimes:jpg,jpeg,png|max:2048',

            /*  tags  */
            'tags'               => 'nullable|array',
            'tags.*.nama_tags'   => 'required_with:tags|string|max:255',
            'tags.*.link'        => 'required_with:tags|string|max:255',
        ]);

        /* ---------- UPLOAD FOTO BARU ---------- */
        $paths = [];
        if ($req->hasFile('photo')) {
            foreach ($req->file('photo') as $file) {
                $paths[] = $file->store('articles', 'public');
            }
        }

        /* ---------- UPDATE ARTIKEL ---------- */
        $article->update([
            'kategori_article_id'=> $req->kategori_article_id, 
            'title'   => $req->title,
            'author'  => $req->author,
            'content' => $req->content,
            'photo'   => array_merge($article->photo ?? [], $paths),
        ]);

        /* ---------- TAGS & PIVOT ---------- */
        if ($req->filled('tags')) {
            $tagIds = collect($req->input('tags'))
                ->map(function ($tag) {
                    $record = TagArticle::updateOrCreate(
                        ['nama_tags' => $tag['nama_tags']],
                        ['link'      => $tag['link']]
                    );
                    return $record->id;
                })
                ->all();

            $article->tags()->sync($tagIds);   // simpan & drop yg dihapus user
        } else {
            $article->tags()->detach();        // bila user kosongkan semua tag
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Artikel diperbarui!',
        ]);
    }

     public function toggleStatus(Request $req, int $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        // Toggle
        $newStatus = $article->status_artikel === Article::STATUS_AKTIF
                     ? Article::STATUS_NON_AKTIF
                     : Article::STATUS_AKTIF;

        $article->update(['status_artikel' => $newStatus]);

        return response()->json([
            'status'  => 'success',
            'message' => "Status diubah menjadi {$newStatus}.",
            'newStatus' => $newStatus,
        ]);
    }
 
    public function deleteArticle(Request $request)
    {
        $article = Article::find($request->id);

        if (!$article) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Artikel tidak ditemukan.'
            ], 404);
        }

        if ($article->photo) {
            Storage::disk('public')->delete($article->photo);
        }

        $article->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Artikel berhasil dihapus!'
        ]);
    }
}
