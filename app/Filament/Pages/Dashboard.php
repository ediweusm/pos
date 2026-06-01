<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TrenPenjualanChart;
use App\Filament\Widgets\ProdukTerlarisChart;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $title = 'Dashboard Analitik';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            TrenPenjualanChart::class,
            ProdukTerlarisChart::class,
        ];
    }
}
