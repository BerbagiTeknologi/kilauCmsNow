<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraDonatur extends Model
{
    use HasFactory;

    protected $table = 'mitra_donatur';

    const MITRA_AKTIF = 1;
    const MITRA_TIDAK_AKTIF = 2;

    protected $fillable = [
        'nama_mitra',
        'file',
        'status_mitra'
    ];

    public function getStatusMitraAttribute()
    {
        return $this->attributes['status_mitra'] == self::MITRA_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }

    public function program()
    {
        return $this->belongsToMany(Program::class, 'program_mitra', 'mitra_id', 'program_id');
    }


}
