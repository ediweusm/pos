<?php

namespace App\Filament\Resources\JurnalBarangs\Pages;

use App\Filament\Resources\JurnalBarangs\JurnalBarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use App\Filament\Resources\JurnalBarangs\Widgets\KartuStokWidget;

class ListJurnalBarangs extends ListRecords
{
    protected static string $resource = JurnalBarangResource::class;

    public array $tableColumnSearches = [];

    protected function getHeaderActions(): array
    {
        return [
            // Read-only resource, no create action needed
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            KartuStokWidget::class,
        ];
    }
}
