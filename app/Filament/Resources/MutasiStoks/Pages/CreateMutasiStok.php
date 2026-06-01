<?php

namespace App\Filament\Resources\MutasiStoks\Pages;

use App\Filament\Resources\MutasiStoks\UserResource;
use App\Filament\Resources\MutasiStoks\MutasiStokResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use App\Models\StokSaldo;
use App\Models\JurnalBarang;
use App\Models\Produk;

class CreateMutasiStok extends CreateRecord
{
    protected static string $resource = MutasiStokResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function beforeCreate(): void
    {
        $gudangAsalId = $this->data['gudang_asal_id'];
        
        foreach ($this->data['mutasiStokDetails'] as $item) {
            $produkId = $item['produk_id'];
            $qtyMutasi = (float) $item['qty'];
            
            // Ambil stok saat ini di gudang asal
            $stokSaldo = StokSaldo::where('gudang_id', $gudangAsalId)
                ->where('produk_id', $produkId)
                ->first();
                
            $stokSekarang = $stokSaldo ? (float) $stokSaldo->qty_sekarang : 0.0;
            
            if ($stokSekarang < $qtyMutasi) {
                $produkName = Produk::find($produkId)?->nama ?? 'Produk';
                
                Notification::make()
                    ->title('Stok Tidak Mencukupi')
                    ->body("Stok untuk produk \"{$produkName}\" di gudang asal tidak mencukupi (Tersedia: {$stokSekarang}, Dimutasi: {$qtyMutasi}).")
                    ->danger()
                    ->persistent()
                    ->send();
                    
                $this->halt(); // Menghentikan proses penyimpanan Filament secara aman
            }
        }
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        
        DB::transaction(function () use ($record) {
            $gudangAsalId = $record->gudang_asal_id;
            $gudangTujuanId = $record->gudang_tujuan_id;
            
            foreach ($record->mutasiStokDetails as $item) {
                $produkId = $item->produk_id;
                $qty = (float) $item->qty;
                
                // 1. Kurangi stok di gudang asal
                $stokAsal = StokSaldo::where('gudang_id', $gudangAsalId)
                    ->where('produk_id', $produkId)
                    ->first();
                    
                if ($stokAsal) {
                    $stokAsal->decrement('qty_sekarang', $qty);
                } else {
                    $stokAsal = StokSaldo::create([
                        'gudang_id' => $gudangAsalId,
                        'produk_id' => $produkId,
                        'qty_sekarang' => -$qty,
                        'harga_pokok_rata_rata' => 0,
                    ]);
                }
                
                // Catat mutasi keluar di jurnal_barang (menggunakan enum tipe_mutasi = 'TRANSFER')
                JurnalBarang::create([
                    'produk_id' => $produkId,
                    'gudang_id' => $gudangAsalId,
                    'tipe_mutasi' => 'TRANSFER',
                    'referensi_tipe' => get_class($record),
                    'referensi_id' => $record->id,
                    'qty_in' => 0,
                    'qty_out' => $qty,
                    'harga_satuan' => $stokAsal->harga_pokok_rata_rata ?? 0,
                    'keterangan' => 'Transfer Out ke ' . ($record->gudangTujuan->nama ?? 'Gudang Tujuan'),
                ]);
                
                // 2. Tambah stok di gudang tujuan
                $stokTujuan = StokSaldo::where('gudang_id', $gudangTujuanId)
                    ->where('produk_id', $produkId)
                    ->first();
                    
                if ($stokTujuan) {
                    $stokTujuan->increment('qty_sekarang', $qty);
                } else {
                    // Salin HPP rata-rata dari gudang asal jika buat baru
                    $hppAsal = $stokAsal ? $stokAsal->harga_pokok_rata_rata : 0;
                    $stokTujuan = StokSaldo::create([
                        'gudang_id' => $gudangTujuanId,
                        'produk_id' => $produkId,
                        'qty_sekarang' => $qty,
                        'harga_pokok_rata_rata' => $hppAsal,
                    ]);
                }
                
                // Catat mutasi masuk di jurnal_barang (menggunakan enum tipe_mutasi = 'TRANSFER')
                JurnalBarang::create([
                    'produk_id' => $produkId,
                    'gudang_id' => $gudangTujuanId,
                    'tipe_mutasi' => 'TRANSFER',
                    'referensi_tipe' => get_class($record),
                    'referensi_id' => $record->id,
                    'qty_in' => $qty,
                    'qty_out' => 0,
                    'harga_satuan' => $stokTujuan->harga_pokok_rata_rata ?? 0,
                    'keterangan' => 'Transfer In dari ' . ($record->gudangAsal->nama ?? 'Gudang Asal'),
                ]);
            }
        });
    }
}
