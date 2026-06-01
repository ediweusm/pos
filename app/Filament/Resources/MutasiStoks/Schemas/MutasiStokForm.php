<?php

namespace App\Filament\Resources\MutasiStoks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class MutasiStokForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Mutasi Stok')
                    ->columnSpan('full')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tanggal')
                                    ->label('Tanggal Mutasi')
                                    ->default(now())
                                    ->required(),
                                TextInput::make('nomor_bukti')
                                    ->label('Nomor Bukti')
                                    ->default(fn () => 'MUT-' . date('YmdHis'))
                                    ->readOnly()
                                    ->required(),
                                Select::make('gudang_asal_id')
                                    ->label('Gudang / Etalase Asal (Sumber)')
                                    ->relationship('gudangAsal', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('gudang_tujuan_id')
                                    ->label('Gudang / Etalase Tujuan (Penerima)')
                                    ->relationship('gudangTujuan', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->different('gudang_asal_id')
                                    ->validationMessages([
                                        'different' => 'Gudang tujuan tidak boleh sama dengan gudang asal.',
                                    ]),
                                Textarea::make('keterangan')
                                    ->label('Catatan / Keterangan')
                                    ->columnSpanFull()
                                    ->rows(3),
                            ])
                    ]),

                Section::make('Rincian Barang yang Dimutasi')
                    ->columnSpan('full')
                    ->schema([
                        Repeater::make('mutasiStokDetails')
                            ->relationship('mutasiStokDetails')
                            ->schema([
                                Select::make('produk_id')
                                    ->label('Nama Produk')
                                    ->relationship('produk', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('qty')
                                    ->label('Jumlah (Qty)')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0.01)
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->required()
                            ->minItems(1)
                    ])
            ]);
    }
}
