<?php

namespace App\Filament\Resources\PenyesuaianStoks\Pages;

use App\Filament\Resources\PenyesuaianStoks\PenyesuaianStokResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPenyesuaianStok extends EditRecord
{
    protected static string $resource = PenyesuaianStokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
