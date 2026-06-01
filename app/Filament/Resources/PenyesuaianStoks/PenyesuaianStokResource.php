<?php

namespace App\Filament\Resources\PenyesuaianStoks;

use App\Filament\Resources\PenyesuaianStoks\Pages\CreatePenyesuaianStok;
use App\Filament\Resources\PenyesuaianStoks\Pages\ListPenyesuaianStoks;
use App\Filament\Resources\PenyesuaianStoks\Schemas\PenyesuaianStokForm;
use App\Filament\Resources\PenyesuaianStoks\Tables\PenyesuaianStoksTable;
use App\Models\PenyesuaianStok;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PenyesuaianStokResource extends Resource
{
    protected static ?string $model = PenyesuaianStok::class;
    
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';
    protected static ?string $modelLabel = 'Stock Opname';
    protected static ?string $pluralModelLabel = 'Data Stock Opname';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Logistik';
    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return PenyesuaianStokForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenyesuaianStoksTable::configure($table);
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
            'index' => ListPenyesuaianStoks::route('/'),
            'create' => CreatePenyesuaianStok::route('/create'),
        ];
    }
}
