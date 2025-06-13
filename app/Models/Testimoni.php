<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'testimoni';

    const TESTIMONI_AKTIF = 1;
    const TESTIMONI_TIDAK_AKTIF = 2;

    protected $fillable = [
        'komentar',
        'nama',
        'pekerjaan',
        'file',
        'statuss_testimoni',
        'video_link'
    ];

    public function getStatusAttribute()
    {
        return $this->statuss_testimoni == self::TESTIMONI_AKTIF ? 'Aktif' : 'Tidak Aktif';
    }
}
