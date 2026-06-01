<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AuditKas extends Model
{
    protected $table = 'audit_kas';
    protected $guarded = ['id'];

    public function akun(): BelongsTo
    {
        return $this->belongsTo(AkunMaster::class, 'akun_id');
    }

    // Relasi Polymorphic ke Jurnal Keuangan
    public function jurnal(): MorphMany
    {
        return $this->morphMany(Jurnal::class, 'referensi', 'referensi_tipe', 'referensi_id');
    }
}
