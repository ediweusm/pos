<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiKas extends Model
{
    protected $table = 'transaksi_kas';

    protected $guarded = ['id'];

    /**
     * Relasi ke Akun Pengirim (Sumber Dana / Kredit)
     */
    public function akunPengirim(): BelongsTo
    {
        return $this->belongsTo(AkunMaster::class, 'akun_pengirim_id');
    }

    /**
     * Relasi ke Akun Penerima (Tujuan Dana / Debit)
     */
    public function akunPenerima(): BelongsTo
    {
        return $this->belongsTo(AkunMaster::class, 'akun_penerima_id');
    }
}
