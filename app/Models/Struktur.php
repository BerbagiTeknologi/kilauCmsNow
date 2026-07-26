<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Struktur extends Model
{
    use HasFactory;

    protected $table = 'struktur'; // Nama tabel di database

    const STRUKTUR_AKTIF = '1';
    const STRUKTUR_TIDAK_AKTIF = '2';

    protected $fillable = [
        'name_judul',
        'file',
        'status_struktur'
    ];

    public function getStatusStrukturAttribute()
    {
        return $this->attributes['status_struktur'] === self::STRUKTUR_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
