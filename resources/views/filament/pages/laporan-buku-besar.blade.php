<x-filament-panels::page>
    
    <div class="bg-white dark:bg-gray-900 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 mb-2">
        <form wire:submit.prevent>
            {{ $this->form }}
        </form>
    </div>

    @if($this->akun_id && !empty($this->laporanStats))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-2">
            
            <!-- Saldo Awal -->
            <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-850 border-l-4 border-l-gray-500 shadow-sm flex flex-col justify-center transition-all hover:shadow-md">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo Awal</span>
                <span class="text-xl font-mono font-extrabold mt-1 text-gray-900 dark:text-gray-100">
                    Rp {{ number_format($this->laporanStats['saldo_awal'], 0, ',', '.') }}
                </span>
            </div>

            <!-- Total Debit -->
            <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-850 border-l-4 border-l-blue-600 shadow-sm flex flex-col justify-center transition-all hover:shadow-md">
                <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Total Debit (Masuk)</span>
                <span class="text-xl font-mono font-extrabold mt-1 text-blue-700 dark:text-blue-400">
                    Rp {{ number_format($this->laporanStats['total_debit'], 0, ',', '.') }}
                </span>
            </div>

            <!-- Total Kredit -->
            <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-850 border-l-4 border-l-red-600 shadow-sm flex flex-col justify-center transition-all hover:shadow-md">
                <span class="text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">Total Kredit (Keluar)</span>
                <span class="text-xl font-mono font-extrabold mt-1 text-red-700 dark:text-red-400">
                    Rp {{ number_format($this->laporanStats['total_kredit'], 0, ',', '.') }}
                </span>
            </div>

            <!-- Saldo Akhir -->
            <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-850 border-l-4 border-l-emerald-600 shadow-sm flex flex-col justify-center transition-all hover:shadow-md">
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Saldo Akhir</span>
                <span class="text-2xl font-mono font-black mt-1 tabular-nums {{ $this->laporanStats['saldo_akhir'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-700 dark:text-emerald-400' }}">
                    Rp {{ number_format($this->laporanStats['saldo_akhir'], 0, ',', '.') }}
                </span>
            </div>
            
        </div>
    @elseif(!$this->akun_id)
        <div class="bg-transparent border-l-4 border-l-amber-500 px-4 py-2 text-sm mb-2">
            <span class="text-amber-700 dark:text-amber-400 font-bold">💡 Petunjuk:</span>
            <span class="text-gray-600 dark:text-gray-400">Silakan pilih <strong class="text-amber-700 dark:text-amber-400">Akun Keuangan</strong> pada kolom di atas terlebih dahulu untuk memunculkan detail Buku Besar.</span>
        </div>
    @endif

    <div>
        {{ $this->table }}
    </div>

</x-filament-panels::page>
