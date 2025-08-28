<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonasiHistory extends Model
{
    use HasFactory;

    protected $table = 'donasi_histories';

    protected $fillable = [
        'donasikilau_id',
        'external_user_id',
        'status_donasi',
        'total_donasi',
        'feedback',
        'token',
    ];

    public function donasikilau()
    {
        return $this->belongsTo(Donasikilau::class, 'donasikilau_id');
    }
}
