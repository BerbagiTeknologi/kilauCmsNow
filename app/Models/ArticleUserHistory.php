<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleUserHistory extends Model
{
    protected $table = 'article_user_histories';
    public  $timestamps = false;   // cuma pakai created_at manual
    protected $fillable = [
        'article_id',
        'external_user_id',
        'token',
        'created_at',
    ];

    /* relasi opsional */
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
