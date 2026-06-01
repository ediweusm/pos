<?php

namespace App\Filament\Resources\AkunCfgs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class AkunCfgsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_event')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_event')
                    ->searchable(),
                TextColumn::make('akunDebit.nama_akun')
                    ->label('Akun Debit')
                    ->sortable(),
                TextColumn::make('akunKredit.nama_akun')
                    ->label('Akun Kredit')
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
