<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_supplier')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_perusahaan')
                    ->label('Nama Perusahaan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_pic')
                    ->label('PIC / Kontak')
                    ->searchable(),
                TextColumn::make('no_telepon')
                    ->label('Nomor Telepon'),
                IconColumn::make('is_aktif')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
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
