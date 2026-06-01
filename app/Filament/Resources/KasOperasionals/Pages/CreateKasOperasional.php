<?php

namespace App\Filament\Resources\KasOperasionals\Pages;

use App\Filament\Resources\KasOperasionals\KasOperasionalResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use App\Models\Jurnal;
use App\Models\AkunTrans;

class CreateKasOperasional extends CreateRecord
{
    protected static string $resource = KasOperasionalResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        
        DB::transaction(function () use ($record) {
            // 1. Buat record di tabel jurnal (Header)
            $jurnal = Jurnal::create([
                'tanggal' => $record->tanggal,
                'nomor_jurnal' => $record->nomor_bukti,
                'keterangan' => "Kas Operasional (" . $record->jenis . "): " . $record->keterangan,
                'referensi_tipe' => get_class($record),
                'referensi_id' => $record->id,
                // Mengaitkan cabang_id milik user yang sedang aktif ke jurnal untuk profit center
                'cabang_id' => auth()->user()->cabang_id ?? null,
            ]);
            
            // 2. Buat record di tabel akun_trans (Detail Double Entry)
            if ($record->jenis === 'PENGELUARAN') {
                // AkunTrans 1 (Debit): akun_id = akun_lawan_id, debit = nominal, kredit = 0
                AkunTrans::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $record->akun_lawan_id,
                    'debit' => $record->nominal,
                    'kredit' => 0,
                ]);
                
                // AkunTrans 2 (Kredit): akun_id = akun_kas_id, debit = 0, kredit = nominal
                AkunTrans::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $record->akun_kas_id,
                    'debit' => 0,
                    'kredit' => $record->nominal,
                ]);
            } elseif ($record->jenis === 'PEMASUKAN') {
                // AkunTrans 1 (Debit): akun_id = akun_kas_id, debit = nominal, kredit = 0
                AkunTrans::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $record->akun_kas_id,
                    'debit' => $record->nominal,
                    'kredit' => 0,
                ]);
                
                // AkunTrans 2 (Kredit): akun_id = akun_lawan_id, debit = 0, kredit = nominal
                AkunTrans::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $record->akun_lawan_id,
                    'debit' => 0,
                    'kredit' => $record->nominal,
                ]);
            }
        });
    }
}
