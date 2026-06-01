<?php

namespace App\Filament\Resources\JurnalBarangs;
use App\Filament\Resources\JurnalBarangs\Pages\ListJurnalBarangs;
use App\Filament\Resources\JurnalBarangs\Tables\JurnalBarangsTable;
use App\Models\JurnalBarang;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Filament\Tables\Contracts\HasTable;

class JurnalBarangResource extends Resource
{
    protected static ?string $model = JurnalBarang::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $modelLabel = 'Kartu Stok (Logistik)';
    protected static ?string $pluralModelLabel = 'Kartu Stok';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Logistik';
    protected static ?int $navigationSort = 5;

    // ─── KUNCI KEAMANAN: JADIKAN READ-ONLY ───
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        // Read-only resource, tidak perlu form schema
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return JurnalBarangsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJurnalBarangs::route('/'),
        ];
    }
}
