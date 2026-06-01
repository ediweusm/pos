<?php

namespace App\Filament\Resources\AuditKas\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;

class AuditKasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal Audit')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('akun.nama_akun')
                    ->label('Akun Kas')
                    ->description(fn ($record) => $record->akun?->kode_akun)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nominal_sistem')
                    ->label('Saldo Sistem')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('nominal_fisik')
                    ->label('Saldo Fisik')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('selisih')
                    ->label('Selisih')
                    ->money('IDR', locale: 'id')
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : ($state > 0 ? 'success' : 'gray'))
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('akun_id')
                    ->relationship('akun', 'nama_akun')
                    ->label('Akun Kas')
                    ->searchable()
                    ->preload(),

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
                    ->label('Cetak Laporan Audit')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn ($livewire) => route('audit-kas.print', [
                        'akun_id' => $livewire->tableFilters['akun_id']['value'] ?? null,
                        'dari_tanggal' => $livewire->tableFilters['tanggal']['dari_tanggal'] ?? null,
                        'sampai_tanggal' => $livewire->tableFilters['tanggal']['sampai_tanggal'] ?? null,
                    ]))
                    ->openUrlInNewTab()
            ])
            ->recordActions([
                // Read-only logs, no record actions allowed to preserve log integrity
            ])
            ->toolbarActions([
                // No bulk actions allowed to preserve log integrity
            ]);
    }
}

