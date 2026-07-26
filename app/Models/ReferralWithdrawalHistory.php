<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReferralWithdrawalHistory extends Model
{
    use HasFactory;

    protected $table = 'referral_withdrawal_histories';

    protected $fillable = [
        'referral_withdrawal_id',
        'status',
        'note',
        'changed_at',
    ];

    public $timestamps = false;

    /**
     * Relasi: satu history milik satu withdrawal.
     */
    public function withdrawal()
    {
        return $this->belongsTo(ReferralWithdrawal::class, 'referral_withdrawal_id');
    }
}
