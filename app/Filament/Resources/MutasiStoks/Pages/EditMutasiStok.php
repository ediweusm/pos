<?php

namespace App\Filament\Resources\MutasiStoks\Pages;

use App\Filament\Resources\MutasiStoks\MutasiStokResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMutasiStok extends EditRecord
{
    protected static string $resource = MutasiStokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
