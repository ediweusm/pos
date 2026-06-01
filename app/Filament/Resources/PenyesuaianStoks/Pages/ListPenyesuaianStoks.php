<?php

namespace App\Filament\Resources\PenyesuaianStoks\Pages;

use App\Filament\Resources\PenyesuaianStoks\PenyesuaianStokResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenyesuaianStoks extends ListRecords
{
    protected static string $resource = PenyesuaianStokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
