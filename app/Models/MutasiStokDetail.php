<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiStokDetail extends Model
{
    protected $table = 'mutasi_stok_detail';
    protected $guarded = ['id'];

    public function mutasiStok(): BelongsTo
    {
        return $this->belongsTo(MutasiStok::class, 'mutasi_stok_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
