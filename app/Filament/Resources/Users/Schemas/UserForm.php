<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengguna & Akses')
                    ->columnSpan('full')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->email()
                                    ->required()
                                    ->unique(table: 'users', ignoreRecord: true),
                                TextInput::make('password')
                                    ->label('Kata Sandi (Password)')
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state)),
                                Select::make('roles')
                                    ->label('Hak Akses / Pangkat')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload(),
                                Select::make('cabang_id')
                                    ->label('Penempatan Cabang')
                                    ->relationship('cabang', 'nama_cabang')
                                    ->preload()
                                    ->nullable(),
                                Select::make('gudang_id')
                                    ->label('Penempatan Etalase/Gudang')
                                    ->relationship('gudang', 'nama')
                                    ->preload()
                                    ->nullable(),
                            ])
                    ])
            ]);
    }
}
