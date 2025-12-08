<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perangkat extends Model
{
    use HasFactory;

    protected $table = 'perangkat';

    protected $fillable = [
        'nama',
        'unique_id',
        'api_key',
        'last_heartbeat',
        'pending_enrollment_id',
    ];

    protected function casts(): array
    {
        return [
            'last_heartbeat' => 'datetime',
        ];
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }
}