<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    use HasFactory;

    protected $table = 'galeris';

    const GALERI_AKTIF = 1;
    const GALERI_TIDAK_AKTIF = 2;

    const KANTOR_CABANG_INDRAMAYU = 'Kantor Cabang Indramayu';
    const KANTOR_CABANG_SUMEDANG = 'Kantor Cabang Sumedang';
    const KANTOR_CABANG_BANDUNG = 'Kantor Cabang Bandung';
    const KANTOR_CABANG_MAJALENGKA = 'Kantor Cabang Majalengka';

    // Tentukan kolom yang dapat diisi (fillable)
    protected $fillable = [
        'status_galeri',
        'file_galeri',
        'judul_kegiatan',
        'deskripsi_kegiatan',
        'nama_kantor_cabang',
    ];

    // Tentukan kolom yang harus di-cast (untuk kolom JSON)
    protected $casts = [
        'file_galeri' => 'array', 
    ];

    public function getStatusGaleriAttribute()
    {
        return $this->attributes['status_galeri'] == self::GALERI_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
