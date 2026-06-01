<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use App\Models\AkunTrans;
use App\Models\AkunMaster;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Filament\Actions\Action;

class LaporanBukuBesar extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Akuntansi';
    protected static ?string $title = 'Buku Besar';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.laporan-buku-besar';

    // State untuk menampung inputan filter
    public ?int $akun_id = null;
    public ?string $dari_tanggal = null;
    public ?string $sampai_tanggal = null;

    public function mount(): void
    {
        // Set default filter ke bulan berjalan
        $this->form->fill([
            'dari_tanggal' => now()->startOfMonth()->format('Y-m-d'),
            'sampai_tanggal' => now()->endOfMonth()->format('Y-m-d'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printPdf')
                ->label('Cetak Buku Besar')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => route('buku-besar.print', [
                    'akun_id' => $this->akun_id,
                    'dari_tanggal' => $this->dari_tanggal,
                    'sampai_tanggal' => $this->sampai_tanggal,
                ]))
                ->openUrlInNewTab()
                ->visible(fn () => !empty($this->akun_id)),

            Action::make('exportExcel')
                ->label('Ekspor Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn () => route('buku-besar.excel', [
                    'akun_id' => $this->akun_id,
                    'dari_tanggal' => $this->dari_tanggal,
                    'sampai_tanggal' => $this->sampai_tanggal,
                ]))
                ->visible(fn () => !empty($this->akun_id)),
        ];
    }

    // ─── FORM FILTER DI BAGIAN ATAS HALAMAN ───
    // ─── FORM FILTER DI BAGIAN ATAS HALAMAN ───
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Grid::make(3)->schema([
                    Select::make('akun_id')
                        ->label('Pilih Akun Keuangan')
                        ->options(\App\Models\AkunMaster::query()->get()->mapWithKeys(fn ($a) => [$a->id => "{$a->kode_akun} - {$a->nama_akun}"])->toArray()) 
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetTable()) // <--- TAMBAHKAN INI
                        ->required(),
                    
                    DatePicker::make('dari_tanggal')
                        ->label('Dari Tanggal')
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetTable()) // <--- TAMBAHKAN INI
                        ->required(),
                        
                    DatePicker::make('sampai_tanggal')
                        ->label('Sampai Tanggal')
                        ->live()
                        ->afterStateUpdated(fn () => $this->resetTable()) // <--- TAMBAHKAN INI
                        ->required(),
                ]),
            ]);
    }

    // ─── TABEL DAFTAR TRANSAKSI DI BAGIAN BAWAH ───
    public function table(Table $table): Table
    {
        return $table
            ->query(
                AkunTrans::query()
                    ->select('akun_trans.*')
                    ->join('jurnal', 'jurnal.id', '=', 'akun_trans.jurnal_id')
                    ->with('jurnal')
                    ->when($this->akun_id, fn (Builder $q) => $q->where('akun_trans.akun_id', $this->akun_id))
                    ->when($this->dari_tanggal && $this->sampai_tanggal, function (Builder $q) {
                        $q->whereBetween('jurnal.tanggal', [
                            Carbon::parse($this->dari_tanggal)->startOfDay(), 
                            Carbon::parse($this->sampai_tanggal)->endOfDay()
                        ]);
                    })
                    ->when(! $this->akun_id, fn (Builder $q) => $q->whereNull('akun_trans.id')) // Kosongkan tabel jika akun belum dipilih
            )
            ->defaultSort('jurnal.tanggal', 'asc') // Urutkan dari yang terlama ke terbaru
            ->columns([
                TextColumn::make('jurnal.tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('jurnal.nomor_jurnal')
                    ->label('No. Bukti')
                    ->searchable()
                    ->color('gray')
                    ->size('sm'),

                TextColumn::make('jurnal.keterangan')
                    ->label('Uraian / Keterangan')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('debit')
                    ->label('Debit')
                    ->numeric()
                    ->color('info')
                    ->alignRight(),

                TextColumn::make('kredit')
                    ->label('Kredit')
                    ->numeric()
                    ->color('danger')
                    ->alignRight(),
            ]);
    }

    // ─── FUNGSI KALKULASI MATEMATIKA UNTUK BANNER RINGKASAN ───
    public function getLaporanStatsProperty(): array
    {
        if (! $this->akun_id) {
            return [];
        }

        $tanggalAwal = Carbon::parse($this->dari_tanggal)->startOfDay();
        $tanggalAkhir = Carbon::parse($this->sampai_tanggal)->endOfDay();

        // 1. Hitung Saldo Awal (Semua mutasi SEBELUM tanggal filter)
        // Rumus Dasar: Total Debit Historis - Total Kredit Historis
        $saldoAwal = AkunTrans::where('akun_id', $this->akun_id)
            ->whereHas('jurnal', fn ($q) => $q->where('tanggal', '<', $tanggalAwal))
            ->selectRaw('SUM(debit) - SUM(kredit) as saldo')
            ->value('saldo') ?? 0;

        // 2. Hitung Mutasi Berjalan (Pada rentang filter terpilih)
        $mutasiBerjalan = AkunTrans::where('akun_id', $this->akun_id)
            ->whereHas('jurnal', fn ($q) => $q->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]))
            ->selectRaw('SUM(debit) as total_debit, SUM(kredit) as total_kredit')
            ->first();

        $totalDebit = $mutasiBerjalan->total_debit ?? 0;
        $totalKredit = $mutasiBerjalan->total_kredit ?? 0;

        // 3. Hitung Saldo Akhir
        $saldoAkhir = $saldoAwal + $totalDebit - $totalKredit;

        return [
            'saldo_awal' => $saldoAwal,
            'total_debit' => $totalDebit,
            'total_kredit' => $totalKredit,
            'saldo_akhir' => $saldoAkhir,
        ];
    }
}
