<?php

namespace App\Filament\Resources\TransaksiKas\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Models\TransaksiKas;

class TransaksiKasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('nomor_bukti')
                    ->label('Nomor Bukti')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('akunPengirim.nama_akun')
                    ->label('Dari')
                    ->description(fn ($record) => $record->akunPengirim?->kode_akun)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('akunPenerima.nama_akun')
                    ->label('Ke')
                    ->description(fn ($record) => $record->akunPenerima?->kode_akun)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id')
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('cetak_kwitansi')
                    ->label('Cetak Kwitansi')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn ($record) => route('cetak.kwitansi', ['id' => $record->id]))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                // Read-only logs, no edit/delete allowed to preserve log integrity
            ]);
    }
}
