<?php

namespace App\Filament\Resources\AuditKas\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\AkunMaster;
use App\Models\AkunTrans;

class AuditKasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    DatePicker::make('tanggal')
                        ->label('Tanggal Audit')
                        ->default(now())
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::updateNominalSistem($get, $set)),

                    Select::make('akun_id')
                        ->label('Akun Kas')
                        ->options(\App\Models\AkunMaster::query()->get()->mapWithKeys(fn ($a) => [$a->id => "{$a->kode_akun} - {$a->nama_akun}"])->toArray())
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required()
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::updateNominalSistem($get, $set))
                        ->columnSpan(2),
                ]),

                Grid::make(3)->schema([
                    TextInput::make('nominal_sistem')
                        ->label('Saldo Sistem')
                        ->numeric()
                        ->readOnly()
                        ->default(0),

                    TextInput::make('nominal_fisik')
                        ->label('Saldo Fisik (Uang Riil)')
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
                    ->label('Keterangan / Temuan Audit')
                    ->placeholder('Contoh: Selisih kurang karena pecahan kembalian, salah hitung kasir kemarin, dll.')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    // HELPER JURNAL DINAMIS REAKTIF
    private static function updateNominalSistem(Get $get, Set $set): void
    {
        $akunId = $get('akun_id');
        $tanggal = $get('tanggal');

        if (!$akunId || !$tanggal) {
            $set('nominal_sistem', 0);
            self::hitungSelisih($get, $set);
            return;
        }

        // Hitung Saldo Akun di Buku Besar sebelum/pada tanggal terpilih
        // Rumus: SUM(debit) - SUM(kredit)
        $saldo = AkunTrans::where('akun_id', $akunId)
            ->whereHas('jurnal', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', '<=', $tanggal);
            })
            ->selectRaw('SUM(debit) - SUM(kredit) as saldo')
            ->value('saldo') ?? 0;

        $set('nominal_sistem', (float) $saldo);
        self::hitungSelisih($get, $set);
    }

    private static function hitungSelisih(Get $get, Set $set): void
    {
        $sistem = (float) ($get('nominal_sistem') ?? 0);
        $fisik = (float) ($get('nominal_fisik') ?? 0);
        $set('selisih', $fisik - $sistem);
    }
}
