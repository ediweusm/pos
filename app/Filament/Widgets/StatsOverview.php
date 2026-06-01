<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        // 1. Omzet Bulan Ini
        $omzet = (float) DB::table('pos_transaksi')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('grand_total');

        // Sparkline untuk 7 hari terakhir (historis)
        $sparkline = [];
        for ($i = 6; $i >= 0; $i--) {
            $sparkline[] = (float) DB::table('pos_transaksi')
                ->whereDate('created_at', now()->subDays($i)->toDateString())
                ->sum('grand_total');
        }

        // 2. Laba Bersih Bulan Ini
        $transaksi = DB::table('akun_trans')
            ->join('jurnal', 'jurnal.id', '=', 'akun_trans.jurnal_id')
            ->join('akun_master', 'akun_master.id', '=', 'akun_trans.akun_id')
            ->whereBetween('jurnal.tanggal', [$startOfMonth, $endOfMonth])
            ->whereIn('akun_master.tipe_akun', ['PENDAPATAN', 'BEBAN'])
            ->select(
                'akun_master.tipe_akun',
                DB::raw('SUM(akun_trans.debit) as total_debit'),
                DB::raw('SUM(akun_trans.kredit) as total_kredit')
            )
            ->groupBy('akun_master.tipe_akun')
            ->get();
            
        $totalPendapatan = 0;
        $totalBeban = 0;
        
        foreach ($transaksi as $row) {
            if ($row->tipe_akun === 'PENDAPATAN') {
                $totalPendapatan += ($row->total_kredit - $row->total_debit);
            } elseif ($row->tipe_akun === 'BEBAN') {
                $totalBeban += ($row->total_debit - $row->total_kredit);
            }
        }
        
        $laba = $totalPendapatan - $totalBeban;

        // 3. Total Transaksi POS Hari Ini
        $totalTransaksi = DB::table('pos_transaksi')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        return [
            Stat::make('Omzet Bulan Ini', 'Rp ' . number_format($omzet, 0, ',', '.'))
                ->description('Total omzet transaksi POS bulan berjalan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($sparkline)
                ->color('success'),
                
            Stat::make('Laba Bersih Bulan Ini', 'Rp ' . number_format($laba, 0, ',', '.'))
                ->description('Pendapatan dikurangi beban bulan berjalan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
                
            Stat::make('Total Transaksi POS', $totalTransaksi . ' Transaksi')
                ->description('Jumlah transaksi kasir POS hari ini')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),
        ];
    }
}
