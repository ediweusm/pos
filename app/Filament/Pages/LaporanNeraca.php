<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LaporanNeraca extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Akuntansi';
    protected static ?string $title = 'Neraca (Balance Sheet)';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.pages.laporan-neraca';

    public ?string $per_tanggal = null;
    public ?int $cabang_id = null;

    public function mount(): void
    {
        $this->form->fill([
            'per_tanggal' => now()->format('Y-m-d'),
            'cabang_id' => null,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Grid::make(2)->schema([
                    DatePicker::make('per_tanggal')
                        ->label('Per Tanggal')
                        ->live()
                        ->required(),
                    Select::make('cabang_id')
                        ->label('Filter Cabang (Kosongkan untuk Gabungan)')
                        ->options(\App\Models\Cabang::pluck('nama_cabang', 'id'))
                        ->searchable()
                        ->nullable()
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetTable()), // Pastikan render ulang
                ]),
            ]);
    }

    public function resetTable(): void
    {
        // Custom page does not have a standard table, so we don't need to do anything here.
        // Livewire will automatically re-render the view when the state changes.
    }

    public function getLaporanDataProperty(): array
    {
        $asOfDate = Carbon::parse($this->per_tanggal)->endOfDay();

        // 1. Hitung Laba Tahun Berjalan secara virtual (Total Pendapatan - Total Beban) up to $asOfDate
        $rekapLabaRugi = DB::table('akun_trans')
            ->join('jurnal', 'jurnal.id', '=', 'akun_trans.jurnal_id')
            ->join('akun_master', 'akun_master.id', '=', 'akun_trans.akun_id')
            ->whereDate('jurnal.tanggal', '<=', $asOfDate)
            ->whereIn('akun_master.tipe_akun', ['PENDAPATAN', 'BEBAN'])
            ->when($this->cabang_id, function ($query) {
                return $query->where('jurnal.cabang_id', $this->cabang_id);
            })
            ->select(
                'akun_master.tipe_akun',
                DB::raw('SUM(akun_trans.debit) as total_debit'),
                DB::raw('SUM(akun_trans.kredit) as total_kredit')
            )
            ->groupBy('akun_master.tipe_akun')
            ->get();

        $totalPendapatan = 0;
        $totalBeban = 0;

        foreach ($rekapLabaRugi as $row) {
            if ($row->tipe_akun === 'PENDAPATAN') {
                $totalPendapatan += ($row->total_kredit - $row->total_debit);
            } elseif ($row->tipe_akun === 'BEBAN') {
                $totalBeban += ($row->total_debit - $row->total_kredit);
            }
        }

        $labaBerjalan = $totalPendapatan - $totalBeban;

        // 2. Hitung Saldo Aset, Kewajiban, dan Ekuitas up to $asOfDate
        $transaksi = DB::table('akun_trans')
            ->join('jurnal', 'jurnal.id', '=', 'akun_trans.jurnal_id')
            ->join('akun_master', 'akun_master.id', '=', 'akun_trans.akun_id')
            ->whereDate('jurnal.tanggal', '<=', $asOfDate)
            ->whereIn('akun_master.tipe_akun', ['ASET', 'KEWAJIBAN', 'EKUITAS'])
            ->when($this->cabang_id, function ($query) {
                return $query->where('jurnal.cabang_id', $this->cabang_id);
            })
            ->select(
                'akun_master.kode_akun',
                'akun_master.nama_akun',
                'akun_master.tipe_akun',
                DB::raw('SUM(akun_trans.debit) as total_debit'),
                DB::raw('SUM(akun_trans.kredit) as total_kredit')
            )
            ->groupBy('akun_master.kode_akun', 'akun_master.nama_akun', 'akun_master.tipe_akun')
            ->orderBy('akun_master.kode_akun')
            ->get();

        $aset = [];
        $kewajiban = [];
        $ekuitas = [];

        $totalAset = 0;
        $totalKewajiban = 0;
        $totalEkuitas = 0;

        foreach ($transaksi as $row) {
            if ($row->tipe_akun === 'ASET') {
                $saldo = $row->total_debit - $row->total_kredit;
                $aset[] = [
                    'kode' => $row->kode_akun,
                    'nama' => $row->nama_akun,
                    'saldo' => $saldo
                ];
                $totalAset += $saldo;
            } elseif ($row->tipe_akun === 'KEWAJIBAN') {
                $saldo = $row->total_kredit - $row->total_debit;
                $kewajiban[] = [
                    'kode' => $row->kode_akun,
                    'nama' => $row->nama_akun,
                    'saldo' => $saldo
                ];
                $totalKewajiban += $saldo;
            } elseif ($row->tipe_akun === 'EKUITAS') {
                $saldo = $row->total_kredit - $row->total_debit;
                $ekuitas[] = [
                    'kode' => $row->kode_akun,
                    'nama' => $row->nama_akun,
                    'saldo' => $saldo
                ];
                $totalEkuitas += $saldo;
            }
        }

        // Tambahkan Laba Tahun Berjalan secara virtual ke dalam kelompok EKUITAS
        $ekuitas[] = [
            'kode' => '3999', // Kode virtual Laba Berjalan
            'nama' => 'Laba Tahun Berjalan (Earnings)',
            'saldo' => $labaBerjalan
        ];
        $totalEkuitas += $labaBerjalan;

        $totalPasiva = $totalKewajiban + $totalEkuitas;

        return [
            'aset' => $aset,
            'total_aset' => $totalAset,
            'kewajiban' => $kewajiban,
            'total_kewajiban' => $totalKewajiban,
            'ekuitas' => $ekuitas,
            'total_ekuitas' => $totalEkuitas,
            'total_pasiva' => $totalPasiva,
        ];
    }
}

