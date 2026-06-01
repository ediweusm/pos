<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosTransaksiDetail extends Model
{
    protected $table = 'pos_transaksi_detail';
    protected $guarded = ['id'];

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(PosTransaksi::class, 'pos_transaksi_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
