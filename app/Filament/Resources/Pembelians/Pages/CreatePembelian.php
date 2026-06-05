<?php

namespace App\Filament\Resources\Pembelians\Pages;

use App\Filament\Resources\Pembelians\PembelianResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use App\Models\StokSaldo;
use App\Models\JurnalBarang;
use App\Models\Jurnal;
use App\Models\AkunTrans;
use App\Models\AkunMaster;
use Exception;


class CreatePembelian extends CreateRecord
{
    protected static string $resource = PembelianResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $pembelian = $this->record;

        DB::transaction(function () use ($pembelian) {
            // 1. Logistik update is now handled automatically by App\Observers\PembelianDetailObserver.

            // 2. AUTO-JOURNALING KEUANGAN (DOUBLE-ENTRY) DINAMIS VIA akun_cfg
            $jurnal = Jurnal::create([
                'nomor_jurnal' => 'JRN-BUY-' . time(),
                'tanggal' => $pembelian->tanggal,
                'keterangan' => 'Pembelian Faktur: ' . $pembelian->nomor_faktur,
                'referensi_tipe' => get_class($pembelian),
                'referensi_id' => $pembelian->id,
            ]);

            // Tentukan Event Pembelian berdasarkan status pembayaran
            $kodeEvent = $pembelian->status_pembayaran === 'LUNAS' ? 'PURCHASE_TUNAI' : 'PURCHASE_TEMPO';
            $cfgPembelian = DB::table('akun_cfg')->where('kode_event', $kodeEvent)->first();

            if (!$cfgPembelian) {
                throw new Exception("Konfigurasi jurnal untuk event '{$kodeEvent}' belum diset di tabel akun_cfg.");
            }

            // Debit: Persediaan Barang Dagang (Bertambah sebesar harga murni)
            AkunTrans::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $cfgPembelian->akun_debit_id,
                'debit' => $pembelian->subtotal_barang,
                'kredit' => 0,
            ]);

            // Debit: PPN Masukan (Jika ada)
            if ($pembelian->ppn_nominal > 0) {
                // Mencari akun PPN secara dinamis dari tabel master
                $akunPpn = AkunMaster::where('nama_akun', 'like', '%PPN%')
                            ->orWhere('nama_akun', 'like', '%Pajak%')
                            ->first();

                if ($akunPpn) {
                    AkunTrans::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id' => $akunPpn->id,
                        'debit' => $pembelian->ppn_nominal,
                        'kredit' => 0,
                    ]);
                } else {
                    // Jika belum membuat akun PPN, lempar error agar tidak ada uang yang menguap tak tercatat
                    throw new Exception("Anda memiliki PPN, tetapi Akun 'PPN Masukan' belum dibuat di Daftar Kode Akun.");
                }
            }

            // Kredit: Kas Besar / Hutang Usaha (Diambil otomatis dari akun_cfg)
            AkunTrans::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $cfgPembelian->akun_kredit_id,
                'debit' => 0,
                'kredit' => $pembelian->grand_total,
            ]);
        });
    }
}