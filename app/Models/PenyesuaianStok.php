<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PenyesuaianStok extends Model
{
    protected $table = 'penyesuaian_stok';
    protected $guarded = ['id'];

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    // Relasi Polymorphic ke Kartu Stok (Jurnal Barang)
    public function jurnalBarangs(): MorphMany
    {
        return $this->morphMany(JurnalBarang::class, 'referensi', 'referensi_tipe', 'referensi_id');
    }
}
