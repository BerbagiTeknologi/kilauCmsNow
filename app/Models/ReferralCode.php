<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    use HasFactory;

    public const TYPE_CMS_USER = 'cms_user';
    public const TYPE_KILAU_EMPLOYEE = 'kilau_employee';

    protected $fillable = [
        'cms_user_id',
        'global_user_id',
        'code',
        'referral_type',
        'km12_user_id',
        'karyawan_id',
        'name_snapshot',
        'email_snapshot',
        'position_snapshot',
        'photo_url_snapshot',
        'is_active',
        'employee_verified_at',
        'synced_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'employee_verified_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'cms_user_id');
    }

    public function isKilauEmployee(): bool
    {
        return $this->referral_type === self::TYPE_KILAU_EMPLOYEE;
    }
}
