<?php

namespace App\Filament\Resources\TransaksiKas;

use App\Filament\Resources\TransaksiKas\Pages\CreateTransaksiKas;
use App\Filament\Resources\TransaksiKas\Pages\ListTransaksiKas;
use App\Filament\Resources\TransaksiKas\Schemas\TransaksiKasForm;
use App\Filament\Resources\TransaksiKas\Tables\TransaksiKasTable;
use App\Models\TransaksiKas;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TransaksiKasResource extends Resource
{
    protected static ?string $model = TransaksiKas::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $modelLabel = 'Transaksi Kas';
    protected static ?string $pluralModelLabel = 'Transaksi Kas';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Akuntansi';
    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return TransaksiKasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransaksiKasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransaksiKas::route('/'),
            'create' => CreateTransaksiKas::route('/create'),
        ];
    }
}
