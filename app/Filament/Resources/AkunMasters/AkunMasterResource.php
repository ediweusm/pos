<?php

namespace App\Filament\Resources\AkunMasters;

use App\Filament\Resources\AkunMasters\Pages\CreateAkunMaster;
use App\Filament\Resources\AkunMasters\Pages\EditAkunMaster;
use App\Filament\Resources\AkunMasters\Pages\ListAkunMasters;
use App\Filament\Resources\AkunMasters\Schemas\AkunMasterForm;
use App\Filament\Resources\AkunMasters\Tables\AkunMastersTable;
use App\Models\AkunMaster;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AkunMasterResource extends Resource
{
    protected static ?string $model = AkunMaster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Bagan Akun (COA)';
    protected static ?string $pluralModelLabel = 'Bagan Akun (COA)';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Akuntansi';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return AkunMasterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AkunMastersTable::configure($table);
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
            'index' => ListAkunMasters::route('/'),
            'create' => CreateAkunMaster::route('/create'),
            'edit' => EditAkunMaster::route('/{record}/edit'),
        ];
    }
}
