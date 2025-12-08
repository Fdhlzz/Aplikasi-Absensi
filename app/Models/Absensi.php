<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'pengguna_id',
        'waktu_absen',
        'tipe',       // 'masuk', 'pulang'
        'status',     // 'Tepat Waktu', 'Telat'
        'sumber',     // 'perangkat', 'manual'
        'perangkat_id',
        'catatan',
        'diubah_oleh_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'waktu_absen' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function perangkat(): BelongsTo
    {
        return $this->belongsTo(Perangkat::class);
    }
}