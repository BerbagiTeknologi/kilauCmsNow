<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsGeneralDonationKm12Mapping extends Model
{
    use HasFactory;

    public const TYPE_ZAKAT = 'zakat';

    public const TYPE_INFAQ = 'infaq';

    protected $fillable = [
        'donation_type',
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

    public static function typeLabels(): array
    {
        return [
            self::TYPE_ZAKAT => 'Zakat',
            self::TYPE_INFAQ => 'Infaq',
        ];
    }
}
