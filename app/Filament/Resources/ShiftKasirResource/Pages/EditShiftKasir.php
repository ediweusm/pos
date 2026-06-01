<?php

namespace App\Filament\Resources\ShiftKasirResource\Pages;

use App\Filament\Resources\ShiftKasirResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use App\Models\Jurnal;
use App\Models\AkunTrans;

class EditShiftKasir extends EditRecord
{
    protected static string $resource = ShiftKasirResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function mount($record): void
    {
        parent::mount($record);

        if ($this->getRecord()->status === 'CLOSED') {
            Notification::make()
                ->title('Shift Sudah Ditutup')
                ->body('Shift Kasir ini sudah berstatus CLOSED dan tidak dapat diubah lagi.')
                ->danger()
                ->send();

            $this->redirect(ShiftKasirResource::getUrl('index'));
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();
        $data['waktu_tutup'] = now();
        $data['status'] = 'CLOSED';

        $modalAwal = (float) $record->modal_awal;
        $totalPenjualan = (float) $record->total_penjualan;
        $saldoAktual = (float) $data['saldo_aktual'];

        $data['selisih'] = $saldoAktual - ($modalAwal + $totalPenjualan);

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        DB::transaction(function () use ($record) {
            $cabangId = $record->cabang_id ?? (auth()->user()->cabang_id ?? null);
            $selisih = (float) $record->selisih;

            // 1. Catat Selisih Kasir jika ada
            if ($selisih < 0) {
                // DEFISIT: Debit Akun Beban/Kerugian Kas (ID: 16), Kredit Akun Kasir POS (ID: 1)
                $nominalDefisit = abs($selisih);

                $jurnalDefisit = Jurnal::create([
                    'tanggal' => now()->toDateString(),
                    'nomor_jurnal' => 'JRN-SHF-DEF-' . $record->id . '-' . time(),
                    'keterangan' => "Defisit Selisih Shift Kasir (Shift #" . $record->id . "): " . $record->user->name,
                    'referensi_tipe' => get_class($record),
                    'referensi_id' => $record->id,
                    'cabang_id' => $cabangId,
                ]);

                // Debit: Beban Selisih Kas (16)
                AkunTrans::create([
                    'jurnal_id' => $jurnalDefisit->id,
                    'akun_id' => 16,
                    'debit' => $nominalDefisit,
                    'kredit' => 0,
                ]);

                // Kredit: Kasir POS (1)
                AkunTrans::create([
                    'jurnal_id' => $jurnalDefisit->id,
                    'akun_id' => 1,
                    'debit' => 0,
                    'kredit' => $nominalDefisit,
                ]);

            } elseif ($selisih > 0) {
                // SURPLUS: Debit Akun Kasir POS (ID: 1), Kredit Akun Pendapatan Selisih Kas (ID: 11)
                $nominalSurplus = $selisih;

                $jurnalSurplus = Jurnal::create([
                    'tanggal' => now()->toDateString(),
                    'nomor_jurnal' => 'JRN-SHF-SUR-' . $record->id . '-' . time(),
                    'keterangan' => "Surplus Selisih Shift Kasir (Shift #" . $record->id . "): " . $record->user->name,
                    'referensi_tipe' => get_class($record),
                    'referensi_id' => $record->id,
                    'cabang_id' => $cabangId,
                ]);

                // Debit: Kasir POS (1)
                AkunTrans::create([
                    'jurnal_id' => $jurnalSurplus->id,
                    'akun_id' => 1,
                    'debit' => $nominalSurplus,
                    'kredit' => 0,
                ]);

                // Kredit: Pendapatan Selisih Kas (11)
                AkunTrans::create([
                    'jurnal_id' => $jurnalSurplus->id,
                    'akun_id' => 11,
                    'debit' => 0,
                    'kredit' => $nominalSurplus,
                ]);
            }

            // 2. OTOMATIS SETOR: Pindahkan saldo_aktual dari Kasir POS (1) ke Kas Besar (2)
            $saldoAktual = (float) $record->saldo_aktual;
            if ($saldoAktual > 0) {
                // Debit: Kas Besar / Brankas (2), Kredit: Kasir POS (1)
                $jurnalSetor = Jurnal::create([
                    'tanggal' => now()->toDateString(),
                    'nomor_jurnal' => 'JRN-SHF-STR-' . $record->id . '-' . time(),
                    'keterangan' => "Setoran Shift Kasir Otomatis ke Brankas (Shift #" . $record->id . "): " . $record->user->name,
                    'referensi_tipe' => get_class($record),
                    'referensi_id' => $record->id,
                    'cabang_id' => $cabangId,
                ]);

                // Debit: Kas Besar / Brankas (2)
                AkunTrans::create([
                    'jurnal_id' => $jurnalSetor->id,
                    'akun_id' => 2,
                    'debit' => $saldoAktual,
                    'kredit' => 0,
                ]);

                // Kredit: Kasir POS (1)
                AkunTrans::create([
                    'jurnal_id' => $jurnalSetor->id,
                    'akun_id' => 1,
                    'debit' => 0,
                    'kredit' => $saldoAktual,
                ]);
            }
        });
    }
}
