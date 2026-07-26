<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KilauIklan extends Model
{
    use HasFactory;

    /* Iklan Donasi */
    protected $table = 'kilauiklan'; 
    const DONASI_IKLAN_AKTIVE = '1';
    const DONASI_IKLAN_TIDAK_AKTIF = '2';

    protected $fillable = [
        'file',
        'nama',
        'statuskilauiklan',
        'icon_iklan',
        'name_button_iklan',
        'link'
    ];

    public function getStatusDonasiIklanAttribute()
    {
        return (string) $this->attributes['statuskilauiklan'] === self::DONASI_IKLAN_AKTIVE ? 'Aktif' : 'Tidak Aktif';
    }
}
