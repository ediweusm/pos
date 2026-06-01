<?php

namespace App\Filament\Resources\AkunCfgs\Pages;

use App\Filament\Resources\AkunCfgs\AkunCfgResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAkunCfg extends EditRecord
{
    protected static string $resource = AkunCfgResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
