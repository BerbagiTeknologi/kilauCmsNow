<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramMitra extends Model
{
    use HasFactory;

    // Tabel yang digunakan oleh model ini
    protected $table = 'program_mitra';

    // Kolom yang bisa diisi secara mass-assignment
    protected $fillable = [
        'program_id',
        'mitra_id',
    ];

    // Relasi Many-to-Many ke Program
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    // Relasi Many-to-Many ke MitraDonatur
    public function mitras()
    {
        return $this->belongsTo(MitraDonatur::class, 'mitra_id');
    }
}
