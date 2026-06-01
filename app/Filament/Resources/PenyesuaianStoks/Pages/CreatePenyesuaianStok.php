<?php

namespace App\Filament\Resources\PenyesuaianStoks\Pages;

use App\Filament\Resources\PenyesuaianStoks\PenyesuaianStokResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\StokSaldo;
use Illuminate\Support\Facades\DB;
use App\Models\Jurnal;
use Exception;

class CreatePenyesuaianStok extends CreateRecord
{
    protected static string $resource = PenyesuaianStokResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $opname = $this->record;

        if ($opname->selisih == 0) {
            return; // Tidak ada selisih, tidak perlu jurnal
        }

        DB::transaction(function () use ($opname) {
            // 1. Dapatkan atau buat data StokSaldo
            $stokSaldo = StokSaldo::firstOrCreate(
                [
                    'gudang_id' => $opname->gudang_id,
                    'produk_id' => $opname->produk_id,
                ],
                [
                    'qty_sekarang' => 0,
                    'harga_pokok_rata_rata' => 0,
                ]
            );

            $qtyIn = 0;
            $qtyOut = 0;

            if ($opname->selisih > 0) {
                $qtyIn = $opname->selisih;
            } else {
                $qtyOut = abs($opname->selisih); 
            }

            // 2. Catat ke Kartu Stok (Polymorphic)
            $opname->jurnalBarangs()->create([
                'produk_id' => $opname->produk_id,
                'gudang_id' => $opname->gudang_id,
                'tipe_mutasi' => 'ADJUSTMENT',
                'qty_in' => $qtyIn,
                'qty_out' => $qtyOut,
                'harga_satuan' => $stokSaldo->harga_pokok_rata_rata,
            ]);

            // 3. Perbarui fisik di Gudang
            $stokSaldo->update([
                'qty_sekarang' => $opname->qty_fisik
            ]);
            
            // 4. Proses Jurnal Keuangan Dinamis via akun_cfg
            $totalNilai = abs($opname->selisih) * $stokSaldo->harga_pokok_rata_rata;

            if ($totalNilai <= 0) return;

            $jurnalKeuangan = Jurnal::create([
                'tanggal' => $opname->tanggal,
                'nomor_jurnal' => 'OPN-' . date('Ymd') . '-' . str_pad($opname->id, 4, '0', STR_PAD_LEFT),
                'keterangan' => "Stock Opname: {$opname->keterangan} (Ref ID: {$opname->id})",
            ]);

            if ($opname->selisih < 0) {
                // Event Barang Hilang
                $cfgDefisit = DB::table('akun_cfg')->where('kode_event', 'STOK_DEFISIT')->first();
                if (!$cfgDefisit) throw new Exception("Konfigurasi akun_cfg untuk event 'STOK_DEFISIT' tidak ditemukan.");

                $jurnalKeuangan->akunTrans()->create([
                    'akun_id' => $cfgDefisit->akun_debit_id,
                    'debit' => $totalNilai,
                    'kredit' => 0,
                ]);
                $jurnalKeuangan->akunTrans()->create([
                    'akun_id' => $cfgDefisit->akun_kredit_id,
                    'debit' => 0,
                    'kredit' => $totalNilai,
                ]);
            } else {
                // Event Barang Lebih
                $cfgSurplus = DB::table('akun_cfg')->where('kode_event', 'STOK_SURPLUS')->first();
                if (!$cfgSurplus) throw new Exception("Konfigurasi akun_cfg untuk event 'STOK_SURPLUS' tidak ditemukan.");

                $jurnalKeuangan->akunTrans()->create([
                    'akun_id' => $cfgSurplus->akun_debit_id,
                    'debit' => $totalNilai,
                    'kredit' => 0,
                ]);
                $jurnalKeuangan->akunTrans()->create([
                    'akun_id' => $cfgSurplus->akun_kredit_id,
                    'debit' => 0,
                    'kredit' => $totalNilai,
                ]);
            }
        });
    }
}
