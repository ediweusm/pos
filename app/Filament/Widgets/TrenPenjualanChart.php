<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TrenPenjualanChart extends ChartWidget
{
    protected ?string $heading = 'Tren Omzet 30 Hari Terakhir';
    
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d M');
            $data[] = (float) DB::table('pos_transaksi')
                ->whereDate('created_at', $date->toDateString())
                ->sum('grand_total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Omzet Harian (Rp)',
                    'data' => $data,
                    'borderColor' => '#fbbf24', // Amber/Orange color matching the theme
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4, // smooth curvature
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'grid' => [
                        'display' => true,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => true,
                    ],
                ],
            ],
        ];
    }
}
