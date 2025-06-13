<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingsMenu extends Model
{
    use HasFactory;

    protected $table = 'settings_menu';

    const SETTINGS_MENU_AKTIF = 1;
    const SETTINGS_MENU_TIDAK_AKTIF = 2;

    protected $fillable = [
        'judul',
        'subjudul',
        'status'
    ];

    // Accessor untuk status
    public function getStatusAttribute()
    {
        // Akses nilai asli dari kolom status
        return $this->attributes['status'] == self::SETTINGS_MENU_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}