<?php

namespace App\Filament\Resources\Pelanggans\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class PelangganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pelanggan / Customer')
                    ->columnSpan('full')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('kode_pelanggan')
                                    ->label('Kode Pelanggan')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('nama')
                                    ->label('Nama Lengkap')
                                    ->required(),
                                TextInput::make('no_telepon')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->maxLength(20),
                                TextInput::make('poin_loyalitas')
                                    ->label('Poin Loyalitas')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                Toggle::make('is_aktif')
                                    ->label('Status Aktif')
                                    ->default(true),
                                Textarea::make('alamat')
                                    ->label('Alamat Lengkap')
                                    ->columnSpanFull(),
                            ])
                    ])
            ]);
    }
}
