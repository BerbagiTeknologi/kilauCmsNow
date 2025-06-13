<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimlineKilau extends Model
{
    use HasFactory;

    protected $table = 'timline_kilau'; 

    const TIMELINE_AKTIF = 1;
    const TIMELINE_TIDAK_AKTIF = 2;

    protected $fillable = [
        'judul_timline',
        'subjudul_timline',
        'status_timline',
        'deskripsi_timeline',
        'icon_timeline',
        'sequence_timeline'
    ];

    protected $casts = [
        'sequence_timeline' => 'integer',
    ];

    // Aksesor untuk mendapatkan status yang lebih user-friendly
    public function getStatusTimlineAttribute()
    {
        // Akses nilai asli dari kolom status
        return $this->attributes['status_timline'] == self::TIMELINE_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
