<?php

namespace App\Filament\Resources\AuditKas\Pages;

use App\Filament\Resources\AuditKas\AuditKasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAuditKas extends ListRecords
{
    protected static string $resource = AuditKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
