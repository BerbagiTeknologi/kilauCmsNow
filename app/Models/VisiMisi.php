<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisiMisi extends Model
{
    use HasFactory;

    protected $table = 'visimisis';

    const VISI_MISI_AKTIF = '1';
    const VISI_MISI_TIDAK_AKTIF = '2';

    protected $fillable = [
        'visi',
        'misi',
        'status_visimisi'
    ];

    public function getStatusVisiMisiAttribute()
    {
        return $this->attributes['status_visimisi'] === self::VISI_MISI_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
