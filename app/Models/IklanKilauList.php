<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IklanKilauList extends Model
{
    use HasFactory;

    protected $table = 'iklan_kilau_list'; // Nama tabel di database

    const IKLAN_KILAU_LIST_AKTIF = '1';
    const IKLAN_KILAU_LIST_TIDAK_AKTIF = '2';

    protected $fillable = [
        'name',
        'iklan_kilau_id',
        'status_iklan_kilau_list'
    ];

    public function getStatusIklanKilauListAttribute()
    {
        return $this->attributes['status_iklan_kilau_list'] === self::IKLAN_KILAU_LIST_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }

    public function iklanKilau()
    {
        return $this->belongsTo(IklanKilau::class, 'iklan_kilau_id', 'id');
    }

}
