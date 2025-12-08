<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SidikJari extends Model
{
    use HasFactory;

    protected $table = 'sidik_jari';

    protected $fillable = [
        'pengguna_id',
        'fingerprint_id',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}