<?php

namespace App\Filament\Resources\AkunMasters\Pages;

use App\Filament\Resources\AkunMasters\AkunMasterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAkunMasters extends ListRecords
{
    protected static string $resource = AkunMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
