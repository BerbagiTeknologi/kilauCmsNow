<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sejarah extends Model
{
    use HasFactory;

    protected $table = 'sejarahh';

    const SEJARAH_AKTIF = '1';
    const SEJARAH_TIDAK_AKTIF = '2';

    protected $fillable = [
        'deskripsi_sejarah',
        'nama_file',
        'status_sejarah'
    ];

    public function getStatusSejarahAttribute()
    {
        return $this->attributes['status_sejarah'] === self::SEJARAH_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
