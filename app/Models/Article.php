<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    /* ---------- konfigurasi dasar ---------- */
    protected $table      = 'articles';
    protected $primaryKey = 'id';
    public    $incrementing = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /* ---------- mass assignment ------------- */
    protected $fillable = [
        'title',
        'slug',              // ← kolom baru
        'author',
        'content',
        'photo',
        'views',
        'likes',
        'status_artikel',
        'kategori_article_id',
    ];

    /* ---------- casting kolom --------------- */
    protected $casts = [
        'photo' => 'array',
        'views' => 'integer',
        'likes' => 'integer',
    ];

    /* ---------- route-model binding pakai slug */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ---------- auto-generate slug ----------- */
    protected static function booted(): void
    {
        static::saving(function ($article) {

            $base = Str::slug($article->title);     // hasil: testing-kok

            if (!$article->slug || $article->isDirty('title')) {
                $slug = $base;
                $i    = 2;                          // mulai 2, BUKAN id

                while (static::where('slug', $slug)
                            ->where('id', '!=', $article->id ?? 0)
                            ->exists()) {
                    $slug = "{$base}-{$i}";         // testing-kok-2, -3, ...
                    $i++;
                }
                $article->slug = $slug;
            }
        });
    }

    /* ---------- helper & relasi ------------- */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function tags()
    {
        return $this->belongsToMany(
            TagArticle::class,
            'article_tag',
            'article_id',
            'tag_id'
        )->withTimestamps('created_at', 'updated_at');
    }

    public function kategori()
    {
        return $this->belongsTo(
            KategoriArticle::class,
            'kategori_article_id'
        );
    }

    /* ---------- konstanta status ------------- */
    public const STATUS_AKTIF     = 'Aktif';
    public const STATUS_NON_AKTIF = 'Tidak Aktif';
}
