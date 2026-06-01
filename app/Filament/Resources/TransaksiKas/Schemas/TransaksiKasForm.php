<?php

namespace App\Filament\Resources\TransaksiKas\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use App\Models\AkunMaster;

class TransaksiKasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    DatePicker::make('tanggal')
                        ->label('Tanggal Transaksi')
                        ->default(now())
                        ->required(),

                    TextInput::make('nomor_bukti')
                        ->label('Nomor Bukti')
                        ->readOnly()
                        ->default(fn () => 'TRK-' . date('YmdHis'))
                        ->required(),
                ]),

                Grid::make(2)->schema([
                    Select::make('akun_pengirim_id')
                        ->label('Dari Akun (Sumber Dana)')
                        ->options(AkunMaster::query()->get()->mapWithKeys(fn ($a) => [$a->id => "{$a->kode_akun} - {$a->nama_akun}"])->toArray())
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('akun_penerima_id')
                        ->label('Ke Akun (Tujuan Dana)')
                        ->options(AkunMaster::query()->get()->mapWithKeys(fn ($a) => [$a->id => "{$a->kode_akun} - {$a->nama_akun}"])->toArray())
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

                TextInput::make('nominal')
                    ->label('Nominal Transaksi')
                    ->rules(['numeric'])
                    ->prefix('Rp')
                    ->required(),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Contoh: Setor tunai ke Bank BCA, pembayaran cicilan sewa, dll.')
                    ->rows(3)
                    ->required(),
            ]);
    }
}
