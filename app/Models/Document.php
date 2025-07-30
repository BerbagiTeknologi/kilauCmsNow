<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan
    protected $table = 'documents';

    const DOKUMEN_AKTIF = 1;
    const DOKUMEN_TIDAK_AKTIF = 2;

    // Tentukan field yang dapat diisi
    protected $fillable = [
        'text_document',
        'files',
        'thumbnail', 
        'status_document',
        'slug',
    ];

    public function getStatusDokumenAttribute()
    {
        return $this->attributes['status_document'] == self::DOKUMEN_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }

      /* ----------- Auto-generate slug ----------- */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($doc) {
            if (empty($doc->slug)) {
                $base = Str::slug(strip_tags($doc->text_document) ?: 'dokumen');
                $doc->slug = static::uniqueSlug($base);
            }
        });
    }

    private static function uniqueSlug($base)
    {
        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }

    /* ---------- helper gambar share ---------- */
    public function getShareImageAttribute(): string
    {
        return $this->thumbnail
            ? Storage::url($this->thumbnail)
            : asset('assets/img/default-image.jpg');
    }
}
