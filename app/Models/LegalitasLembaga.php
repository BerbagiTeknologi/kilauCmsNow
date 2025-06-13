<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalitasLembaga extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan
    protected $table = 'legalitas_lembaga';

    const LEGALITAS_AKTIF = 1;
    const LEGALITAS_TIDAK_AKTIF = 2;

    // Tentukan field yang dapat diisi
    protected $fillable = [
        'judul',
        'file_legalitas',
        'status_legalitas'
    ];

    public function getStatusLegalitasAttribute()
    {
        return $this->attributes['status_legalitas'] == self::LEGALITAS_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }

    
}
