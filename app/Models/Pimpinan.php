<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pimpinan extends Model
{
    use HasFactory;

    protected $table = 'pimpinan'; 

    const PIMPINAN_AKTIF = 1;
    const PIMPINAN_TIDAK_AKTIF = 2;

    protected $fillable = [
        'nama',
        'jabatan',
        'deskripsi_diri',
        'sequence_tempat',
        'status_pimpinan',
        'pendidikan',
        'file_pimpinan'
    ];

    protected $casts = [
        'sequence_tempat' => 'integer',
    ];

    public function getStatusPimpinanAttribute()
    {
        // Akses nilai asli dari kolom status
        return $this->attributes['status_pimpinan'] == self::PIMPINAN_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
