<?php

namespace App\Filament\Resources\AkunCfgs\Pages;

use App\Filament\Resources\AkunCfgs\AkunCfgResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAkunCfgs extends ListRecords
{
    protected static string $resource = AkunCfgResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
