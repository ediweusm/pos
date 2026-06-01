<?php

namespace App\Filament\Resources\KasOperasionals\Pages;

use App\Filament\Resources\KasOperasionals\KasOperasionalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKasOperasional extends EditRecord
{
    protected static string $resource = KasOperasionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
