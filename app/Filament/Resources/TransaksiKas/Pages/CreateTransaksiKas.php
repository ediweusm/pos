<?php

namespace App\Filament\Resources\TransaksiKas\Pages;

use App\Filament\Resources\TransaksiKas\TransaksiKasResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use App\Models\Jurnal;
use App\Models\AkunTrans;

class CreateTransaksiKas extends CreateRecord
{
    protected static string $resource = TransaksiKasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        DB::transaction(function () use ($record) {
            // 1. Buat record di tabel jurnal (Header)
            $jurnal = Jurnal::create([
                'tanggal' => $record->tanggal,
                'nomor_jurnal' => $record->nomor_bukti,
                'keterangan' => 'Mutasi Kas: ' . $record->keterangan,
                'referensi_tipe' => get_class($record),
                'referensi_id' => $record->id,
            ]);

            // 2. Buat akun_trans DEBIT (Uang Masuk ke Tujuan)
            AkunTrans::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $record->akun_penerima_id,
                'debit' => $record->nominal,
                'kredit' => 0,
            ]);

            // 3. Buat akun_trans KREDIT (Uang Keluar dari Sumber)
            AkunTrans::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $record->akun_pengirim_id,
                'debit' => 0,
                'kredit' => $record->nominal,
            ]);
        });
    }
}
