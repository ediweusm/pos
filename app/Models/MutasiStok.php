<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MutasiStok extends Model
{
    protected $table = 'mutasi_stok';
    protected $guarded = ['id'];

    public function gudangAsal(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'gudang_asal_id');
    }

    public function gudangTujuan(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'gudang_tujuan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mutasiStokDetails(): HasMany
    {
        return $this->hasMany(MutasiStokDetail::class, 'mutasi_stok_id');
    }
}
