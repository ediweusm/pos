<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\MorphTo;

class JurnalBarang extends Model
{
    protected $table = 'jurnal_barang';
    protected $guarded = ['id'];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }

    // Relasi Polymorphic untuk mengetahui sumber dokumen (Pembelian / Penjualan)
    public function referensi(): MorphTo
    {
        return $this->morphTo('referensi', 'referensi_tipe', 'referensi_id');
    }
}
