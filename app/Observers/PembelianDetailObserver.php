<?php

namespace App\Observers;

use App\Models\PembelianDetail;
use App\Models\StokSaldo;
use App\Models\ProdukKonversi;
use App\Models\JurnalBarang;

class PembelianDetailObserver
{
    /**
     * Handle the PembelianDetail "created" event.
     */
    public function created(PembelianDetail $pembelianDetail): void
    {
        $produkId = $pembelianDetail->produk_id;
        $satuanId = $pembelianDetail->satuan_id;
        $qty = (float) $pembelianDetail->qty;
        $hargaBeliSatuan = (float) $pembelianDetail->harga_beli_satuan;

        // 1. Cari nilai faktor_pengali dari model ProdukKonversi
        $faktorPengali = 1.0;
        if ($satuanId) {
            $konversi = ProdukKonversi::where('produk_id', $produkId)
                ->where('satuan_id', $satuanId)
                ->first();
            if ($konversi) {
                $faktorPengali = (float) $konversi->faktor_pengali;
            }
        }

        // 2. Hitung qty_dasar
        $qtyDasar = $qty * $faktorPengali;

        // 3. Ambil gudang_id dari relasi Pembelian induknya
        $pembelian = $pembelianDetail->pembelian;
        $gudangId = $pembelian ? $pembelian->gudang_id : null;

        if ($gudangId && $produkId) {
            // 4. Update StokSaldo dan Hitung Moving Average
            $stok = StokSaldo::firstOrCreate(
                ['gudang_id' => $gudangId, 'produk_id' => $produkId],
                ['qty_sekarang' => 0, 'harga_pokok_rata_rata' => 0]
            );

            $stokLama = (float) $stok->qty_sekarang;
            $hargaRataLama = (float) $stok->harga_pokok_rata_rata;

            $totalNilaiLama = $stokLama * $hargaRataLama;
            $totalNilaiBaru = $qty * $hargaBeliSatuan; // Nilai total rupiah transaksi pembelian barang ini
            $totalStokBaru = $stokLama + $qtyDasar;

            $hargaRataBaru = $totalStokBaru > 0 ? ($totalNilaiLama + $totalNilaiBaru) / $totalStokBaru : 0;

            $stok->update([
                'qty_sekarang' => $totalStokBaru,
                'harga_pokok_rata_rata' => $hargaRataBaru,
            ]);

            // 5. Catat ke Kartu Stok (JurnalBarang)
            JurnalBarang::create([
                'produk_id' => $produkId,
                'gudang_id' => $gudangId,
                'tipe_mutasi' => 'PURCHASE',
                'referensi_tipe' => get_class($pembelian),
                'referensi_id' => $pembelian->id,
                'qty_in' => $qtyDasar,
                'qty_out' => 0,
                'harga_satuan' => $faktorPengali > 0 ? ($hargaBeliSatuan / $faktorPengali) : $hargaBeliSatuan,
            ]);
        }
    }
}
