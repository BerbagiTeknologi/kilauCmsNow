<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $table = 'faqs';

    const FAQ_AKTIF = 'Aktif';
    const FAQ_TIDAK_AKTIF = 'Tidak Aktif';

    protected $fillable = [
        'question',
        'answer',
        'status_faqs',
    ];

    public function getStatusFaqsAttribute()
    {
        return $this->attributes['status_faqs'] === self::FAQ_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
