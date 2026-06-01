<?php

namespace App\Filament\Resources\KasOperasionals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class KasOperasionalTable
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
                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENGELUARAN' => 'danger',
                        'PEMASUKAN' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('akunKas.nama_akun')
                    ->label('Akun Kas / Bank')
                    ->sortable(),
                TextColumn::make('akunLawan.nama_akun')
                    ->label('Kategori')
                    ->sortable(),
                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 2, ',', '.'))
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
