<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramReferral extends Model
{
    use HasFactory;

    protected $table = 'program_referrals';

    protected $fillable = [
        'program_id',
        'referer_name',
        'click_count',
    ];

    /**
     * Relasi ke program (setiap referral milik 1 program)
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'id');
    }

    public function withdrawals()
    {
        return $this->hasMany(ReferralWithdrawal::class, 'program_referral_id', 'id');
    }

    
}
