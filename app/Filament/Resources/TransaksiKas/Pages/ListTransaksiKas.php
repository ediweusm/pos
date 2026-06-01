<?php

namespace App\Filament\Resources\TransaksiKas\Pages;

use App\Filament\Resources\TransaksiKas\TransaksiKasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiKas extends ListRecords
{
    protected static string $resource = TransaksiKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
