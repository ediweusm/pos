<?php

namespace App\Filament\Resources\JurnalBarangs\Tables;

use App\Models\JurnalBarang;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Carbon;

class JurnalBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            
            // ─── SKENARIO A: BARIS SALDO AWAL DINAMIS VIA DESCRIPTION ───
            ->description(function (\Filament\Tables\Contracts\HasTable $livewire) {
                // 1. Ambil state filter yang sedang dipilih oleh user di layar
                $filters = $livewire->tableFilters;
                $produkId = $filters['produk_id']['value'] ?? null;
                $gudangId = $filters['gudang_id']['value'] ?? null;
                $dariTanggal = $filters['created_at']['dari_tanggal'] ?? now()->startOfMonth()->format('Y-m-d');

                // Jika filter produk masih kosong, tampilkan panduan pengisian
                if (!$produkId) {
                    return new HtmlString('
                        <div class="text-xs text-amber-600 dark:text-amber-400 font-medium px-3 py-2 bg-grey-50 dark:bg-amber-950/30 rounded-md border border-amber-200 dark:border-amber-900/50 inline-block mt-2 shadow-sm">
                            <span class="font-bold">💡 Petunjuk Audit:</span> Silakan gunakan tombol filter di kanan atas untuk memilih <strong>Nama Produk</strong> agar perhitungan Saldo Awal dapat dimunculkan.
                        </div>
                    ');
                }

                // 2. Jalankan Logika Kalkulasi Berbasis Snapshot + Gap Mutasi
                $tanggalAwal = Carbon::parse($dariTanggal)->startOfDay();
                $periodeLalu = $tanggalAwal->copy()->subMonth()->format('Y-m');

                // Langkah 2.1: Tarik saldo akhir bulan lalu dari tabel snapshot
                $querySnapshot = \App\Models\StokPeriode::where('periode', $periodeLalu)->where('produk_id', $produkId);
                if ($gudangId) $querySnapshot->where('gudang_id', $gudangId);
                $saldoBulanLalu = (float) $querySnapshot->sum('qty_akhir');

                // Langkah 2.2: Hitung akumulasi mutasi berjalan dari tanggal 1 s.d H-1 tanggal filter
                $awalBulanIni = $tanggalAwal->copy()->startOfMonth();
                $mutasiGapMasuk = 0;
                $mutasiGapKeluar = 0;

                if ($tanggalAwal->greaterThan($awalBulanIni)) {
                    $queryGap = \App\Models\JurnalBarang::where('produk_id', $produkId)
                        ->whereBetween('created_at', [$awalBulanIni, $tanggalAwal->copy()->subSecond()]);
                    if ($gudangId) $queryGap->where('gudang_id', $gudangId);
                    
                    $mutasiGapMasuk = (float) $queryGap->sum('qty_in');
                    $mutasiGapKeluar = (float) $queryGap->sum('qty_out');
                }

                // Langkah 2.3: Rumus Final Saldo Awal
                $saldoAwal = $saldoBulanLalu + $mutasiGapMasuk - $mutasiGapKeluar;
                
                $formatSaldo = number_format($saldoAwal, 0, ',', '.');
                $formatTanggal = $tanggalAwal->format('d F Y');

                // 3. Kembalikan bentuk komponen visual Banner Saldo Awal
                return new HtmlString("
                    <div class='mt-3 p-3.5 bg-primary-50 dark:bg-primary-950/20 border border-primary-200 dark:border-primary-900/50 rounded-lg flex justify-between items-center max-w-xl shadow-sm transition-all'>
                        <div class='flex flex-col'>
                            <span class='text-[10px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-wider'>SALDO AWAL PERIODE</span>
                            <span class='text-xs text-gray-500 dark:text-gray-400 mt-0.5'>Posisi buku barang pada tanggal {$formatTanggal}</span>
                        </div>
                        <div class='text-xl font-mono font-extrabold text-primary-700 dark:text-primary-400 tabular-nums'>
                            {$formatSaldo} Items
                        </div>
                    </div>
                ");
            })
            
            // ─── STRUKTUR KOLOM TABEL ───
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal & Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                
                TextColumn::make('produk.nama')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('gudang.nama')
                    ->label('Lokasi')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('tipe_mutasi')
                    ->label('Jenis Mutasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PURCHASE' => 'info',
                        'SALE' => 'success',
                        'RETURN' => 'warning',
                        'ADJUSTMENT' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('qty_in')
                    ->label('Masuk (IN)')
                    ->numeric()
                    ->color('info')
                    ->weight('bold'),

                TextColumn::make('qty_out')
                    ->label('Keluar (OUT)')
                    ->numeric()
                    ->color('danger')
                    ->weight('bold'),

                TextColumn::make('referensi_dokumen')
                    ->label('No. Referensi')
                    ->getStateUsing(function (JurnalBarang $record) {
                        if (!$record->referensi) return '-';
                        $tipe = class_basename($record->referensi_tipe);
                        
                        if ($tipe === 'PosTransaksi') {
                            return $record->referensi->nomor_nota ?? '-';
                        } elseif ($tipe === 'Pembelian') {
                            return $record->referensi->nomor_faktur ?? '-';
                        }
                        return $tipe . ' #' . $record->referensi_id;
                    })
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where('referensi_id', 'like', "%{$search}%");
                    }),
            ])
            
            // ─── STRUKTUR FILTER TABEL ───
            ->filters([
                SelectFilter::make('gudang_id')
                    ->relationship('gudang', 'nama')
                    ->label('Filter Gudang'),

                SelectFilter::make('produk_id')
                    ->relationship('produk', 'nama')
                    ->searchable()
                    ->preload()
                    ->label('Filter Produk'),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth()), // Mengunci pencarian awal bulan secara default
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfMonth()),   // Mengunci pencarian akhir bulan secara default
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            
            // ─── TOMBOL CETAK KARTU STOK ───
            ->headerActions([
                Action::make('print')
                    ->label('Cetak Kartu Stok')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn ($livewire) => route('kartu-stok.print', [
                        'produk_id' => $livewire->tableFilters['produk_id']['value'] ?? null,
                        'gudang_id' => $livewire->tableFilters['gudang_id']['value'] ?? null,
                        'dari_tanggal' => $livewire->tableFilters['created_at']['dari_tanggal'] ?? null,
                        'sampai_tanggal' => $livewire->tableFilters['created_at']['sampai_tanggal'] ?? null,
                    ]))
                    ->openUrlInNewTab()
                    ->visible(fn ($livewire) => !empty($livewire->tableFilters['produk_id']['value']))
            ])
            ->recordActions([])
            ->bulkActions([])
            
            // ─── SKENARIO A: BARIS SALDO AKHIR DI BAWAH TABEL ───
            ->contentFooter(function (\Filament\Tables\Contracts\HasTable $livewire) {
                $filters = $livewire->tableFilters; 
                $produkId = $filters['produk_id']['value'] ?? null;
                $gudangId = $filters['gudang_id']['value'] ?? null;
                
                // Jika produk belum dipilih, jangan tampilkan footer saldo akhir
                if (!$produkId) return null;

                $dariTanggal = $filters['created_at']['dari_tanggal'] ?? now()->startOfMonth()->format('Y-m-d');
                $sampaiTanggal = $filters['created_at']['sampai_tanggal'] ?? now()->endOfMonth()->format('Y-m-d');

                $tanggalAwal = Carbon::parse($dariTanggal)->startOfDay();
                $tanggalAkhir = Carbon::parse($sampaiTanggal)->endOfDay();
                $periodeLalu = $tanggalAwal->copy()->subMonth()->format('Y-m');

                // 1. Ulangi Kalkulasi Saldo Awal (Sama persis seperti Header)
                $querySnapshot = \App\Models\StokPeriode::where('periode', $periodeLalu)->where('produk_id', $produkId);
                if ($gudangId) $querySnapshot->where('gudang_id', $gudangId);
                $saldoBulanLalu = (float) $querySnapshot->sum('qty_akhir');

                $awalBulanIni = $tanggalAwal->copy()->startOfMonth();
                $mutasiGapMasuk = 0;
                $mutasiGapKeluar = 0;

                if ($tanggalAwal->greaterThan($awalBulanIni)) {
                    $queryGap = \App\Models\JurnalBarang::where('produk_id', $produkId)
                        ->whereBetween('created_at', [$awalBulanIni, $tanggalAwal->copy()->subSecond()]);
                    if ($gudangId) $queryGap->where('gudang_id', $gudangId);
                    
                    $mutasiGapMasuk = (float) $queryGap->sum('qty_in');
                    $mutasiGapKeluar = (float) $queryGap->sum('qty_out');
                }

                $saldoAwal = $saldoBulanLalu + $mutasiGapMasuk - $mutasiGapKeluar;

                // 2. Hitung Total Masuk & Keluar pada Rentang Tanggal Terpilih (Data Tabel)
                $queryAktif = \App\Models\JurnalBarang::where('produk_id', $produkId)
                    ->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]);
                if ($gudangId) $queryAktif->where('gudang_id', $gudangId);

                $totalMasuk = (float) $queryAktif->sum('qty_in');
                $totalKeluar = (float) $queryAktif->sum('qty_out');

                // 3. Rumus Final: Saldo Akhir
                $saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;

                // Formatting Angka
                $formatAwal = number_format($saldoAwal, 0, ',', '.');
                $formatMasuk = number_format($totalMasuk, 0, ',', '.');
                $formatKeluar = number_format($totalKeluar, 0, ',', '.');
                $formatAkhir = number_format($saldoAkhir, 0, ',', '.');
                $formatTanggalAkhir = $tanggalAkhir->format('d F Y');

                // 4. Return View Laporan Footer
                return view('filament.jurnal-barang-footer', [
                    'saldoAwal'          => $formatAwal,
                    'totalMasuk'         => $formatMasuk,
                    'totalKeluar'        => $formatKeluar,
                    'saldoAkhir'         => $formatAkhir,
                    'formatTanggalAkhir' => $formatTanggalAkhir,
                ]);
            });
    }
}
