<?php

namespace App\Filament\Resources\Satuans\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class SatuanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('simbol')
                    ->maxLength(15),
            ]);
    }
}
