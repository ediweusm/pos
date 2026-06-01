<?php

namespace App\Filament\Resources\PenyesuaianStoks\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;

class PenyesuaianStoksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal Opname')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('gudang.nama')
                    ->label('Gudang')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('produk.nama')
                    ->label('Produk')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('qty_sistem')
                    ->label('Sistem')
                    ->numeric(),

                TextColumn::make('qty_fisik')
                    ->label('Fisik')
                    ->numeric(),

                TextColumn::make('selisih')
                    ->label('Selisih')
                    ->numeric()
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : ($state > 0 ? 'success' : 'gray')),

                TextColumn::make('keterangan')
                    ->wrap()
                    ->limit(30),
            ])
            ->filters([
                SelectFilter::make('gudang_id')
                    ->relationship('gudang', 'nama')
                    ->label('Gudang / Lokasi'),

                SelectFilter::make('produk_id')
                    ->relationship('produk', 'nama')
                    ->searchable()
                    ->preload()
                    ->label('Produk'),

                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
            ])
            ->headerActions([
                Action::make('print')
                    ->label('Cetak Laporan Opname')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn ($livewire) => route('penyesuaian-stok.print', [
                        'gudang_id' => $livewire->tableFilters['gudang_id']['value'] ?? null,
                        'produk_id' => $livewire->tableFilters['produk_id']['value'] ?? null,
                        'dari_tanggal' => $livewire->tableFilters['tanggal']['dari_tanggal'] ?? null,
                        'sampai_tanggal' => $livewire->tableFilters['tanggal']['sampai_tanggal'] ?? null,
                    ]))
                    ->openUrlInNewTab()
            ])
            ->recordActions([
                // Read-only logs
            ])
            ->toolbarActions([
                // No bulk delete or edit to preserve absolute audit logs integrity
            ]);
    }
}
