<?php

namespace App\Filament\Resources\MutasiStoks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class MutasiStokTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('nomor_bukti')
                    ->label('No. Bukti')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gudangAsal.nama')
                    ->label('Gudang Asal (Sumber)')
                    ->sortable(),
                TextColumn::make('gudangTujuan.nama')
                    ->label('Gudang Tujuan (Penerima)')
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50),
            ])
            ->defaultSort('tanggal', 'desc')
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
