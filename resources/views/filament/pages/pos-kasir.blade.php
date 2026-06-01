<x-filament-panels::page>
    {{--
        Keyboard Shortcuts:
        F2  → Fokus ke input barcode scanner
        F8  → Buka modal pembayaran
        ESC → Tutup modal pembayaran
        Enter (di modal) → Konfirmasi bayar
    --}}
    <div
        x-data="{
        openModal: false,
        uangBayar: 0,
        metodeBayar: 'TUNAI',

        bukaModal() {
            // Gunakan $wire untuk mengambil data real-time, BUKAN blade template
            let total = $wire.grandTotal;
            
            if (total <= 0) return;

            // Guard: TEMPO atau DELIVERY wajib ada pelanggan terpilih
            if (($wire.statusPembayaran === 'TEMPO' || $wire.tipeOrder === 'DELIVERY') && !$wire.pelangganId) {
                alert('Transaksi TEMPO atau DELIVERY wajib memilih Pelanggan terlebih dahulu!');
                return;
            }

            this.uangBayar = total;
            this.metodeBayar = 'TUNAI';
            this.openModal = true;
            $nextTick(() => {
                if ($refs.inputBayar) {
                    $refs.inputBayar.focus();
                    $refs.inputBayar.select();
                }
            });
        },

        kembalian() {
            let k = this.uangBayar - $wire.grandTotal;
            return k < 0 ? 0 : k;
        },

        formatRp(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        },

        fokusPencarian() {
            $nextTick(() => { if ($refs.inputPencarian) $refs.inputPencarian.focus(); });
        }
    }"
    x-init="fokusPencarian()"
    @fokus-input-scan.window="fokusPencarian()"
    @keydown.window.f2.prevent="fokusPencarian()"
    @keydown.window.f8.prevent="bukaModal()"
    @keydown.window.escape="openModal = false; fokusPencarian()"
    @cetak-struk.window="window.open($event.detail[0].url, '_blank')"
    class="relative"
    >
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

            {{-- ═══ KOLOM KIRI: SCANNER + TABEL KERANJANG ═══ --}}
            <div class="lg:col-span-9 space-y-3">

                {{-- Input Scanner & Pencarian Nama --}}
                <div class="relative bg-white dark:bg-gray-900 px-3 py-2.5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 flex items-center gap-3">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400 shrink-0" />
                    <input
                        type="text"
                        x-ref="inputPencarian"
                        wire:model.live.debounce.250ms="search"
                        wire:keydown.enter="cariDanTambahBarang"
                        placeholder="Scan Barcode / Ketik Nama Barang atau SKU..."
                        autocomplete="off"
                        class="flex-1 text-sm bg-transparent border-none focus:ring-0 p-0 text-gray-800 dark:text-gray-100 placeholder-gray-400"
                    >
                    <span class="text-[10px] bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-1.5 py-0.5 rounded font-mono text-gray-500 shrink-0">F2</span>

                    {{-- Dropdown Hasil Pencarian Nama (Muncul Dinamis) --}}
                    @if(!empty($searchResults))
                        <div class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl rounded-lg z-50 overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($searchResults as $index => $result)
                                <button 
                                    type="button"
                                    wire:click="pilihProduk({{ $result['id'] }})"
                                    class="w-full text-left px-4 py-2.5 text-sm flex justify-between items-center hover:bg-primary-50 dark:hover:bg-primary-950/40 transition group"
                                >
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ $result['nama'] }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            SKU: {{ $result['sku'] ?? '-' }} | Barcode: {{ $result['barcode'] ?? '-' }}
                                        </span>
                                    </div>
                                    
                                    @if($index === 0)
                                        <span class="text-[10px] bg-gray-100 dark:bg-gray-700 text-gray-400 px-1.5 py-0.5 rounded font-mono">
                                            Enter
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Tabel Keranjang --}}
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/70 border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-4 py-2.5 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Produk</th>
                                <th class="px-4 py-2.5 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide text-right w-32">Harga Satuan</th>
                                <th class="px-4 py-2.5 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide text-center w-24">Qty</th>
                                <th class="px-4 py-2.5 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide text-right w-36">Subtotal</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60">
                            @forelse($cart as $produkId => $item)
                                <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/20 transition-colors group"
                                    wire:key="cart-{{ $produkId }}">
                                    <td class="px-4 py-2.5">
                                        <div class="font-medium text-gray-900 dark:text-gray-100 leading-tight">{{ $item['nama'] }}</div>
                                        <span @class([
                                            'inline-block text-[10px] font-bold px-1.5 py-px rounded mt-0.5',
                                            'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500' => ($item['tipe_harga'] ?? 'ECERAN') === 'ECERAN',
                                            'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400' => ($item['tipe_harga'] ?? '') === 'GROSIR',
                                            'bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400' => ($item['tipe_harga'] ?? '') === 'DISTRIBUTOR',
                                            'bg-purple-50 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400' => ($item['tipe_harga'] ?? '') === 'MEMBER',
                                        ])>
                                            {{ $item['tipe_harga'] ?? 'ECERAN' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-gray-500 dark:text-gray-400 font-mono text-xs tabular-nums">
                                        {{ number_format($item['harga_jual'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <input
                                            type="number"
                                            min="1"
                                            value="{{ $item['qty'] }}"
                                            wire:change="ubahQty({{ $produkId }}, $event.target.value)"
                                            class="w-16 py-1 px-1.5 text-center text-xs border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md focus:ring-1 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                        >
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-semibold text-gray-900 dark:text-gray-100 font-mono text-sm tabular-nums">
                                        {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </td>
                                    <td class="pr-2 text-center">
                                        <button
                                            wire:click="hapusDariKeranjang({{ $produkId }})"
                                            class="p-1 rounded text-gray-200 dark:text-gray-700 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 opacity-0 group-hover:opacity-100 transition-all"
                                        >
                                            <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-14 text-center">
                                        <p class="text-sm text-gray-400">Belum ada item. Scan produk untuk memulai transaksi.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($cart) > 0)
                        <tfoot class="border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right text-xs text-gray-500 font-semibold">
                                    {{ count($cart) }} produk &middot; Total:
                                </td>
                                <td class="px-4 py-2 text-right font-bold text-gray-900 dark:text-gray-100 font-mono tabular-nums">
                                    {{ number_format($grandTotal, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- ═══ KOLOM KANAN: FORM LOGISTIK + RINGKASAN ═══ --}}
            <div class="lg:col-span-3 space-y-3">

                {{-- Form Info Pelanggan & Logistik --}}
                <div class="bg-white dark:bg-gray-900 p-3.5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 space-y-3">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 dark:border-gray-800 pb-1.5">Info Pelanggan & Logistik</h3>

                    {{-- Dropdown Pelanggan --}}
                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold text-gray-500">Pelanggan</label>
                        <select
                            wire:model.live="pelangganId"
                            class="w-full text-xs py-1.5 px-2 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md focus:ring-1 focus:ring-primary-500 focus:border-primary-500 dark:text-gray-300"
                        >
                            <option value="">— Walk-in (Umum) —</option>
                            @foreach($daftarPelanggan as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->kode_pelanggan }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Grid: Tipe Order + Status Pembayaran --}}
                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-1">
                            <label class="text-[11px] font-semibold text-gray-500">Penyerahan</label>
                            <select
                                wire:model.live="tipeOrder"
                                class="w-full text-xs py-1.5 px-2 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md focus:ring-1 focus:ring-primary-500 dark:text-gray-300"
                            >
                                <option value="TAKE_AWAY">Bawa Langsung</option>
                                <option value="DELIVERY">Kirim (Delivery)</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[11px] font-semibold text-gray-500">Pembayaran</label>
                            <select
                                wire:model.live="statusPembayaran"
                                class="w-full text-xs py-1.5 px-2 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md focus:ring-1 focus:ring-primary-500 dark:text-gray-300"
                            >
                                <option value="LUNAS">Lunas</option>
                                <option value="TEMPO">Tempo / Kredit</option>
                            </select>
                        </div>
                    </div>

                    {{-- Alamat Pengiriman (hanya saat DELIVERY) --}}
                    @if($tipeOrder === 'DELIVERY')
                        <div class="space-y-1 pt-1 border-t border-dashed border-gray-200 dark:border-gray-700">
                            <label class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">📍 Alamat Pengiriman</label>
                            <textarea
                                wire:model.live="alamatPengiriman"
                                rows="2"
                                placeholder="Alamat tujuan pengiriman..."
                                class="w-full text-xs py-1.5 px-2 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md dark:text-gray-300 resize-none focus:ring-1 focus:ring-emerald-500"
                            ></textarea>
                        </div>
                    @endif

                    {{-- Badge peringatan TEMPO tanpa pelanggan --}}
                    @if($statusPembayaran === 'TEMPO' && !$pelangganId)
                        <p class="text-[10px] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded px-2 py-1">
                            ⚠️ Pilih pelanggan untuk transaksi TEMPO
                        </p>
                    @endif
                </div>

                {{-- Total Panel --}}
                <div class="bg-gray-900 dark:bg-gray-950 p-4 rounded-lg shadow-sm text-white">
                    <div class="text-[10px] uppercase tracking-widest font-semibold text-gray-500 mb-1">Total Tagihan</div>
                    <div class="text-3xl font-bold font-mono tracking-tight text-emerald-400 tabular-nums leading-none">
                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                    </div>
                    @if($diskon > 0)
                        <div class="text-xs text-gray-500 mt-1.5">
                            Diskon: <span class="text-red-400 font-mono">-{{ number_format($diskon, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($statusPembayaran === 'TEMPO')
                        <div class="mt-2 text-[10px] font-semibold text-amber-400 bg-amber-900/30 rounded px-2 py-1">
                            📋 Akan dicatat sebagai Piutang Usaha
                        </div>
                    @endif
                </div>

                {{-- Tombol Aksi --}}
                <div class="bg-white dark:bg-gray-900 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 space-y-2">
                    <button
                        @click="bukaModal()"
                        :disabled="{{ $grandTotal }} <= 0"
                        class="w-full bg-primary-600 hover:bg-primary-500 active:bg-primary-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold py-2.5 px-4 rounded-lg text-sm flex items-center justify-center gap-2 transition-colors shadow-sm"
                    >
                        <x-heroicon-o-banknotes class="w-4 h-4" />
                        Bayar <span class="ml-auto font-mono text-[10px] bg-primary-700 px-1.5 py-0.5 rounded">F8</span>
                    </button>

                    <button
                        wire:click="bersihkanKeranjang"
                        wire:confirm="Batalkan seluruh transaksi ini?"
                        :disabled="{{ $grandTotal }} <= 0"
                        class="w-full bg-transparent hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-30 disabled:cursor-not-allowed border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 font-medium py-2 rounded-lg text-xs transition-colors"
                    >
                        Batal Transaksi
                    </button>
                </div>

                {{-- Info Shortcut --}}
                <div class="text-center text-[10px] text-gray-400 dark:text-gray-600 space-y-0.5">
                    <div><kbd class="font-mono bg-gray-100 dark:bg-gray-800 px-1 rounded">F2</kbd> Fokus scan &nbsp;|&nbsp; <kbd class="font-mono bg-gray-100 dark:bg-gray-800 px-1 rounded">F8</kbd> Bayar</div>
                    <div><kbd class="font-mono bg-gray-100 dark:bg-gray-800 px-1 rounded">ESC</kbd> Tutup modal</div>
                </div>
            </div>

        </div>{{-- End Grid --}}


        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- MODAL PEMBAYARAN (Alpine.js — zero Livewire round-trip) --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div
            x-show="openModal"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
        >
            <div
                x-show="openModal"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.away="openModal = false; fokusPencarian()"
                class="bg-white dark:bg-gray-900 w-full max-w-sm rounded-xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden"
            >
                {{-- Header --}}
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50 dark:bg-gray-800/60">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200">Pembayaran</h3>
                        <p class="text-[11px] text-gray-400">{{ count($cart) }} item</p>
                    </div>
                    <button @click="openModal = false; fokusPencarian()" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors text-lg leading-none">&times;</button>
                </div>

                {{-- Body --}}
                <div class="p-5 space-y-4">

                    {{-- Total Tagihan --}}
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</span>
                        <span class="text-2xl font-extrabold font-mono tabular-nums text-gray-900 dark:text-white">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Metode Pembayaran Selector --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                type="button"
                                @click="metodeBayar = 'TUNAI'; uangBayar = $wire.grandTotal"
                                :class="metodeBayar === 'TUNAI' ? 'bg-primary-600 text-white border-primary-600 dark:bg-primary-500 dark:border-primary-500' : 'bg-transparent text-gray-600 border-gray-200 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/40'"
                                class="py-2 px-1 text-xs font-bold border rounded-lg flex flex-col items-center gap-1 transition-colors"
                            >
                                <x-heroicon-o-banknotes class="w-4 h-4" />
                                <span>Tunai</span>
                            </button>
                            <button
                                type="button"
                                @click="metodeBayar = 'QRIS'; uangBayar = $wire.grandTotal"
                                :class="metodeBayar === 'QRIS' ? 'bg-primary-600 text-white border-primary-600 dark:bg-primary-500 dark:border-primary-500' : 'bg-transparent text-gray-600 border-gray-200 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/40'"
                                class="py-2 px-1 text-xs font-bold border rounded-lg flex flex-col items-center gap-1 transition-colors"
                            >
                                <x-heroicon-o-qr-code class="w-4 h-4" />
                                <span>QRIS</span>
                            </button>
                            <button
                                type="button"
                                @click="metodeBayar = 'TRANSFER'; uangBayar = $wire.grandTotal"
                                :class="metodeBayar === 'TRANSFER' ? 'bg-primary-600 text-white border-primary-600 dark:bg-primary-500 dark:border-primary-500' : 'bg-transparent text-gray-600 border-gray-200 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/40'"
                                class="py-2 px-1 text-xs font-bold border rounded-lg flex flex-col items-center gap-1 transition-colors"
                            >
                                <x-heroicon-o-credit-card class="w-4 h-4" />
                                <span>Transfer</span>
                            </button>
                        </div>
                    </div>

                    {{-- Input Uang Tunai (TUNAI Only) --}}
                    <div class="space-y-1.5" x-show="metodeBayar === 'TUNAI'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Uang Diterima</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm font-mono pointer-events-none">Rp</span>
                            <input
                                type="number"
                                x-ref="inputBayar"
                                x-model.number="uangBayar"
                                @keydown.enter.prevent="
                                    if (metodeBayar === 'TUNAI') {
                                        if (uangBayar >= $wire.grandTotal) {
                                            $wire.set('metodeBayar', metodeBayar);
                                            $wire.set('tunaiDiterima', uangBayar);
                                            $wire.set('kembalian', kembalian());
                                            $wire.prosesPembayaran().then(() => { openModal = false; uangBayar = 0; fokusPencarian(); });
                                        }
                                    } else {
                                        $wire.set('metodeBayar', metodeBayar);
                                        $wire.set('tunaiDiterima', $wire.grandTotal);
                                        $wire.set('kembalian', 0);
                                        $wire.prosesPembayaran().then(() => { openModal = false; uangBayar = 0; fokusPencarian(); });
                                    }
                                "
                                min="0"
                                class="w-full pl-9 pr-3 py-3 text-xl font-mono font-bold text-right border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            >
                        </div>

                        {{-- Tombol Nominal Cepat --}}
                        <div class="grid grid-cols-4 gap-1.5 mt-2">
                            @foreach([5000, 10000, 20000, 50000] as $tambah)
                                <button
                                    type="button"
                                    @click="uangBayar += {{ $tambah }}"
                                    class="py-1.5 text-[11px] font-semibold border border-gray-200 dark:border-gray-700 rounded-md hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:border-primary-300 hover:text-primary-700 dark:hover:text-primary-400 text-gray-600 dark:text-gray-400 transition-colors"
                                >
                                    +{{ number_format($tambah / 1000, 0) }}rb
                                </button>
                            @endforeach
                            @foreach([100000, 50000] as $pecahan)
                                <button
                                    type="button"
                                    @click="uangBayar = {{ $pecahan }} * Math.ceil($wire.grandTotal / {{ $pecahan }})"
                                    class="py-1.5 text-[11px] font-semibold border border-gray-200 dark:border-gray-700 rounded-md hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:border-primary-300 hover:text-primary-700 dark:hover:text-primary-400 text-gray-600 dark:text-gray-400 transition-colors"
                                >
                                    {{ number_format($pecahan / 1000, 0) }}rb↑
                                </button>
                            @endforeach
                            <button
                                type="button"
                                @click="uangBayar = $wire.grandTotal"
                                class="py-1.5 text-[11px] font-semibold border border-gray-200 dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 text-gray-600 dark:text-gray-400 transition-colors col-span-2"
                            >
                                Uang Pas
                            </button>
                        </div>
                    </div>

                    {{-- Info Pembayaran Non-Tunai (QRIS/Transfer Only) --}}
                    <div x-show="metodeBayar !== 'TUNAI'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform -translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="p-3.5 bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-800/60 rounded-lg text-blue-700 dark:text-blue-400">
                        <div class="flex items-start gap-2.5">
                            <x-heroicon-o-information-circle class="w-4 h-4 shrink-0 mt-0.5" />
                            <div class="space-y-1">
                                <p class="text-xs font-bold">Pembayaran Non-Tunai</p>
                                <p class="text-[11px] leading-normal opacity-90">
                                    Silakan selesaikan pembayaran via <span class="font-bold" x-text="metodeBayar"></span> sebesar 
                                    <span class="font-bold font-mono" x-text="'Rp ' + formatRp($wire.grandTotal)"></span>. 
                                    Uang diterima diset pas tanpa ada kembalian.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Kembalian --}}
                    <div
                        x-show="metodeBayar === 'TUNAI'"
                        class="flex items-center justify-between p-3 rounded-lg transition-colors bg-gray-50 dark:bg-gray-800/40"
                        :class="kembalian() >= 0 && uangBayar > 0 ? 'bg-emerald-50 dark:bg-emerald-950/30' : 'bg-gray-50 dark:bg-gray-800/40'"
                    >
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400"
                            :class="kembalian() >= 0 && uangBayar > 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-400'">
                            Kembalian
                        </span>
                        <span
                            class="text-xl font-extrabold font-mono tabular-nums text-gray-300 dark:text-gray-600 transition-colors"
                            :class="kembalian() > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-300 dark:text-gray-600'"
                            x-text="'Rp ' + formatRp(kembalian())"
                        ></span>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-800 flex gap-2">
                    <button
                        @click="openModal = false; fokusPencarian()"
                        class="flex-1 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Batal <span class="text-gray-400 font-mono ml-1">ESC</span>
                    </button>
                    <button
                        @click="
                            if (metodeBayar === 'TUNAI') {
                                if (uangBayar >= $wire.grandTotal) {
                                    $wire.set('metodeBayar', metodeBayar);
                                    $wire.set('tunaiDiterima', uangBayar);
                                    $wire.set('kembalian', kembalian());
                                    $wire.prosesPembayaran().then(() => { openModal = false; uangBayar = 0; fokusPencarian(); });
                                }
                            } else {
                                $wire.set('metodeBayar', metodeBayar);
                                $wire.set('tunaiDiterima', $wire.grandTotal);
                                $wire.set('kembalian', 0);
                                $wire.prosesPembayaran().then(() => { openModal = false; uangBayar = 0; fokusPencarian(); });
                            }
                        "
                        :disabled="metodeBayar === 'TUNAI' && uangBayar < $wire.grandTotal"
                        class="flex-1 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed rounded-lg flex items-center justify-center gap-1.5 shadow-sm transition-colors"
                    >
                        <x-heroicon-o-check class="w-3.5 h-3.5" />
                        Selesai <span class="font-mono text-emerald-200 ml-1">↵</span>
                    </button>
                </div>
            </div>
        </div>{{-- End Modal --}}

    </div>{{-- End Alpine Wrapper --}}
</x-filament-panels::page>