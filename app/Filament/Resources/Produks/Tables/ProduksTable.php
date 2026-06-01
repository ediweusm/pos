<?php

namespace App\Filament\Resources\Produks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;

class ProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->sortable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('satuanDasar.nama')
                    ->label('Satuan Dasar')
                    ->sortable(),
                
                // Tambahan Kolom Stok Terkini
                TextColumn::make('stok')
                    ->label('Stok Terkini')
                    ->getStateUsing(fn ($record) => $record->stokSaldos->sum('qty_sekarang'))
                    ->numeric()
                    ->badge()
                    ->color(fn ($state) => $state <= 0 ? 'danger' : 'success'),

                // Tambahan Kolom Harga Jual (Eceran Default)
                TextColumn::make('harga_jual')
                    ->label('Harga Jual')
                    ->getStateUsing(fn ($record) => $record->getHargaEceranDefault()?->harga ?? 0)
                    ->money('IDR', locale: 'id')
                    ->alignRight()
                    ->weight('bold'),

                TextColumn::make('stok_minimum')
                    ->label('Stok Minimum')
                    ->alignRight(),
                IconColumn::make('is_aktif')
                    ->label('Status Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('kategori_id')
                    ->relationship('kategori', 'nama')
                    ->label('Filter Kategori'),
            ])
            ->headerActions([
                Action::make('print')
                    ->label('Cetak Daftar Produk')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn ($livewire) => route('produk.print', [
                        'search' => $livewire->tableSearch ?? null,
                        'kategori_id' => $livewire->tableFilters['kategori_id']['value'] ?? null,
                    ]))
                    ->openUrlInNewTab()
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
