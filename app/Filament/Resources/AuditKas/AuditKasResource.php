<?php

namespace App\Filament\Resources\AuditKas;

use App\Filament\Resources\AuditKas\Pages\CreateAuditKas;
use App\Filament\Resources\AuditKas\Pages\ListAuditKas;
use App\Filament\Resources\AuditKas\Schemas\AuditKasForm;
use App\Filament\Resources\AuditKas\Tables\AuditKasTable;
use App\Models\AuditKas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AuditKasResource extends Resource
{
    protected static ?string $model = AuditKas::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $modelLabel = 'Audit Kas';
    protected static ?string $pluralModelLabel = 'Data Audit Kas';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Akuntansi';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return AuditKasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditKasTable::configure($table);
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
            'index' => ListAuditKas::route('/'),
            'create' => CreateAuditKas::route('/create'),
        ];
    }
}
