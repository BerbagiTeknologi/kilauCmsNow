<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsGuestDonor extends Model
{
    use HasFactory;

    protected $table = 'cms_guest_donors';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'no_hp',
        'profile_version',
        'is_active',
        'anonymized_at',
    ];

    protected $casts = [
        'profile_version' => 'integer',
        'is_active' => 'boolean',
        'anonymized_at' => 'datetime',
    ];
}
