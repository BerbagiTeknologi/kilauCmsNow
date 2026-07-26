<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use HasFactory;

    protected $table = 'kontak'; // Nama tabel di database

    const KONTAK_AKTIF = 1;
    const KONTAK_TIDAK_AKTIF = 2;

    protected $fillable = [
        'nama_kacab',
        'alamat',
        'telephone',
        'email',
        'maplink',
        'status_kontak'
    ];

    public function getStatusKontakAttribute()
    {
        return $this->attributes['status_kontak'] == self::KONTAK_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
