<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagArticle extends Model
{
    use HasFactory;

    /* ────────────── dasar tabel ────────────── */
    protected $table      = 'tags_article';
    protected $primaryKey = 'id';
    protected $keyType    = 'int';
    public    $incrementing = true;

    /* hanya kolom created_at */
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'nama_tags',
        'link',
    ];

    /* ────────────── relasi ────────────── */
   public function articles()
    {
        return $this->belongsToMany(
                Article::class,
                'article_tag',
                'tag_id',
                'article_id'
            )->withTimestamps('created_at', 'updated_at');
    }

}
