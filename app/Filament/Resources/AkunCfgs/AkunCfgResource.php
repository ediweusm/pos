<?php

namespace App\Filament\Resources\AkunCfgs;

use App\Filament\Resources\AkunCfgs\Pages\CreateAkunCfg;
use App\Filament\Resources\AkunCfgs\Pages\EditAkunCfg;
use App\Filament\Resources\AkunCfgs\Pages\ListAkunCfgs;
use App\Filament\Resources\AkunCfgs\Schemas\AkunCfgForm;
use App\Filament\Resources\AkunCfgs\Tables\AkunCfgsTable;
use App\Models\AkunCfg;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AkunCfgResource extends Resource
{
    protected static ?string $model = AkunCfg::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $modelLabel = 'Konfigurasi Jurnal';
    protected static ?string $pluralModelLabel = 'Konfigurasi Jurnal';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Akuntansi';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return AkunCfgForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AkunCfgsTable::configure($table);
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
            'index' => ListAkunCfgs::route('/'),
            'create' => CreateAkunCfg::route('/create'),
            'edit' => EditAkunCfg::route('/{record}/edit'),
        ];
    }
}
