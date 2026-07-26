<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TentangKami extends Model
{
    use HasFactory;

    protected $table = 'tentang_kami';

    const TENTANG_AKTIF = '1';
    const TENTANG_TIDAK_AKTIF = '2';

    protected $fillable = [
        'judul_tentang_kami',
        'deskripsi',
        'file',
        'status_tentang_kami'
    ];

    public function getStatusTentangKamiAttribute()
    {
        return $this->attributes['status_tentang_kami'] === self::TENTANG_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
