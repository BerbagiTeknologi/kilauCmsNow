<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';

    const PROGRAM_AKTIF = 1;
    const PROGRAM_TIDAK_AKTIF = 2;

    protected $fillable = [
        'judul',
        'deskripsi',
        'program_yang_berhasil_dijalankan',
        'foto_image',
        'status_program',
        'thumbnail_image',
        'jumlah_target_tercapai'
    ];

    // Menentukan kolom-kolom yang harus di-cast
    protected $casts = [
        'foto_image' => 'array', // Mengonversi kolom foto_image menjadi array
    ];

    public function getStatusProgramAttribute()
    {
        // Akses nilai asli dari kolom status
        return $this->attributes['status_program'] == self::PROGRAM_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }

    public function getFotoImageAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $images = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($images)) {
            return $images;
        }

        return [$value];
    }

    public function mitras()
    {
        return $this->belongsToMany(MitraDonatur::class, 'program_mitra', 'program_id', 'mitra_id');
    }

    public function donasikilau()
    {
        return $this->hasMany(DonasiKilau::class, 'id_program', 'id');
    }

    public function feedbacks()
    {
        return $this->hasMany(DonasiKilau::class, 'id_program', 'id')
                    ->whereNotNull('feedback')
                    ->where('feedback','!=','')
                    ->where('status_donasi', DonasiKilau::DONASI_AKTIVE)
                    ->select('id_program','nama','feedback','created_at','status_donasi') // ← tambahkan
                    ->latest();
    }

    public function referrals()
    {
        return $this->hasMany(ProgramReferral::class, 'program_id', 'id');
    }

    public function km12Mapping()
    {
        return $this->hasOne(CmsProgramKm12Mapping::class, 'cms_program_id');
    }

}
