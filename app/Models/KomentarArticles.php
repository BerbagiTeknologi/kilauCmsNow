<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KomentarArticles extends Model
{
    use HasFactory;

    protected $table = 'komentar_articles';
    protected $primaryKey = 'id_komentar';

    protected $fillable = [
        'id_articles',
        'nama_pengirim',
        'isi_komentar',
        'parent_id',
        'status_komentar',
        'likes_komentar',
    ];

    const STATUS_AKTIF = 'Aktif';
    const STATUS_TIDAK_AKTIF = 'Tidak Aktif';

    public function article()
    {
        return $this->belongsTo(Article::class, 'id_articles', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(KomentarArticles::class, 'parent_id');
    }

    public function replies()
    {
        /* rekursif tak terbatas */
        return $this->hasMany(self::class, 'parent_id')
                    ->with('replies')          // <─ inilah kuncinya
                    ->orderBy('created_at');   // urutkan kalau mau
    }


    public function getStatusKomentarAttribute($value)
    {
        return $value ?? self::STATUS_AKTIF;
    }

    public function setStatusKomentarAttribute($value)
    {
        $this->attributes['status_komentar'] = in_array($value, [self::STATUS_AKTIF, self::STATUS_TIDAK_AKTIF])
            ? $value
            : self::STATUS_TIDAK_AKTIF;
    }
}
