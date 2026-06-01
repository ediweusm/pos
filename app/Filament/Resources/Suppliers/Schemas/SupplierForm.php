<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pemasok / Supplier')
                    ->columnSpan('full')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('kode_supplier')
                                    ->label('Kode Supplier')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('nama_perusahaan')
                                    ->label('Nama Perusahaan')
                                    ->required(),
                                TextInput::make('nama_pic')
                                    ->label('Nama PIC (Person in Charge)'),
                                TextInput::make('no_telepon')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->maxLength(20),
                                TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->email(),
                                Toggle::make('is_aktif')
                                    ->label('Status Aktif')
                                    ->default(true),
                                Textarea::make('alamat')
                                    ->label('Alamat Kantor')
                                    ->columnSpanFull(),
                            ])
                    ])
            ]);
    }
}
