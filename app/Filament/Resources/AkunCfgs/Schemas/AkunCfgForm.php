<?php

namespace App\Filament\Resources\AkunCfgs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class AkunCfgForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_event')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('nama_event')
                    ->required(),
                Select::make('akun_debit_id')
                    ->label('Akun Debit')
                    ->relationship('akunDebit', 'nama_akun')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('akun_kredit_id')
                    ->label('Akun Kredit')
                    ->relationship('akunKredit', 'nama_akun')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
