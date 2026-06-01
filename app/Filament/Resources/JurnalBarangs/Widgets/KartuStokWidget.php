<?php

namespace App\Filament\Resources\JurnalBarangs\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Reactive;
use App\Models\StokPeriode;
use App\Models\JurnalBarang;
use App\Models\Produk;
use Illuminate\Support\Carbon;

class KartuStokWidget extends BaseWidget
{
    // Deklarasi properti reaktif secara custom & aman dari type-hinting null
    #[Reactive]
    public ?array $tableFilters = null;

    #[Reactive]
    public $tableColumnSearches = [];

    #[Reactive]
    public $tableSearch = '';

    #[Reactive]
    public $tableSort = null;

    #[Reactive]
    public $activeTab = null;

    #[Reactive]
    public $paginators = [];

    #[Reactive]
    public $parentRecord = null;

    #[Reactive]
    public $tableGrouping = null;

    #[Reactive]
    public $tableRecordsPerPage = null;

    protected ?string $pollingInterval = null; // Matikan auto-refresh agar RAM server hemat

    protected function getStats(): array
    {
        // 1. Tangkap filter yang sedang aktif di tabel
        $filters  = $this->tableFilters;

        $produkId      = $filters['produk_id']['value'] ?? null;
        $gudangId      = $filters['gudang_id']['value'] ?? null;
        $dariTanggal   = $filters['created_at']['dari_tanggal'] ?? now()->startOfMonth()->format('Y-m-d');
        $sampaiTanggal = $filters['created_at']['sampai_tanggal'] ?? now()->endOfMonth()->format('Y-m-d');

        // Jika tidak ada produk yang dipilih, sembunyikan widget agar tidak memakan tempat
        if (! $produkId) {
            return [];
        }

        $produk = Produk::find($produkId);
        $tanggalAwal = Carbon::parse($dariTanggal)->startOfDay();
        $periodeLalu = $tanggalAwal->copy()->subMonth()->format('Y-m');

        // ====================================================================
        // LANGKAH A: Ambil Saldo Snapshot dari Bulan Sebelumnya
        // ====================================================================
        $querySnapshot = StokPeriode::where('periode', $periodeLalu)->where('produk_id', $produkId);
        if ($gudangId) $querySnapshot->where('gudang_id', $gudangId);
        
        $saldoBulanLalu = (float) $querySnapshot->sum('qty_akhir');

        // ====================================================================
        // LANGKAH B: Hitung "Mutasi Gap" (Dari Tgl 1 s.d. H-1 Tanggal Filter)
        // (Misal: Filter 15 Mei. Maka kita cari mutasi dari tgl 1 s.d. 14 Mei)
        // ====================================================================
        $awalBulanIni = $tanggalAwal->copy()->startOfMonth();
        $mutasiGapMasuk = 0;
        $mutasiGapKeluar = 0;
        
        if ($tanggalAwal->greaterThan($awalBulanIni)) {
            $queryGap = JurnalBarang::where('produk_id', $produkId)
                ->whereBetween('created_at', [$awalBulanIni, $tanggalAwal->copy()->subSecond()]);
            
            if ($gudangId) $queryGap->where('gudang_id', $gudangId);
            
            $mutasiGapMasuk  = (float) $queryGap->sum('qty_in');
            $mutasiGapKeluar = (float) $queryGap->sum('qty_out');
        }

        // SALDO AWAL RIIL = Snapshot Bulan Lalu + Mutasi Gap Masuk - Mutasi Gap Keluar
        $saldoAwal = $saldoBulanLalu + $mutasiGapMasuk - $mutasiGapKeluar;

        // ====================================================================
        // LANGKAH C: Hitung Mutasi pada Periode Filter yang Dipilih
        // ====================================================================
        $queryAktif = JurnalBarang::where('produk_id', $produkId)
            ->whereBetween('created_at', [
                $tanggalAwal, 
                Carbon::parse($sampaiTanggal)->endOfDay()
            ]);
        if ($gudangId) $queryAktif->where('gudang_id', $gudangId);

        $masuk  = (float) $queryAktif->sum('qty_in');
        $keluar = (float) $queryAktif->sum('qty_out');
        
        // SALDO AKHIR RIIL
        $saldoAkhir = $saldoAwal + $masuk - $keluar;

        return [
            Stat::make('Saldo Awal', number_format($saldoAwal, 0, ',', '.'))
                ->description(strtoupper($produk->nama))
                ->descriptionIcon('heroicon-m-cube')
                ->color('gray'),
                
            Stat::make('Masuk (In)', number_format($masuk, 0, ',', '.'))
                ->description('Total masuk di rentang tanggal')
                ->descriptionIcon('heroicon-m-arrow-down-left')
                ->color('success'),
                
            Stat::make('Keluar (Out)', number_format($keluar, 0, ',', '.'))
                ->description('Total keluar di rentang tanggal')
                ->descriptionIcon('heroicon-m-arrow-up-right')
                ->color('danger'),
                
            Stat::make('Saldo Akhir', number_format($saldoAkhir, 0, ',', '.'))
                ->description('Sisa pada ' . Carbon::parse($sampaiTanggal)->format('d M Y'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),
        ];
    }
}
