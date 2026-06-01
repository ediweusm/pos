<?php

namespace App\Filament\Resources\Cabangs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class CabangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_cabang')
                    ->label('Kode Cabang')
                    ->required()
                    ->unique('cabang', 'kode_cabang', ignoreRecord: true)
                    ->default(fn () => 'CBG-' . time())
                    ->maxLength(50),
                TextInput::make('nama_cabang')
                    ->label('Nama Cabang')
                    ->required()
                    ->maxLength(150),
                Textarea::make('alamat')
                    ->label('Alamat')
                    ->rows(3)
                    ->maxLength(500),
                Toggle::make('is_aktif')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }
}
