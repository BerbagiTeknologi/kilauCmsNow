<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Models\KomentarArticles;
use Illuminate\Http\Request;

class ArticleKomentarAdminController extends Controller
{
    /* ===== view blade ===== */
    public function index()
    {
        return view('AdminPage.Artikel.Komentar.index');
    }

    /* ===== JSON list ===== */
    public function list(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $search  = $request->input('search');

        $query = KomentarArticles::with(['article:id,title', 'replies'])
                    ->whereNull('parent_id')
                    ->orderByDesc('created_at');

        if ($search) {
            $query->where('nama_pengirim','like',"%{$search}%");
        }

        $paginated = $query->paginate($perPage);

        /* rekursif mapping ke array sederhana */
        $data = $paginated->getCollection()
            ->map(fn($c) => $this->mapComment($c))
            ->values();

        return response()->json([
            'status'     => true,
            'data'       => $data,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage()
            ]
        ]);
    }

    /* ===== toggle status ===== */
    public function toggleStatus($id)
    {
        $komentar = KomentarArticles::findOrFail($id);
        $komentar->status_komentar =
            $komentar->status_komentar === KomentarArticles::STATUS_AKTIF
            ? KomentarArticles::STATUS_TIDAK_AKTIF
            : KomentarArticles::STATUS_AKTIF;
        $komentar->save();

        return response()->json([
            'message' => 'Status diperbarui'
        ]);
    }

    /* ===== hapus komentar & semua balasan ===== */
    public function destroy($id)
    {
        $komentar = KomentarArticles::with('replies')->findOrFail($id);

        /* hapus rekursif */
        $this->deleteBranch($komentar);

        return response()->json([
            'message' => 'Komentar (dan balasan) dihapus'
        ]);
    }

    /* ---------- util rekursif ---------- */
    private function deleteBranch(KomentarArticles $comment): void
    {
        foreach ($comment->replies as $child) {
            $this->deleteBranch($child);
        }
        $comment->delete();
    }

    private function mapComment(KomentarArticles $c): array
    {
        return [
            'id_komentar'   => $c->id_komentar,
            'nama_pengirim' => $c->nama_pengirim,
            'isi_komentar'  => $c->isi_komentar,
            'status_komentar'=> $c->status_komentar,
            'likes_komentar'=> $c->likes_komentar,
            'created_at'    => $c->created_at->format('d-m-Y H:i'),
            'berita'        => ['judul' => $c->article->title ?? '-'],
            'replies'       => $c->replies->map(fn($r)=>$this->mapComment($r))->values(),
        ];
    }
}
