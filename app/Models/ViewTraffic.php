<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ViewTraffic extends Model
{
    // Nama tabel jika tidak mengikuti konvensi jamak
    protected $table = 'view_traffics';

    // Mass assignable
    protected $fillable = [
        'session_id',
        'ip_address',
        'user_agent',
        'type',
        'viewed_at',
    ];

    // Casting tipe data
    protected $casts = [
        'viewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Konstanta ENUM
    public const TYPE_LANDINGPAGE         = 'landingpage';
    public const TYPE_FORM_DONASI         = 'form_donasi';
    public const TYPE_FORM_DONASI_PROGRAM = 'form_donasi_program';

    public static function validTypes(): array
    {
        return [
            self::TYPE_LANDINGPAGE,
            self::TYPE_FORM_DONASI,
            self::TYPE_FORM_DONASI_PROGRAM,
        ];
    }
}
