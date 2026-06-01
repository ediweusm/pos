<?php

namespace App\Filament\Resources\AkunMasters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class AkunMastersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_akun')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_akun')
                    ->searchable(),
                TextColumn::make('tipe_akun')
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
