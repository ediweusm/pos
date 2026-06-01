<?php

namespace App\Filament\Resources\KasOperasionals\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class KasOperasionalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Catatan Kas Operasional')
                    ->columnSpan('full')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('jenis')
                                    ->label('Jenis Transaksi')
                                    ->options([
                                        'PENGELUARAN' => 'Pengeluaran Kas (Bayar Biaya / Beban)',
                                        'PEMASUKAN' => 'Pemasukan Kas (Pendapatan Lain-Lain)',
                                    ])
                                    ->required()
                                    ->live(),
                                DatePicker::make('tanggal')
                                    ->label('Tanggal')
                                    ->default(now())
                                    ->required(),
                                TextInput::make('nomor_bukti')
                                    ->label('Nomor Bukti')
                                    ->default(fn () => 'OPR-' . date('YmdHis'))
                                    ->readOnly()
                                    ->required(),
                                TextInput::make('nominal')
                                    ->label('Nominal Transaksi')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                                Select::make('akun_kas_id')
                                    ->label('Pilih Akun Kas / Bank (Sumber/Tujuan)')
                                    ->relationship('akunKas', 'nama_akun')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->kode_akun} - {$record->nama_akun}")
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('akun_lawan_id')
                                    ->label('Pilih Akun Kategori (Beban / Pendapatan Lain)')
                                    ->relationship('akunLawan', 'nama_akun')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->kode_akun} - {$record->nama_akun}")
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Textarea::make('keterangan')
                                    ->label('Keterangan / Deskripsi')
                                    ->placeholder('Misal: Pembayaran tagihan listrik bulan Mei 2026')
                                    ->columnSpanFull()
                                    ->rows(3)
                                    ->required(),
                            ])
                    ])
            ]);
    }
}
