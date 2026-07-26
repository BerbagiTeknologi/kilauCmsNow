<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colaborasi extends Model
{
    use HasFactory;

    protected $table = 'colaborasi';

    const COLABORASI_AKTIF = 1;
    const COLABORASI_TIDAK_AKTIF = 2;

    const COLABORASI_PROGRES_PENDING = "Pending";
    const COLABORASI_PROGRES_CLOSED = "Closed";

    protected $fillable = [
        'id_program',
        'jenis_kerjasama',
        'kategori_mitra',
        'nama_lengkap',
        'alamat_email',
        'nomor_hp',
        'balasan',
        'status_progress_kerjasama',
        'status_kerjasama',
        'deskripsi_pengajuan_kerjasama',
        'nomor_hp_organisasi', 
        'npwp_file', 
        'foto_orang_npwp',
        'nama_perusahaan',
        'jabatan',
    ];

    public function getStatusKerjasamaAttribute()
    {
        return $this->attributes['status_kerjasama'] == self::COLABORASI_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'id_program')->withDefault([
            'judul' => 'Tidak Diketahui'
        ]);
    }
}
