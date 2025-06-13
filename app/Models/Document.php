<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'status_document'
    ];

    public function getStatusDokumenAttribute()
    {
        return $this->attributes['status_document'] == self::DOKUMEN_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
