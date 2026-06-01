<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\StokSaldo;
use App\Models\StokPeriode;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TutupBuku extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lock-closed';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Akuntansi';
    protected static ?string $title = 'Tutup Buku Bulanan';
    protected string $view = 'filament.pages.tutup-buku';
    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // Set default ke bulan lalu
            'bulan_proses' => Carbon::now()->subMonth()->format('Y-m'), 
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                DatePicker::make('bulan_proses')
                    ->label('Periode Bulan (Pilih Tanggal Berapa Saja di Bulan Tersebut)')
                    ->displayFormat('F Y')
                    ->format('Y-m')
                    ->required()
                    ->helperText('Perhatian: Tutup buku akan membekukan snapshot stok pada akhir bulan yang dipilih.'),
            ])
            ->statePath('data');
    }

    public function prosesTutupBukuAction(): Action
    {
        return Action::make('prosesTutupBuku')
            ->label('Proses Tutup Buku Sekarang')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Tutup Buku')
            ->modalDescription(function () {
                $bulanProses = $this->data['bulan_proses'] ?? null;
                if (!$bulanProses) {
                    return 'Tindakan ini akan mengalkulasi dan menyimpan saldo akhir stok barang.';
                }

                $periode = Carbon::parse($bulanProses)->format('Y-m');
                $sudahAda = StokPeriode::where('periode', $periode)->exists();

                if ($sudahAda) {
                    return "⚠️ Peringatan: Data tutup buku untuk periode {$periode} SUDAH ADA! Apakah Anda ingin MENIMPA data lama dengan perhitungan baru atau MEMBATALKAN proses?";
                }

                return 'Tindakan ini akan mengalkulasi dan menyimpan saldo akhir stok barang. Pastikan seluruh transaksi pada bulan tersebut sudah selesai diinput.';
            })
            ->modalSubmitActionLabel(function () {
                $bulanProses = $this->data['bulan_proses'] ?? null;
                if (!$bulanProses) return 'Ya, Kunci Saldo';

                $periode = Carbon::parse($bulanProses)->format('Y-m');
                $sudahAda = StokPeriode::where('periode', $periode)->exists();

                return $sudahAda ? 'Ya, Timpa Data Lama' : 'Ya, Kunci Saldo';
            })
            ->modalCancelAction(function ($action) {
                $bulanProses = $this->data['bulan_proses'] ?? null;
                if (!$bulanProses) return $action->label('Batal');

                $periode = Carbon::parse($bulanProses)->format('Y-m');
                $sudahAda = StokPeriode::where('periode', $periode)->exists();

                if ($sudahAda) {
                    return $action
                        ->label('Batalkan & Ke Dashboard')
                        ->color('secondary')
                        ->url('/admin'); // Redirect ke dashboard jika dibatalkan
                }

                return $action->label('Batal');
            })
            ->color('danger')
            ->action(fn () => $this->eksekusiTutupBuku());
    }

    private function eksekusiTutupBuku(): void
    {
        $periode = Carbon::parse($this->data['bulan_proses'])->format('Y-m');

        try {
            DB::transaction(function () use ($periode) {
                // 1. Ambil data stok fisik saat ini (Tarik dari tabel stok_saldo)
                $semuaStok = StokSaldo::all();

                if ($semuaStok->isEmpty()) {
                    return;
                }

                $dataSnapshot = [];
                $waktuSekarang = now();

                foreach ($semuaStok as $stok) {
                    $dataSnapshot[] = [
                        'periode'    => $periode,
                        'gudang_id'  => $stok->gudang_id,
                        'produk_id'  => $stok->produk_id,
                        'qty_akhir'  => $stok->qty_sekarang,
                        'hpp_akhir'  => $stok->harga_pokok_rata_rata,
                        'created_at' => $waktuSekarang,
                        'updated_at' => $waktuSekarang,
                    ];
                }

                // 2. Eksekusi Upsert (Insert atau Update jika sudah pernah ditutup)
                StokPeriode::upsert(
                    $dataSnapshot,
                    ['periode', 'gudang_id', 'produk_id'], // Kunci pencarian unik
                    ['qty_akhir', 'hpp_akhir', 'updated_at'] // Kolom yang diupdate jika data sudah ada
                );
            });

            Notification::make()
                ->title('Tutup Buku Berhasil')
                ->body("Saldo akhir untuk periode {$periode} berhasil dikunci dan disimpan.")
                ->success()
                ->send();

            // Redirect ke Dashboard setelah sukses
            $this->redirect('/admin');

        } catch (\Throwable $e) {
            Notification::make()
                ->title('Tutup Buku Gagal')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    // FUNGSI AKSI UNTUK TOMBOL ARSIP
    public function arsipTahunanAction(): Action
    {
        return Action::make('arsipTahunan')
            ->label('Arsipkan Data Tahun Lalu')
            ->requiresConfirmation()
            ->modalHeading('Peringatan Pengarsipan')
            ->modalDescription('Proses ini akan memindahkan SEMUA riwayat transaksi (Kartu Stok) sebelum tanggal 1 Januari tahun ini ke tabel Arsip. Pastikan seluruh laporan tahun lalu sudah final. Lanjutkan?')
            ->modalSubmitActionLabel('Ya, Arsipkan Sekarang')
            ->color('warning')
            ->icon('heroicon-o-archive-box')
            ->action(fn () => $this->eksekusiArsipTahunan());
    }

    // LOGIKA PEMINDAHAN (HOT TO COLD STORAGE)
    private function eksekusiArsipTahunan(): void
    {
        // Tetapkan batas waktu: 1 Januari jam 00:00:00 pada tahun berjalan
        $batasWaktu = now()->startOfYear();

        try {
            DB::transaction(function () use ($batasWaktu) {
                // 1. Cek apakah ada data yang perlu dipindah
                $jumlahData = DB::table('jurnal_barang')->where('created_at', '<', $batasWaktu)->count();
                
                if ($jumlahData === 0) {
                    throw new \Exception('Sistem sudah bersih. Tidak ada data tahun lalu yang perlu diarsipkan.');
                }

                // 2. Salin massal ke tabel history
                DB::statement("
                    INSERT INTO jurnal_barang_history 
                    (produk_id, gudang_id, tipe_mutasi, referensi_tipe, referensi_id, qty_in, qty_out, harga_satuan, created_at, updated_at)
                    SELECT 
                    produk_id, gudang_id, tipe_mutasi, referensi_tipe, referensi_id, qty_in, qty_out, harga_satuan, created_at, updated_at
                    FROM jurnal_barang
                    WHERE created_at < ?
                ", [$batasWaktu]);

                // 3. Hapus bersih data asli untuk meringankan tabel aktif
                DB::table('jurnal_barang')->where('created_at', '<', $batasWaktu)->delete();
            });

            Notification::make()
                ->title('Arsip Selesai')
                ->body('Ribuan data mutasi tahun lalu berhasil dipindahkan. Performa database kembali maksimal.')
                ->success()
                ->send();

            // Redirect ke Dashboard setelah sukses
            $this->redirect('/admin');

        } catch (\Throwable $e) {
            Notification::make()
                ->title('Info Sistem')
                ->body($e->getMessage())
                ->warning()
                ->send();
        }
    }
}
