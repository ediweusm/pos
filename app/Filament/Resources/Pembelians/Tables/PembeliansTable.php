<?php

namespace App\Filament\Resources\Pembelians\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ForceDeleteBulkAction;

class PembeliansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_faktur')
                    ->label('Nomor Faktur')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('supplier.nama_perusahaan')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gudang.nama')
                    ->label('Gudang')
                    ->sortable(),
                TextColumn::make('status_pembayaran')
                    ->label('Status Bayar')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'LUNAS' => 'success',
                        'UTANG' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('subtotal_barang')
                    ->label('Subtotal')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('ppn_nominal')
                    ->label('PPN')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money('IDR')
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
                    DeleteBulkAction::make(), // Ini hanya memindahkan ke "tong sampah" (mengisi deleted_at)
                    ForceDeleteBulkAction::make(), // GUNAKAN INI untuk melenyapkan data permanen dari MySQL
                ]),
            ]);
    }
}
