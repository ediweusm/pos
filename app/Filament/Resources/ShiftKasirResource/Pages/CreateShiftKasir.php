<?php

namespace App\Filament\Resources\ShiftKasirResource\Pages;

use App\Filament\Resources\ShiftKasirResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use App\Models\Jurnal;
use App\Models\AkunTrans;
use App\Models\AkunCfg;

class CreateShiftKasir extends CreateRecord
{
    protected static string $resource = ShiftKasirResource::class;

    protected function beforeCreate(): void
    {
        $user = auth()->user();
        if ($user && $user->hasOpenShift()) {
            Notification::make()
                ->title('Gagal Membuka Shift')
                ->body('Anda masih memiliki shift aktif yang berstatus OPEN. Silakan tutup shift tersebut terlebih dahulu.')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['user_id'] = $user->id;
        $data['cabang_id'] = $user->cabang_id;
        $data['gudang_id'] = $user->gudang_id;
        $data['waktu_buka'] = now();
        $data['status'] = 'OPEN';

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $modalAwal = (float) $record->modal_awal;

        if ($modalAwal > 0) {
            DB::transaction(function () use ($record, $modalAwal) {
                // Ambil konfigurasi akun dari akun_cfg dengan kode_event "SHIFT_KASIR"
                $cfg = AkunCfg::where('kode_event', 'SHIFT_KASIR')->first();

                if (!$cfg) {
                    throw new \Exception("Konfigurasi akun untuk SHIFT_KASIR tidak ditemukan.");
                }

                $cabangId = $record->cabang_id ?? (auth()->user()->cabang_id ?? null);

                // Buat record Jurnal (Header)
                $jurnal = Jurnal::create([
                    'tanggal' => now()->toDateString(),
                    'nomor_jurnal' => 'JRN-SHF-OPN-' . $record->id . '-' . time(),
                    'keterangan' => "Modal Buka Shift Kasir (Shift #" . $record->id . "): " . $record->user->name,
                    'referensi_tipe' => get_class($record),
                    'referensi_id' => $record->id,
                    'cabang_id' => $cabangId,
                ]);

                // Debit: Kasir POS (dari cfg)
                AkunTrans::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $cfg->akun_debit_id,
                    'debit' => $modalAwal,
                    'kredit' => 0,
                ]);

                // Kredit: Kas Besar / Brankas (dari cfg)
                AkunTrans::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $cfg->akun_kredit_id,
                    'debit' => 0,
                    'kredit' => $modalAwal,
                ]);
            });
        }
    }
}

