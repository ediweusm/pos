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

class LaporanLabaRugi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Akuntansi';
    protected static ?string $title = 'Laba Rugi (Profit & Loss)';
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.pages.laporan-laba-rugi';

    public ?string $dari_tanggal = null;
    public ?string $sampai_tanggal = null;
    public ?int $cabang_id = null;

    public function mount(): void
    {
        $this->form->fill([
            'dari_tanggal' => now()->startOfMonth()->format('Y-m-d'),
            'sampai_tanggal' => now()->endOfMonth()->format('Y-m-d'),
            'cabang_id' => null,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Grid::make(3)->schema([
                    DatePicker::make('dari_tanggal')
                        ->label('Dari Tanggal')
                        ->live()
                        ->required(),
                    DatePicker::make('sampai_tanggal')
                        ->label('Sampai Tanggal')
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
        $start = Carbon::parse($this->dari_tanggal)->startOfDay();
        $end = Carbon::parse($this->sampai_tanggal)->endOfDay();

        // Ambil rekap transaksi berdasarkan akun yang tipenya PENDAPATAN atau BEBAN
        $transaksi = DB::table('akun_trans')
            ->join('jurnal', 'jurnal.id', '=', 'akun_trans.jurnal_id')
            ->join('akun_master', 'akun_master.id', '=', 'akun_trans.akun_id')
            ->whereBetween('jurnal.tanggal', [$start, $end])
            ->whereIn('akun_master.tipe_akun', ['PENDAPATAN', 'BEBAN'])
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

        $pendapatan = [];
        $hpp = [];
        $beban = [];

        $totalPendapatan = 0;
        $totalHpp = 0;
        $totalBeban = 0;

        foreach ($transaksi as $row) {
            if ($row->tipe_akun === 'PENDAPATAN') {
                // Saldo normal Pendapatan adalah Kredit
                $saldo = $row->total_kredit - $row->total_debit;
                $pendapatan[] = ['nama' => $row->kode_akun . ' - ' . $row->nama_akun, 'saldo' => $saldo];
                $totalPendapatan += $saldo;
            } elseif ($row->tipe_akun === 'BEBAN') {
                // Saldo normal Beban adalah Debit
                $saldo = $row->total_debit - $row->total_kredit;
                
                // Pisahkan HPP (kode kepala 5) dari Beban Operasional (kode kepala 6)
                if (str_starts_with($row->kode_akun, '5')) {
                    $hpp[] = ['nama' => $row->kode_akun . ' - ' . $row->nama_akun, 'saldo' => $saldo];
                    $totalHpp += $saldo;
                } else {
                    $beban[] = ['nama' => $row->kode_akun . ' - ' . $row->nama_akun, 'saldo' => $saldo];
                    $totalBeban += $saldo;
                }
            }
        }

        $labaKotor = $totalPendapatan - $totalHpp;
        $labaBersih = $labaKotor - $totalBeban;

        return [
            'pendapatan' => $pendapatan,
            'total_pendapatan' => $totalPendapatan,
            'hpp' => $hpp,
            'total_hpp' => $totalHpp,
            'laba_kotor' => $labaKotor,
            'beban' => $beban,
            'total_beban' => $totalBeban,
            'laba_bersih' => $labaBersih,
        ];
    }
}

