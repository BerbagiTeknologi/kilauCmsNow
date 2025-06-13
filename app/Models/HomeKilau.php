<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeKilau extends Model
{
    use HasFactory;

    protected $table = 'home_kilau';

    const HOME_KILAU_AKTIF = '1';
    const HOME_KILAU_TIDAK_AKTIF = '2';

    protected $fillable = [
        'judul_home',
        'file_home',
        'status_home_kilau'
    ];

    public function getStatusHomeKilauAttribute()
    {
        return $this->attributes['status_home_kilau'] === self::HOME_KILAU_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
