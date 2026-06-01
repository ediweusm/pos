<?php

namespace App\Filament\Resources\Cabangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class CabangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_cabang')
                    ->label('Kode Cabang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_cabang')
                    ->label('Nama Cabang')
                    ->searchable()
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
