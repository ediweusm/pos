<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use App\Models\Produk;
use App\Models\StokSaldo;
use App\Models\JurnalBarang;
use App\Models\Jurnal;
use App\Models\AkunTrans;
use App\Models\AkunCfg;
use App\Models\PosTransaksi;
use App\Models\PosTransaksiDetail;
use App\Models\PosShift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PosKasir extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $title = 'Kasir POS';
    protected static ?string $navigationLabel = 'Kasir POS';
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.pos-kasir';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:PosKasir') ?? false;
    }

    public function mount(): void
    {
        $user = Auth::user();
        if (!$user || !$user->hasOpenShift()) {
            Notification::make()
                ->title('Shift Kasir Belum Dibuka')
                ->body('Silakan Buka Shift Kasir terlebih dahulu di menu Shift.')
                ->warning()
                ->send();

            $this->redirect('/admin/shift-kasirs');
            return;
        }

        $openShift = $user->getOpenShift();
        if ($openShift) {
            $this->gudangId = $openShift->gudang_id;
        }
    }

    // ─── STATE VARIABLES ────────────────────────────────────────────────
    public string $search = '';
    public array $searchResults = [];              // DISISIPKAN: Menampung hasil live suggestions pencarian nama
    public array $cart = [];
    public float $grandTotal = 0;
    public float $diskon = 0;
    public float $tunaiDiterima = 0;
    public float $kembalian = 0;
    public string $metodeBayar = 'TUNAI';
    public bool $showPaymentModal = false;

    // ─── METADATA TRANSAKSI ───────────────────────────────────────────────
    public ?int $pelangganId = null;
    public string $tipeOrder = 'TAKE_AWAY';        // TAKE_AWAY | DELIVERY
    public string $statusPembayaran = 'LUNAS';      // LUNAS | TEMPO
    public string $alamatPengiriman = '';

    // Default: semua transaksi dari Gudang Utama (ID=1)
    // Bisa dikembangkan menjadi sesi shift kasir di masa depan
    public int $gudangId = 1;

    // ─── VIEW DATA ────────────────────────────────────────────────────────

    /**
     * Inject data ke Blade view tanpa wire:model overhead.
     * $daftarPelanggan dipakai untuk dropdown pilih pelanggan.
     */
    protected function getViewData(): array
    {
        return [
            'daftarPelanggan' => \App\Models\Pelanggan::where('is_aktif', true)
                ->orderBy('nama')
                ->get(['id', 'kode_pelanggan', 'nama', 'alamat']),
        ];
    }

    /**
     * Livewire lifecycle hook: dipanggil otomatis saat $pelangganId berubah.
     * Auto-fill alamat pengiriman jika tipe order adalah DELIVERY.
     */
    public function updatedPelangganId(?int $value): void
    {
        if ($value && $this->tipeOrder === 'DELIVERY') {
            $pelanggan = \App\Models\Pelanggan::find($value);
            $this->alamatPengiriman = $pelanggan?->alamat ?? '';
        }
    }

    /**
     * Livewire lifecycle hook: reset alamat jika beralih kembali ke TAKE_AWAY.
     */
    public function updatedTipeOrder(string $value): void
    {
        if ($value === 'TAKE_AWAY') {
            $this->alamatPengiriman = '';
        } elseif ($value === 'DELIVERY' && $this->pelangganId) {
            $pelanggan = \App\Models\Pelanggan::find($this->pelangganId);
            $this->alamatPengiriman = $pelanggan?->alamat ?? '';
        }
    }

    // ─── SCANNING & PENCARIAN HIBRIDA ────────────────────────────────────

    /**
     * DISISIPKAN: Livewire lifecycle hook untuk memantau perubahan input ketikan.
     * Otomatis dipanggil saat kasir mengetik nama barang di layar POS.
     */
    public function updatedSearch(): void
    {
        $keyword = trim($this->search);

        if (strlen($keyword) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Produk::with([
            'harga',
            'stokSaldos' => fn ($query) => $query->where('gudang_id', $this->gudangId),
        ])
            ->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', '%' . $keyword . '%')
                  ->orWhere('sku', 'like', '%' . $keyword . '%')
                  ->orWhere('barcode', 'like', '%' . $keyword . '%');
            })
            ->where('is_aktif', true)
            ->take(7) // Pembatasan limit item untuk menjaga performa rendering
            ->get(['id', 'nama', 'sku', 'barcode', 'satuan_dasar_id'])
            ->map(function ($produk) {
                return [
                    'id'              => $produk->id,
                    'nama'            => $produk->nama,
                    'sku'             => $produk->sku,
                    'barcode'         => $produk->barcode,
                    'satuan_dasar_id' => $produk->satuan_dasar_id,
                    'stok'            => (float) $produk->stokSaldos->sum('qty_sekarang'),
                ];
            })
            ->toArray();
    }

    /**
     * DISISIPKAN: Menangani aksi klik mouse kasir pada salah satu baris dropdown saran produk.
     */
    public function pilihProduk(int $id): void
    {
        $produk = Produk::with('harga')->find($id);
        if ($produk) {
            $this->tambahKeKeranjang($produk);
        }
        
        $this->search = '';
        $this->searchResults = [];
        $this->dispatch('fokus-input-scan');
    }

    /**
     * DIMODIFIKASI: Mendukung deteksi barcode absolut sekaligus fallback 
     * pemilihan item teratas hasil pencarian ketik nama saat tombol Enter ditekan.
     */
    public function cariDanTambahBarang(): void
    {
        $keyword = trim($this->search);
        if (empty($keyword)) return;

        // 1. Prioritas Utama: Cek kecocokan kode absolut (Skenario pindaian Barcode Scanner)
        $produk = Produk::with('harga')
            ->where(function ($q) use ($keyword) {
                $q->where('barcode', $keyword)
                  ->orWhere('sku', $keyword);
            })
            ->where('is_aktif', true)
            ->first();

        if ($produk) {
            $this->tambahKeKeranjang($produk);
            $this->search = '';
            $this->searchResults = [];
            $this->dispatch('fokus-input-scan');
            return;
        }

        // 2. Prioritas Kedua: Skenario jika kasir mengetik nama, lalu langsung menekan Enter 
        // tanpa klik mouse, ambil item urutan pertama dari daftar saran dropdown.
        if (count($this->searchResults) > 0) {
            $this->pilihProduk($this->searchResults[0]['id']);
            return;
        }

        // 3. Jika barang tidak ditemukan di skenario 1 maupun 2
        Notification::make()
            ->title('Barang tidak ditemukan')
            ->body("Kode atau nama \"$keyword\" tidak cocok dengan produk manapun.")
            ->danger()
            ->send();
            
        $this->search = '';
        $this->searchResults = [];
        $this->dispatch('fokus-input-scan');
    }

    private function tambahKeKeranjang(Produk $produk): void
    {
        $id       = $produk->id;
        $satuanId = $produk->satuan_dasar_id;
        $qtyBaru  = 1;

        if (isset($this->cart[$id])) {
            $qtyBaru = $this->cart[$id]['qty'] + 1;

            // Re-evaluasi tier harga untuk qty baru
            $hargaAktif = $produk->getHargaBerlaku($satuanId, $qtyBaru);
            $hargaJual  = $hargaAktif ? (float) $hargaAktif->harga : $this->cart[$id]['harga_jual'];

            $this->cart[$id]['qty']        = $qtyBaru;
            $this->cart[$id]['harga_jual'] = $hargaJual;
            $this->cart[$id]['subtotal']   = $qtyBaru * $hargaJual;
            $this->cart[$id]['tipe_harga'] = $hargaAktif ? $hargaAktif->tipe_harga : 'TIDAK ADA HARGA';
        } else {
            // Barang baru pertama kali di-scan (Qty = 1)
            $hargaAktif = $produk->getHargaBerlaku($satuanId, 1);

            if (! $hargaAktif) {
                Notification::make()
                    ->title('Harga belum diatur')
                    ->body("Produk \"{$produk->nama}\" belum memiliki data harga jual. Silakan atur di menu Produk.")
                    ->warning()
                    ->send();
                return;
            }

            $hargaJual = (float) $hargaAktif->harga;

            $this->cart[$id] = [
                'produk_id'  => $id,
                'satuan_id'  => $satuanId,
                'nama'       => $produk->nama,
                'harga_jual' => $hargaJual,
                'qty'        => 1,
                'subtotal'   => $hargaJual,
                'tipe_harga' => $hargaAktif->tipe_harga,
            ];
        }

        // Soft-Filter Warning: Cek jika kuantitas melebihi sisa stok sistem
        $stok = (float) $produk->stokSaldos()
            ->where('gudang_id', $this->gudangId)
            ->sum('qty_sekarang');
        if ($qtyBaru > $stok) {
            Notification::make()
                ->title('Peringatan: Stok Kurang')
                ->body("Kuantitas ({$qtyBaru}) melebihi stok gudang kasir untuk produk {$produk->nama}. Sisa stok: {$stok}. Checkout akan ditolak.")
                ->warning()
                ->send();
        }

        $this->hitungTotal();
    }

    /**
     * Dipanggil saat kasir mengedit langsung angka qty di tabel keranjang.
     * Otomatis re-evaluasi tier harga saat qty berubah.
     */
    public function ubahQty(int $id, float $qtyBaru): void
    {
        if ($qtyBaru <= 0) {
            unset($this->cart[$id]);
        } else {
            // Fetch dari DB menggunakan produk_id di cart (bukan array key)
            $produk   = Produk::find($this->cart[$id]['produk_id']);
            $satuanId = $this->cart[$id]['satuan_id'];

            // Re-evaluasi harga tier berdasarkan qty baru yang diketik kasir
            $hargaAktif = $produk ? $produk->getHargaBerlaku($satuanId, $qtyBaru) : null;
            $hargaJual  = $hargaAktif ? (float) $hargaAktif->harga : $this->cart[$id]['harga_jual'];

            $this->cart[$id]['qty']        = $qtyBaru;
            $this->cart[$id]['harga_jual'] = $hargaJual;
            $this->cart[$id]['subtotal']   = $qtyBaru * $hargaJual;
            $this->cart[$id]['tipe_harga'] = $hargaAktif ? $hargaAktif->tipe_harga : 'TIDAK ADA HARGA';

            // Soft-Filter Warning: Cek jika kuantitas melebihi sisa stok sistem
            if ($produk) {
                $stok = (float) $produk->stokSaldos()
                    ->where('gudang_id', $this->gudangId)
                    ->sum('qty_sekarang');
                if ($qtyBaru > $stok) {
                    Notification::make()
                        ->title('Peringatan: Stok Kurang')
                        ->body("Kuantitas ({$qtyBaru}) melebihi stok gudang kasir untuk produk {$produk->nama}. Sisa stok: {$stok}. Checkout akan ditolak.")
                        ->warning()
                        ->send();
                }
            }
        }

        $this->hitungTotal();
    }

    public function hapusDariKeranjang(int $id): void
    {
        unset($this->cart[$id]);
        $this->hitungTotal();
    }

    public function bersihkanKeranjang(): void
    {
        $this->cart              = [];
        $this->searchResults     = []; // DISISIPKAN: Ikut dibersihkan saat transaksi di-reset
        $this->grandTotal        = 0;
        $this->diskon            = 0;
        $this->tunaiDiterima     = 0;
        $this->kembalian         = 0;
        $this->showPaymentModal  = false;
        $this->pelangganId       = null;
        $this->tipeOrder         = 'TAKE_AWAY';
        $this->statusPembayaran  = 'LUNAS';
        $this->alamatPengiriman  = '';
        $this->dispatch('fokus-input-scan');
    }

    private function hitungTotal(): void
    {
        $subtotal         = array_sum(array_column($this->cart, 'subtotal'));
        $this->grandTotal = max(0, $subtotal - $this->diskon);
        $this->hitungKembalian();
    }

    public function hitungKembalian(): void
    {
        $this->kembalian = max(0, $this->tunaiDiterima - $this->grandTotal);
    }

    // ─── PAYMENT MODAL ───────────────────────────────────────────────────

    public function bukaModalPembayaran(): void
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Keranjang kosong')
                ->body('Silakan scan barang terlebih dahulu.')
                ->warning()
                ->send();
            return;
        }

        $this->tunaiDiterima = $this->grandTotal; // Default tepat bayar
        $this->kembalian     = 0;
        $this->showPaymentModal = true;
    }

    public function tutupModalPembayaran(): void
    {
        $this->showPaymentModal = false;
        $this->dispatch('fokus-input-scan');
    }

    // ─── PROSES PEMBAYARAN (CORE TRANSACTION) ────────────────────────────

    public function prosesPembayaran(): void
    {
        if (empty($this->cart)) {
            return;
        }

        try {
            $transaksi = DB::transaction(function () {
                $this->validateCheckoutInput();

                $shift = PosShift::query()
                    ->where('user_id', Auth::id())
                    ->where('status', 'OPEN')
                    ->lockForUpdate()
                    ->first();

                if (! $shift) {
                    throw ValidationException::withMessages([
                        'shift' => 'Shift kasir sudah ditutup atau tidak lagi tersedia.',
                    ]);
                }

                // Never trust hydrated Livewire cart values for final prices or stock.
                $items = $this->buildCheckoutItems();
                $subtotal = array_sum(array_column($items, 'subtotal'));
                $diskon = round((float) $this->diskon, 2);

                if ($diskon < 0 || $diskon > $subtotal) {
                    throw ValidationException::withMessages([
                        'diskon' => 'Diskon tidak valid.',
                    ]);
                }

                $grandTotal = round($subtotal - $diskon, 2);
                $isTempo = $this->statusPembayaran === 'TEMPO';
                $tunaiDiterima = $isTempo
                    ? 0
                    : ($this->metodeBayar === 'TUNAI' ? round((float) $this->tunaiDiterima, 2) : $grandTotal);

                if (! $isTempo && $this->metodeBayar === 'TUNAI' && $tunaiDiterima < $grandTotal) {
                    throw ValidationException::withMessages([
                        'tunaiDiterima' => 'Nominal tunai yang diterima tidak mencukupi.',
                    ]);
                }

                $kembalian = $this->metodeBayar === 'TUNAI'
                    ? round(max(0, $tunaiDiterima - $grandTotal), 2)
                    : 0;
                $nomorNota = 'POS-' . Str::upper((string) Str::ulid());

                // 1. BUAT HEADER TRANSAKSI
                $transaksi = PosTransaksi::create([
                    'nomor_nota'        => $nomorNota,
                    'gudang_id'         => $shift->gudang_id,
                    'kasir_id'          => Auth::id(),
                    'pos_shift_id'      => $shift->id,
                    'pelanggan_id'      => $this->pelangganId,
                    'subtotal'          => $subtotal,
                    'diskon_nominal'    => $diskon,
                    'grand_total'       => $grandTotal,
                    'tunai_diterima'    => $tunaiDiterima,
                    'kembalian'         => $kembalian,
                    'metode_bayar'      => $this->metodeBayar,
                    'tipe_order'        => $this->tipeOrder,
                    'status_pembayaran' => $this->statusPembayaran,
                    'alamat_pengiriman' => $this->tipeOrder === 'DELIVERY' ? $this->alamatPengiriman : null,
                    'status'            => 'SELESAI',
                ]);

                // The cash drawer contains only paid cash sales. QRIS, transfer,
                // and receivables are reconciled outside the physical drawer.
                if (! $isTempo && $this->metodeBayar === 'TUNAI') {
                    $shift->increment('total_penjualan', $grandTotal);
                }

                // 2. AMBIL KONFIGURASI COA BERDASARKAN METODE BAYAR
                $kodeEvent = 'POS_SALE_TUNAI'; // Default

                if ($this->statusPembayaran === 'TEMPO') {
                    $kodeEvent = 'POS_SALE_TEMPO';
                } else {
                    // Jika statusnya LUNAS, cek metode pembayarannya
                    if ($this->metodeBayar === 'QRIS') {
                        $kodeEvent = 'POS_SALE_QRIS';
                    } elseif ($this->metodeBayar === 'TRANSFER' || $this->metodeBayar === 'BANK') { 
                        // Asumsi value dropdown Anda 'TRANSFER' atau 'BANK'
                        $kodeEvent = 'POS_SALE_BANK';
                    } else {
                        $kodeEvent = 'POS_SALE_TUNAI';
                    }
                }

                $cfgPenjualan = AkunCfg::where('kode_event', $kodeEvent)->first();
                $cfgHPP       = AkunCfg::where('kode_event', 'POS_HPP')->first();

                // Validasi agar sistem tidak crash jika config belum dibuat (opsional tapi disarankan)
                if (! $cfgPenjualan || ! $cfgHPP) {
                    throw new \Exception("Konfigurasi akun untuk event '{$kodeEvent}' atau 'POS_HPP' belum diatur.");
                }

                // 3. BUAT JURNAL HEADER
                $jurnal = Jurnal::create([
                    'nomor_jurnal'   => 'JRN-POS-' . Str::upper((string) Str::ulid()),
                    'tanggal'        => now()->toDateString(),
                    'keterangan'     => 'Penjualan POS Nota: ' . $nomorNota,
                    'referensi_tipe' => PosTransaksi::class,
                    'referensi_id'   => $transaksi->id,
                    'cabang_id'      => $shift->cabang_id,
                ]);

                $totalHPP = 0;

                // 4. LOOP PER ITEM KERANJANG
                foreach ($items as $produkId => $item) {
                    $stok = StokSaldo::query()
                        ->where('gudang_id', $shift->gudang_id)
                        ->where('produk_id', $produkId)
                        ->lockForUpdate()
                        ->first();

                    if (! $stok || (float) $stok->qty_sekarang < $item['qty']) {
                        throw ValidationException::withMessages([
                            'stock' => "Stok {$item['nama']} tidak mencukupi di gudang kasir.",
                        ]);
                    }

                    $hppSatuan = (float) $stok->harga_pokok_rata_rata;
                    $hppTotal  = $hppSatuan * $item['qty'];
                    $totalHPP += $hppTotal;

                    PosTransaksiDetail::create([
                        'pos_transaksi_id'    => $transaksi->id,
                        'produk_id'           => $produkId,
                        'qty'                 => $item['qty'],
                        'harga_jual_satuan'   => $item['harga_jual'],
                        'harga_pokok_satuan'  => $hppSatuan,
                        'subtotal'            => $item['subtotal'],
                    ]);

                    $stok->decrement('qty_sekarang', $item['qty']);

                    JurnalBarang::create([
                        'produk_id'      => $produkId,
                        'gudang_id'      => $shift->gudang_id,
                        'tipe_mutasi'    => 'SALE',
                        'referensi_tipe' => PosTransaksi::class,
                        'referensi_id'   => $transaksi->id,
                        'qty_in'         => 0,
                        'qty_out'        => $item['qty'],
                        'harga_satuan'   => $item['harga_jual'],
                    ]);
                }

                // 5. JURNAL KEUANGAN
                if ($cfgPenjualan) {
                    AkunTrans::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id'   => $cfgPenjualan->akun_debit_id,
                        'debit'     => $grandTotal,
                        'kredit'    => 0,
                    ]);
                    AkunTrans::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id'   => $cfgPenjualan->akun_kredit_id,
                        'debit'     => 0,
                        'kredit'    => $grandTotal,
                    ]);
                }

                // 6. JURNAL HPP
                if ($totalHPP > 0) {
                    AkunTrans::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id'   => $cfgHPP->akun_debit_id,
                        'debit'     => $totalHPP,
                        'kredit'    => 0,
                    ]);
                    AkunTrans::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id'   => $cfgHPP->akun_kredit_id,
                        'debit'     => 0,
                        'kredit'    => $totalHPP,
                    ]);
                }

                return $transaksi;
            });

            // SUKSES: Reset state & notifikasi
            $kembalian = (float) $transaksi->kembalian;
            $idTransaksiBaru = $transaksi->id;
            $this->bersihkanKeranjang();

            Notification::make()
                ->title('✅ Transaksi Berhasil!')
                ->body(
                    $kembalian > 0
                        ? 'Kembalian: Rp ' . number_format($kembalian, 0, ',', '.')
                        : 'Pembayaran tepat. Terima kasih!'
                )
                ->success()
                ->duration(5000)
                ->send();

            // DISPATCH EVENT KE BROWSER UNTUK BUKA TAB BARU CETAK STRUK
            $this->dispatch('cetak-struk', ['url' => route('cetak.struk-pos', $idTransaksiBaru)]);

        } catch (ValidationException $e) {
            Notification::make()
                ->title('Transaksi Ditolak')
                ->body(collect($e->errors())->flatten()->first())
                ->warning()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Transaksi Gagal')
                ->body('Terjadi kesalahan sistem: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function validateCheckoutInput(): void
    {
        Validator::make([
            'metode_bayar' => $this->metodeBayar,
            'status_pembayaran' => $this->statusPembayaran,
            'tipe_order' => $this->tipeOrder,
            'pelanggan_id' => $this->pelangganId,
            'alamat_pengiriman' => $this->alamatPengiriman,
        ], [
            'metode_bayar' => ['required', 'in:TUNAI,QRIS,TRANSFER'],
            'status_pembayaran' => ['required', 'in:LUNAS,TEMPO'],
            'tipe_order' => ['required', 'in:TAKE_AWAY,DELIVERY'],
            'pelanggan_id' => ['nullable', 'integer', 'exists:pelanggan,id'],
            'alamat_pengiriman' => ['nullable', 'string', 'max:2000'],
        ])->after(function ($validator) {
            if ($this->statusPembayaran === 'TEMPO' && ! $this->pelangganId) {
                $validator->errors()->add('pelanggan_id', 'Pelanggan wajib dipilih untuk transaksi tempo.');
            }

            if ($this->tipeOrder === 'DELIVERY' && blank(trim($this->alamatPengiriman))) {
                $validator->errors()->add('alamat_pengiriman', 'Alamat pengiriman wajib diisi untuk delivery.');
            }
        })->validate();
    }

    /**
     * Rebuild server-side transaction lines to prevent client-side price and
     * product state tampering. Quantity remains in the product base unit.
     *
     * @return array<int, array{nama: string, qty: float, harga_jual: float, subtotal: float}>
     */
    private function buildCheckoutItems(): array
    {
        $items = [];

        foreach ($this->cart as $productId => $cartItem) {
            $qty = round((float) ($cartItem['qty'] ?? 0), 2);
            if ($qty <= 0 || $qty > 100000) {
                throw ValidationException::withMessages([
                    'cart' => 'Kuantitas produk harus lebih dari nol dan dalam batas yang diizinkan.',
                ]);
            }

            $produk = Produk::query()
                ->whereKey((int) $productId)
                ->where('is_aktif', true)
                ->first();

            if (! $produk) {
                throw ValidationException::withMessages([
                    'cart' => 'Salah satu produk tidak lagi aktif atau tidak ditemukan.',
                ]);
            }

            $harga = $produk->getHargaBerlaku($produk->satuan_dasar_id, $qty);
            if (! $harga) {
                throw ValidationException::withMessages([
                    'cart' => "Harga untuk produk {$produk->nama} belum diatur.",
                ]);
            }

            $hargaJual = round((float) $harga->harga, 2);
            $items[$produk->id] = [
                'nama' => $produk->nama,
                'qty' => $qty,
                'harga_jual' => $hargaJual,
                'subtotal' => round($qty * $hargaJual, 2),
            ];
        }

        if ($items === []) {
            throw ValidationException::withMessages(['cart' => 'Keranjang kosong.']);
        }

        return $items;
    }

    // ─── HELPERS ──────────────────────────────────────────────────────────

    public function formatRupiah(float $nominal): string
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }
}
