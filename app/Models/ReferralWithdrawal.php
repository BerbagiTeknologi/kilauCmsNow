<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralWithdrawal extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'referral_withdrawals';

    /* ---------- Konstanta Status ---------- */
    public const PENDING  = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';

    /* ---------- Kolom Boleh Di-isi ---------- */
    protected $fillable = [
        'program_referral_id',
        'nama_lengkap',
        'email',
        'no_hp',
        'no_rekening',
        'nama_bank', 
        'status',
        'requested_at',
        'processed_at',
    ];

    /* ---------- Casting Timestamp ---------- */
    // protected $dates = [
    //     'requested_at',
    //     'processed_at',
    //     'created_at',
    //     'updated_at',
    // ];

     protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /* ---------- Relasi ---------- */
    /** withdrawal ini milik satu baris program_referrals */
    public function programReferral()
    {
        return $this->belongsTo(ProgramReferral::class, 'program_referral_id', 'id');
    }

    /* ---------- Accessor Label ---------- */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            default        => 'Menunggu',
        };
    }

    public function histories()
    {
        return $this->hasMany(ReferralWithdrawalHistory::class, 'referral_withdrawal_id');
    }
}
