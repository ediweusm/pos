<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftKasirResource\Pages\CreateShiftKasir;
use App\Filament\Resources\ShiftKasirResource\Pages\EditShiftKasir;
use App\Filament\Resources\ShiftKasirResource\Pages\ListShiftKasirs;
use App\Filament\Resources\ShiftKasirResource\Schemas\ShiftKasirForm;
use App\Filament\Resources\ShiftKasirResource\Tables\ShiftKasirTable;
use App\Models\PosShift;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShiftKasirResource extends Resource
{
    protected static ?string $model = PosShift::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $modelLabel = 'Shift Kasir';
    protected static ?string $pluralModelLabel = 'Shift Kasir';
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 0; // Paling atas di Transaksi

    public static function form(Schema $schema): Schema
    {
        return ShiftKasirForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShiftKasirTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Kasir hanya bisa melihat data shift milik mereka sendiri
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('administrator')) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function canEdit(Model $record): bool
    {
        // Hanya bisa diedit jika status masih 'OPEN'
        return $record->status === 'OPEN';
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
            'index' => ListShiftKasirs::route('/'),
            'create' => CreateShiftKasir::route('/create'),
            'edit' => EditShiftKasir::route('/{record}/edit'),
        ];
    }
}
