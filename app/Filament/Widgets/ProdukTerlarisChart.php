<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProdukTerlarisChart extends ChartWidget
{
    protected ?string $heading = '5 Produk Terlaris Bulan Ini';
    
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $bestSellers = DB::table('pos_transaksi_detail')
            ->join('pos_transaksi', 'pos_transaksi.id', '=', 'pos_transaksi_detail.pos_transaksi_id')
            ->join('produk', 'produk.id', '=', 'pos_transaksi_detail.produk_id')
            ->whereBetween('pos_transaksi.created_at', [$startOfMonth, $endOfMonth])
            ->select(
                'produk.nama as nama_produk',
                DB::raw('SUM(pos_transaksi_detail.qty) as total_qty')
            )
            ->groupBy('pos_transaksi_detail.produk_id', 'produk.nama')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();
            
        $labels = [];
        $data = [];
        
        foreach ($bestSellers as $item) {
            $labels[] = $item->nama_produk;
            $data[] = (float) $item->total_qty;
        }
        
        // Fallback jika belum ada data transaksi
        if (empty($labels)) {
            $labels = ['Produk A', 'Produk B', 'Produk C', 'Produk D', 'Produk E'];
            $data = [0, 0, 0, 0, 0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual (Qty)',
                    'data' => $data,
                    'backgroundColor' => [
                        '#10b981', // emerald
                        '#f59e0b', // amber
                        '#3b82f6', // blue
                        '#ec4899', // pink
                        '#8b5cf6', // purple
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // Menjadikan diagram batang horizontal agar nama produk panjang terbaca sempurna
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => true,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}
