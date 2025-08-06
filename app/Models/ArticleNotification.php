<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleNotification extends Model
{
    use HasFactory;

    protected $table = 'article_notifications';

    /* -----------------------------------------------------------------
     * tabel hanya punya kolom created_at → matikan updated_at
     * -----------------------------------------------------------------*/
    public $timestamps = false;   // disable both timestamps
    const CREATED_AT  = 'created_at';
    const UPDATED_AT  = null;

    protected $fillable = [
        'article_id',
        'message',
        'status',          // enum: unread | read
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /* ----------------------------- RELATIONS ----------------------- */
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    /* ----------------------------- CONSTANTS ----------------------- */
    public const UNREAD = 'unread';
    public const READ   = 'read';
}
