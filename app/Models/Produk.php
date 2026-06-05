<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $table = 'produk';
    protected $guarded = [];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function satuanDasar(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'satuan_dasar_id');
    }

    /**
     * Relasi ke daftar harga tiered (one-to-many).
     */
    public function harga(): HasMany
    {
        return $this->hasMany(HargaProduk::class, 'produk_id');
    }

    /**
     * Pricing Engine: ambil harga yang berlaku sesuai satuan & qty yang dibeli.
     *
     * Algoritma: ambil tier dengan minimal_qty terbesar yang masih <= qty beli.
     * Contoh: tiers = [1 => 10000, 5 => 9000, 10 => 8000]
     *   - Beli 3 pcs → pakai tier min_qty=1 (Rp 10.000)
     *   - Beli 7 pcs → pakai tier min_qty=5 (Rp 9.000)
     *   - Beli 12 pcs → pakai tier min_qty=10 (Rp 8.000)
     *
     * @param  int   $satuanId  ID satuan jual yang dipilih di kasir
     * @param  float $qtyBeli   Jumlah yang akan dibeli
     * @return HargaProduk|null
     */
    public function getHargaBerlaku(int $satuanId, float $qtyBeli): ?HargaProduk
    {
        return $this->harga()
            ->where('satuan_id', $satuanId)
            ->where('minimal_qty', '<=', $qtyBeli)
            ->orderBy('minimal_qty', 'desc') // Ambil threshold terbesar yang tertembus
            ->first();
    }

    /**
     * Shortcut: ambil harga eceran terendah (harga minimal qty=1) untuk satuan dasar.
     * Dipakai saat scan barcode pertama kali di kasir.
     *
     * @return HargaProduk|null
     */
    public function getHargaEceranDefault(): ?HargaProduk
    {
        return $this->harga()
            ->where('satuan_id', $this->satuan_dasar_id)
            ->where('minimal_qty', '<=', 1)
            ->orderBy('minimal_qty', 'desc')
            ->first();
    }

    public function stokSaldos(): HasMany
    {
        return $this->hasMany(StokSaldo::class, 'produk_id');
    }

    public function konversi(): HasMany
    {
        return $this->hasMany(ProdukKonversi::class, 'produk_id');
    }
}
