<?php

namespace App\Filament\Resources\AuditKas\Pages;

use App\Filament\Resources\AuditKas\AuditKasResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use App\Models\Jurnal;
use Exception;

class CreateAuditKas extends CreateRecord
{
    protected static string $resource = AuditKasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $audit = $this->record;

        if ($audit->selisih == 0) {
            return; 
        }

        DB::transaction(function () use ($audit) {
            $jurnal = Jurnal::create([
                'tanggal' => $audit->tanggal,
                'nomor_jurnal' => 'ADJ-KAS-' . date('Ymd', strtotime($audit->tanggal)) . '-' . str_pad($audit->id, 4, '0', STR_PAD_LEFT),
                'keterangan' => "Audit Kas: {$audit->keterangan} (Ref ID: {$audit->id})",
                'referensi_tipe' => get_class($audit),
                'referensi_id' => $audit->id,
            ]);

            if ($audit->selisih < 0) {
                $cfgDefisit = DB::table('akun_cfg')->where('kode_event', 'KAS_DEFISIT')->first();
                if (!$cfgDefisit || !$cfgDefisit->akun_debit_id) {
                    throw new Exception("Konfigurasi akun_cfg untuk event 'KAS_DEFISIT' tidak valid atau belum diset.");
                }

                $jurnal->akunTrans()->create([
                    'akun_id' => $cfgDefisit->akun_debit_id,
                    'debit' => abs($audit->selisih),
                    'kredit' => 0,
                ]);

                $jurnal->akunTrans()->create([
                    'akun_id' => $audit->akun_id,
                    'debit' => 0,
                    'kredit' => abs($audit->selisih),
                ]);
            } else {
                $cfgSurplus = DB::table('akun_cfg')->where('kode_event', 'KAS_SURPLUS')->first();
                if (!$cfgSurplus || !$cfgSurplus->akun_kredit_id) {
                    throw new Exception("Konfigurasi akun_cfg untuk event 'KAS_SURPLUS' tidak valid atau belum diset.");
                }

                $jurnal->akunTrans()->create([
                    'akun_id' => $audit->akun_id,
                    'debit' => abs($audit->selisih),
                    'kredit' => 0,
                ]);

                $jurnal->akunTrans()->create([
                    'akun_id' => $cfgSurplus->akun_kredit_id,
                    'debit' => 0,
                    'kredit' => abs($audit->selisih),
                ]);
            }
        });
    }
}