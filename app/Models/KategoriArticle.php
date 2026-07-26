<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriArticle extends Model
{
    use HasFactory;

    /* ────── metadata tabel ────── */
    protected $table      = 'kategori_article';   // nama tabel
    protected $primaryKey = 'id';
    protected $keyType    = 'int';                // kolom id = BIGINT UNSIGNED
    public    $incrementing = true;

    /* timestamps aktif (created_at & updated_at) */
    // tidak perlu override konstanta apa-pun

    /* ────── mass-assignment ────── */
    protected $fillable = [
        'name_kategori',
        'status_kategori_article',
    ];

    public const STATUS_AKTIF     = 'Aktif';
    public const STATUS_NON_AKTIF = 'Tidak Aktif';

    /* ────── relasi ──────
       Satu kategori punya banyak artikel  */
    public function articles()
    {
        return $this->hasMany(Article::class, 'kategori_article_id');
    }
}
