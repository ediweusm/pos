<div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border-t border-emerald-200 dark:border-emerald-900/50 flex flex-col md:flex-row justify-between items-center gap-4 transition-all">
    <div class="flex gap-6 text-sm text-gray-600 dark:text-gray-400">
        <div>Saldo Awal: <span class="font-bold text-gray-900 dark:text-gray-200">{{ $saldoAwal }}</span></div>
        <div>Total Masuk: <span class="font-bold text-emerald-600 dark:text-emerald-400">+{{ $totalMasuk }}</span></div>
        <div>Total Keluar: <span class="font-bold text-red-600 dark:text-red-400">-{{ $totalKeluar }}</span></div>
    </div>
    <div class="flex items-center gap-3">
        <div class="text-right">
            <div class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">SALDO AKHIR PERIODE</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Per tanggal {{ $formatTanggalAkhir }}</div>
        </div>
        <div class="text-2xl font-mono font-extrabold text-emerald-700 dark:text-emerald-400 tabular-nums">
            {{ $saldoAkhir }} Items
        </div>
    </div>
</div>
