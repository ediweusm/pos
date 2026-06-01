<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <div class="lg:col-span-7 bg-white dark:bg-gray-900 p-6 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            <div class="flex items-start gap-4 mb-6">
                <div class="p-2.5 bg-primary-50 dark:bg-primary-900/30 rounded-lg shrink-0">
                    <x-filament::icon
                        icon="heroicon-o-calendar-days"
                        class="w-6 h-6 text-primary-600 dark:text-primary-400"
                        style="width: 24px; height: 24px;"
                    />
                </div>
                <div>
                    <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Tutup Buku Bulanan
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Lakukan proses ini secara rutin di awal bulan untuk membekukan dan menyimpan riwayat saldo persediaan (Stok & HPP). Data ini wajib ada untuk pelaporan audit stok Anda.
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                <form wire:submit.prevent>
                    {{ $this->form }}
                </form>
            </div>

            <div class="mt-6">
                {{ $this->prosesTutupBukuAction }}
            </div>
        </div>

        <div class="lg:col-span-5 bg-amber-50 dark:bg-amber-500/10 p-6 rounded-xl shadow-sm ring-1 ring-amber-200 dark:ring-amber-500/20">
            <div class="flex items-start gap-4">
                <div class="p-2.5 bg-amber-100 dark:bg-amber-500/20 rounded-lg shrink-0">
                    <x-filament::icon
                        icon="heroicon-o-exclamation-triangle"
                        class="w-6 h-6 text-amber-600 dark:text-amber-500"
                        style="width: 24px; height: 24px;"
                    />
                </div>
                <div>
                    <h2 class="text-base font-semibold leading-6 text-amber-900 dark:text-amber-400">
                        Maintenance Tahunan
                    </h2>
                    <div class="text-sm text-amber-700 dark:text-amber-400/80 mt-2 space-y-3 leading-relaxed">
                        <p>
                            Tabel <code class="font-mono bg-amber-100 dark:bg-amber-500/20 px-1.5 py-0.5 rounded text-xs text-amber-800 dark:text-amber-300">jurnal_barang</code> akan membengkak seiring bertambahnya transaksi harian kasir. 
                        </p>
                        <p>
                            Gunakan fitur ini di <strong>awal tahun</strong> untuk memindahkan seluruh data transaksi tahun sebelumnya ke tabel Arsip (*Cold Storage*). Proses ini menjamin layar Kasir POS Anda tetap gesit dan ringan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8 border-t border-amber-200 dark:border-amber-500/20 pt-5 flex justify-end">
                {{ $this->arsipTahunanAction }}
            </div>
        </div>

    </div>
</x-filament-panels::page>