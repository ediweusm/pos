<?php

namespace App\Filament\Resources\Produks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class ProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: INFORMASI DASAR PRODUK
                Section::make('Informasi Produk')
                    ->columnSpan('full')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nama')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('kategori_id')
                                    ->relationship('kategori', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->unique(ignoreRecord: true),
                                TextInput::make('barcode')
                                    ->label('Barcode / EAN')
                                    ->unique(ignoreRecord: true),
                                Select::make('satuan_dasar_id')
                                    ->label('Satuan Dasar (Terkecil)')
                                    ->relationship('satuanDasar', 'nama')
                                    ->required(),
                                TextInput::make('stok_minimum')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                Toggle::make('is_aktif')
                                    ->label('Status Aktif')
                                    ->default(true),
                            ])
                    ]),

                // SECTION 2: PENETAPAN HARGA JUAL (TIERED PRICING ENGINE)
                Section::make('Penetapan Harga Jual')
                    ->columnSpan('full')
                    ->description('Atur harga bertingkat. Sistem kasir akan otomatis memilih harga yang sesuai berdasarkan jumlah beli.')
                    ->schema([
                        Repeater::make('harga')
                            ->relationship()
                            ->schema([
                                Select::make('satuan_id')
                                    ->relationship('satuan', 'nama')
                                    ->required()
                                    ->label('Satuan Jual')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('tipe_harga')
                                    ->required()
                                    ->datalist(['ECERAN', 'GROSIR', 'DISTRIBUTOR', 'MEMBER'])
                                    ->label('Tipe Harga')
                                    ->default('ECERAN'),
                                TextInput::make('minimal_qty')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->label('Min. Qty')
                                    ->suffix('unit')
                                    ->minValue(1),
                                TextInput::make('harga')
                                    ->numeric()
                                    ->required()
                                    ->label('Harga Jual (Rp)')
                                    ->prefix('Rp')
                                    ->minValue(0),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->addActionLabel('+ Tambah Tier Harga')
                            ->reorderable(false)
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['tipe_harga'])
                                    ? "{$state['tipe_harga']} — Min. {$state['minimal_qty']} unit"
                                    : null
                            )
                    ]),
            ]);
    }
}
