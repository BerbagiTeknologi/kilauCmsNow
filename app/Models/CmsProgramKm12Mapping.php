<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsProgramKm12Mapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'cms_program_id',
        'km12_program_penerimaan_id',
        'km12_sumber_dana_id',
        'km12_program_name',
        'km12_sumber_dana_name',
        'is_active',
        'synced_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'cms_program_id');
    }
}
