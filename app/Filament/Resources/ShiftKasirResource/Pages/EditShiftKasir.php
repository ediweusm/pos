<?php

namespace App\Filament\Resources\ShiftKasirResource\Pages;

use App\Filament\Resources\ShiftKasirResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use App\Models\Jurnal;
use App\Models\AkunTrans;
use App\Models\AkunCfg;
use App\Models\PosTransaksi;
use Illuminate\Support\Str;

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
        $totalPenjualan = (float) PosTransaksi::query()
            ->where('pos_shift_id', $record->id)
            ->where('status', 'SELESAI')
            ->where('status_pembayaran', 'LUNAS')
            ->where('metode_bayar', 'TUNAI')
            ->sum('grand_total');
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

            $cfgDefisit = AkunCfg::where('kode_event', 'KAS_DEFISIT')->first();
            $cfgSurplus = AkunCfg::where('kode_event', 'KAS_SURPLUS')->first();
            // The setoran is the reverse of the opening-float journal.
            $cfgSetor = AkunCfg::where('kode_event', 'SHIFT_KASIR')->first();

            // 1. Catat Selisih Kasir jika ada
            if ($selisih < 0) {
                if (! $cfgDefisit) {
                    throw new \RuntimeException('Konfigurasi akun KAS_DEFISIT tidak ditemukan.');
                }

                $nominalDefisit = abs($selisih);

                $jurnalDefisit = Jurnal::create([
                    'tanggal' => now()->toDateString(),
                    'nomor_jurnal' => 'JRN-SHF-DEF-' . Str::upper((string) Str::ulid()),
                    'keterangan' => "Defisit Selisih Shift Kasir (Shift #" . $record->id . "): " . $record->user->name,
                    'referensi_tipe' => get_class($record),
                    'referensi_id' => $record->id,
                    'cabang_id' => $cabangId,
                ]);

                AkunTrans::create([
                    'jurnal_id' => $jurnalDefisit->id,
                    'akun_id' => $cfgDefisit->akun_debit_id,
                    'debit' => $nominalDefisit,
                    'kredit' => 0,
                ]);

                AkunTrans::create([
                    'jurnal_id' => $jurnalDefisit->id,
                    'akun_id' => $cfgDefisit->akun_kredit_id,
                    'debit' => 0,
                    'kredit' => $nominalDefisit,
                ]);

            } elseif ($selisih > 0) {
                if (! $cfgSurplus) {
                    throw new \RuntimeException('Konfigurasi akun KAS_SURPLUS tidak ditemukan.');
                }

                $nominalSurplus = $selisih;

                $jurnalSurplus = Jurnal::create([
                    'tanggal' => now()->toDateString(),
                    'nomor_jurnal' => 'JRN-SHF-SUR-' . Str::upper((string) Str::ulid()),
                    'keterangan' => "Surplus Selisih Shift Kasir (Shift #" . $record->id . "): " . $record->user->name,
                    'referensi_tipe' => get_class($record),
                    'referensi_id' => $record->id,
                    'cabang_id' => $cabangId,
                ]);

                AkunTrans::create([
                    'jurnal_id' => $jurnalSurplus->id,
                    'akun_id' => $cfgSurplus->akun_debit_id,
                    'debit' => $nominalSurplus,
                    'kredit' => 0,
                ]);

                AkunTrans::create([
                    'jurnal_id' => $jurnalSurplus->id,
                    'akun_id' => $cfgSurplus->akun_kredit_id,
                    'debit' => 0,
                    'kredit' => $nominalSurplus,
                ]);
            }

            // 2. OTOMATIS SETOR: pindahkan saldo fisik laci berdasarkan COA terkonfigurasi.
            $saldoAktual = (float) $record->saldo_aktual;
            if ($saldoAktual > 0) {
                if (! $cfgSetor) {
                    throw new \RuntimeException('Konfigurasi akun SHIFT_KASIR tidak ditemukan.');
                }

                $jurnalSetor = Jurnal::create([
                    'tanggal' => now()->toDateString(),
                    'nomor_jurnal' => 'JRN-SHF-STR-' . Str::upper((string) Str::ulid()),
                    'keterangan' => "Setoran Shift Kasir Otomatis ke Brankas (Shift #" . $record->id . "): " . $record->user->name,
                    'referensi_tipe' => get_class($record),
                    'referensi_id' => $record->id,
                    'cabang_id' => $cabangId,
                ]);

                AkunTrans::create([
                    'jurnal_id' => $jurnalSetor->id,
                    'akun_id' => $cfgSetor->akun_kredit_id,
                    'debit' => $saldoAktual,
                    'kredit' => 0,
                ]);

                AkunTrans::create([
                    'jurnal_id' => $jurnalSetor->id,
                    'akun_id' => $cfgSetor->akun_debit_id,
                    'debit' => 0,
                    'kredit' => $saldoAktual,
                ]);
            }
        });
    }
}
