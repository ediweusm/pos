<?php

namespace App\Filament\Resources\ShiftKasirResource\Pages;

use App\Filament\Resources\ShiftKasirResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShiftKasirs extends ListRecords
{
    protected static string $resource = ShiftKasirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
