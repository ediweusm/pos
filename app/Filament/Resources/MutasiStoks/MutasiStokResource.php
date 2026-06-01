<?php

namespace App\Filament\Resources\MutasiStoks;

use App\Filament\Resources\MutasiStoks\Pages\CreateMutasiStok;
use App\Filament\Resources\MutasiStoks\Pages\EditMutasiStok;
use App\Filament\Resources\MutasiStoks\Pages\ListMutasiStoks;
use App\Filament\Resources\MutasiStoks\Schemas\MutasiStokForm;
use App\Filament\Resources\MutasiStoks\Tables\MutasiStokTable;
use App\Models\MutasiStok;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MutasiStokResource extends Resource
{
    protected static ?string $model = MutasiStok::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $modelLabel = 'Mutasi Stok';
    protected static ?string $pluralModelLabel = 'Mutasi Stok';
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return MutasiStokForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MutasiStokTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMutasiStoks::route('/'),
            'create' => CreateMutasiStok::route('/create'),
            'edit' => EditMutasiStok::route('/{record}/edit'),
        ];
    }
}
