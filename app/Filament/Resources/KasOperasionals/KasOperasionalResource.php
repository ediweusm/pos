<?php

namespace App\Filament\Resources\KasOperasionals;

use App\Filament\Resources\KasOperasionals\Pages\CreateKasOperasional;
use App\Filament\Resources\KasOperasionals\Pages\EditKasOperasional;
use App\Filament\Resources\KasOperasionals\Pages\ListKasOperasionals;
use App\Filament\Resources\KasOperasionals\Schemas\KasOperasionalForm;
use App\Filament\Resources\KasOperasionals\Tables\KasOperasionalTable;
use App\Models\KasOperasional;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class KasOperasionalResource extends Resource
{
    protected static ?string $model = KasOperasional::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $modelLabel = 'Kas Operasional';
    protected static ?string $pluralModelLabel = 'Kas Operasional';
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return KasOperasionalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KasOperasionalTable::configure($table);
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
            'index' => ListKasOperasionals::route('/'),
            'create' => CreateKasOperasional::route('/create'),
            'edit' => EditKasOperasional::route('/{record}/edit'),
        ];
    }
}
