<?php

namespace App\Filament\Resources\ShiftKasirResource\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ShiftKasirTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cabang.nama_cabang')
                    ->label('Cabang')
                    ->sortable(),
                TextColumn::make('gudang.nama')
                    ->label('Etalase/Gudang')
                    ->sortable(),
                TextColumn::make('waktu_buka')
                    ->label('Waktu Buka')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('waktu_tutup')
                    ->label('Waktu Tutup')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('modal_awal')
                    ->label('Modal Awal')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('total_penjualan')
                    ->label('Penjualan')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('saldo_aktual')
                    ->label('Uang Fisik')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('selisih')
                    ->label('Selisih')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->placeholder('-')
                    ->color(fn ($state) => $state < 0 ? 'danger' : ($state > 0 ? 'success' : 'gray')),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'OPEN' => 'success',
                        'CLOSED' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('waktu_buka', 'desc');
    }
}
