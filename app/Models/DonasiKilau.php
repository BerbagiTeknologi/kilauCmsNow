<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonasiKilau extends Model
{
    use HasFactory;

    protected $table = 'donasikilau';

    const DONASI_PENDING = 1;
    const DONASI_AKTIVE = 2;

    const TYPE_DONASI_PROGRAM = 1;
    const TYPE_DONASI_UMUM = 2;

    const OPSIONAL_UMUM_ZAKAT = 1;
    const OPSIONAL_UMUM_INFAQ = 2;

    protected $fillable = [
        'type_donasi',
        'opsional_umum',
        'id_program',
        'nama',
        'total_donasi',
        'status_donasi',
        'feedback',
        'no_hp',
        'email'
    ];

    protected $appends = ['formatted_date', 'jenis_donasi'];

    // Mendefinisikan atribut formatted_date
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y');
    }

    // Mendefinisikan atribut jenis_donasi
    public function getJenisDonasiAttribute()
    {
        if ($this->type_donasi == self::TYPE_DONASI_UMUM) {
            return $this->opsional_umum == self::OPSIONAL_UMUM_ZAKAT ? 'Zakat' : 'Infaq';
        } else {
            return 'Program: ' . ($this->program ? $this->program->judul : 'Tidak Ada Program');
        }
    }

    public function getStatusDonasiKilauAttribute()
    {
        return $this->attributes['status_donasi'] == self::DONASI_AKTIVE ? 'Aktif' : 'Tidak Aktif';
    }

    // Relasi ke Program
    public function program()
    {
        return $this->belongsTo(Program::class, 'id_program', 'id');
    }

    // Menentukan kolom-kolom yang harus di-cast jika diperlukan
    protected $casts = [
        'total_donasi' => 'decimal:2', // Menyimpan nilai total_donasi dengan 2 desimal
    ];
}
