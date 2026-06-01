<?php

namespace App\Filament\Resources\Gudangs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class GudangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cabang_id')
                    ->label('Pemilik Cabang')
                    ->relationship('cabang', 'nama_cabang')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Select::make('tipe')
                    ->label('Tipe Gudang')
                    ->options([
                        'PENYIMPANAN' => 'Gudang Penyimpanan (Belakang)',
                        'ETALASE' => 'Etalase Toko (Depan)',
                    ])
                    ->required(),
                Toggle::make('is_aktif')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }
}
