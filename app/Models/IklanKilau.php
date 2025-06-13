<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IklanKilau extends Model
{
    use HasFactory;

    protected $table = 'iklan_kilau'; // Nama tabel di database

    const IKLAN_KILAU_AKTIF = '1';
    const IKLAN_KILAU_TIDAK_AKTIF = '2';

    protected $fillable = [
        'judul',
        'deskripsi',
        'file',
        'jumlah_yayasan',
        'jumlah_donatur',
        'status_kilau'
    ];

    public function getStatusIklanKilauAttribute()
    {
        return $this->attributes['status_kilau'] === self::IKLAN_KILAU_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }

    public function iklanKilauLists()
    {
        return $this->hasMany(IklanKilauList::class, 'iklan_kilau_id', 'id');
    }
}
