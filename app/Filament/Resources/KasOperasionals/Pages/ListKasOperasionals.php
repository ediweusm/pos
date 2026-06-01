<?php

namespace App\Filament\Resources\KasOperasionals\Pages;

use App\Filament\Resources\KasOperasionals\KasOperasionalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKasOperasionals extends ListRecords
{
    protected static string $resource = KasOperasionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
