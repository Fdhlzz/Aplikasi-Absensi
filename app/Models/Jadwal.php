<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';

    protected $fillable = [
        'nama',
        'jam_masuk',
        'jam_pulang',
        'toleransi_telat_menit',
    ];

    public function pengguna(): HasMany
    {
        return $this->hasMany(Pengguna::class);
    }
}