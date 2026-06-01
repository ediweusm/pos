<?php

namespace App\Filament\Resources\PenyesuaianStoks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\StokSaldo;

class PenyesuaianStokForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([
                DatePicker::make('tanggal')
                    ->label('Tanggal Opname')
                    ->default(now())
                    ->required()
                    ->columnSpan(3),

                Select::make('gudang_id')
                    ->label('Gudang / Lokasi')
                    ->relationship('gudang', 'nama')
                    ->searchable()
                    ->live()
                    ->required()
                    ->afterStateUpdated(fn (Set $set) => self::resetPerhitungan($set)),

                Select::make('produk_id')
                    ->label('Produk')
                    ->relationship('produk', 'nama')
                    ->searchable()
                    ->live()
                    ->required()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        if (!$get('gudang_id') || !$state) return;
                        
                        // Tarik stok dari database berdasarkan Gudang & Produk
                        $stok = StokSaldo::where('gudang_id', $get('gudang_id'))
                                         ->where('produk_id', $state)
                                         ->value('qty_sekarang') ?? 0;
                        
                        $set('qty_sistem', $stok);
                        self::hitungSelisih($get, $set);
                    }),
            ]),

            Grid::make(3)->schema([
                TextInput::make('qty_sistem')
                    ->label('Stok di Sistem')
                    ->numeric()
                    ->readOnly()
                    ->default(0),

                TextInput::make('qty_fisik')
                    ->label('Stok Fisik (Riil)')
                    ->numeric()
                    ->live(debounce: 500)
                    ->required()
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::hitungSelisih($get, $set)),

                TextInput::make('selisih')
                    ->label('Selisih')
                    ->numeric()
                    ->readOnly()
                    ->default(0),
            ]),

            Textarea::make('keterangan')
                ->label('Keterangan / Alasan')
                ->required()
                ->columnSpanFull(),
        ]);
    }

    // Fungsi Helper
    private static function resetPerhitungan(Set $set): void
    {
        $set('produk_id', null);
        $set('qty_sistem', 0);
        $set('qty_fisik', null);
        $set('selisih', 0);
    }

    private static function hitungSelisih(Get $get, Set $set): void
    {
        $sistem = (float) ($get('qty_sistem') ?? 0);
        $fisik = (float) ($get('qty_fisik') ?? 0);
        $set('selisih', $fisik - $sistem);
    }
}
