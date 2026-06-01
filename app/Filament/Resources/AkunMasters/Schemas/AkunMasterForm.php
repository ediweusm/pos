<?php

namespace App\Filament\Resources\AkunMasters\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class AkunMasterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_akun')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('nama_akun')
                    ->required(),
                Select::make('tipe_akun')
                    ->options([
                        'ASET' => 'ASET',
                        'KEWAJIBAN' => 'KEWAJIBAN',
                        'EKUITAS' => 'EKUITAS',
                        'PENDAPATAN' => 'PENDAPATAN',
                        'BEBAN' => 'BEBAN',
                    ])
                    ->required(),
                Toggle::make('is_aktif')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }
}
