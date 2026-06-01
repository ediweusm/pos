<?php

namespace App\Filament\Resources\AkunMasters\Pages;

use App\Filament\Resources\AkunMasters\AkunMasterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAkunMaster extends EditRecord
{
    protected static string $resource = AkunMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
