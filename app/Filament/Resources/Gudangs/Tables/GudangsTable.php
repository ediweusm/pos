<?php

namespace App\Filament\Resources\Gudangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class GudangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cabang.nama_cabang')
                    ->label('Cabang')
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama Gudang/Etalase')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipe')
                    ->label('Tipe Gudang')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENYIMPANAN' => 'primary',
                        'ETALASE' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                IconColumn::make('is_aktif')
                    ->label('Status Aktif')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
