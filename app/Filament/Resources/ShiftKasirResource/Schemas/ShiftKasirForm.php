<?php

namespace App\Filament\Resources\ShiftKasirResource\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class ShiftKasirForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord ? 'Buka Shift Kasir (Cash Drawer)' : 'Tutup Shift Kasir (Cash Drawer)')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('modal_awal')
                                    ->label('Modal Awal (Uang Kembalian)')
                                    ->rules(['numeric'])
                                    ->prefix('Rp')
                                    ->required()
                                    ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),

                                TextInput::make('waktu_buka')
                                    ->label('Waktu Buka Shift')
                                    ->disabled()
                                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),

                                TextInput::make('total_penjualan')
                                    ->label('Penjualan Tunai (Laci Kas)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),

                                TextInput::make('saldo_aktual')
                                    ->label('Total Uang Fisik di Laci Saat Ini')
                                    ->rules(['numeric'])
                                    ->prefix('Rp')
                                    ->required()
                                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
                            ])
                    ])
            ]);
    }
}
