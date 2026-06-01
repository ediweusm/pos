<x-filament-panels::page>
    <style>
        /* Desain Cetak Khusus Laporan Laba Rugi (Bypass Filament Theme & Desktop Override) */
        @media print {
            /* KUNCI: paksa mode desktop agar grid 4 kolom bertahan dan overlay tidak muncul */
            html, body {
                min-width: 1024px !important;
            }

            /* SEMBUNYIKAN UI ELEMEN FILAMENT & OVERLAY */
            aside, header, nav, footer, button, form, .fi-sidebar, .fi-topbar, .fi-actions, .no-print, .fi-sidebar-overlay, .fi-modal-backdrop {
                display: none !important;
            }

            /* HAPUS SHADOW & FILTER SECARA GLOBAL SAAT CETAK */
            * {
                box-shadow: none !important;
                text-shadow: none !important;
                filter: none !important;
            }

            /* ATUR KERTAS A4 PORTRAIT */
            @page {
                size: A4 portrait;
                margin: 0.8cm;
            }

            /* HILANGKAN BORDER & PADDING CARD UTAMA SAAT PRINT UNTUK MEMAKSIMALKAN LEBAR */
            .rounded-xl, .shadow-sm {
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            div.rounded-xl.border {
                border: none !important;
            }
            .p-8 {
                padding: 1rem 0 !important; /* Kurangi padding kiri-kanan tabel agar lebih lebar */
            }

            /* KUNCI UTAMA: Paksa root dan container cetak utama menjadi putih bersih */
            html, body, .fi-main, .fi-main-ctn, .fi-content, main, body > div {
                background-color: #ffffff !important;
                background: #ffffff !important;
            }

            /* PAKSA CARD CONTAINER UTAMA MENJADI PUTIH BERSIH SAAT CETAK (BYPASS DARK MODE) */
            div.bg-white, div.dark\:bg-gray-900 {
                background-color: #ffffff !important;
                background: #ffffff !important;
                border: none !important;
            }

            /* PAKSA ELEMEN DI DALAM LAPORAN MENJADI TRANSPARAN AGAR TIDAK SALING MENUTUPI */
            div, table, thead, tbody, tr, th, td, span, h3, p {
                background-color: transparent !important;
                background: transparent !important;
            }

            /* Paksa teks berwarna gelap agar terbaca jelas secara default */
            h3, p, span, th, td, tr, div, table {
                color: #000000 !important;
            }

            /* Pertahankan warna teks khusus (Hijau/Amber/Merah) dengan kontras tinggi */
            .text-emerald-600, .dark\:text-emerald-400 {
                color: #047857 !important; /* Hijau zamrud gelap */
            }
            .text-amber-600, .dark\:text-amber-400, .text-orange-600, .dark\:text-orange-400 {
                color: #d97706 !important; /* Amber gelap */
            }
            .text-red-600, .dark\:text-red-400 {
                color: #b91c1c !important; /* Merah gelap */
            }

            /* Garis Tabel Tajam */
            .border-2, .border, .border-gray-300, .dark\:border-gray-700, .border-b-2, .border-b,
            .divide-y, .divide-gray-100, .dark\:divide-gray-800\/40, .divide-gray-200, .dark\:divide-gray-700 {
                border-color: #374151 !important; /* Abu-abu gelap agar garis tercetak tajam */
            }

            /* Memaksa engine cetak memproses rendering dengan benar */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    <script>
        let isDarkBeforePrint = false;

        window.addEventListener('beforeprint', () => {
            if (document.documentElement.classList.contains('dark')) {
                isDarkBeforePrint = true;
                document.documentElement.classList.remove('dark');
            }
        });

        window.addEventListener('afterprint', () => {
            if (isDarkBeforePrint) {
                document.documentElement.classList.add('dark');
                isDarkBeforePrint = false;
            }
        });
    </script>

    <div class="bg-white dark:bg-gray-900 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 mb-4 no-print">
        <form wire:submit.prevent>
            {{ $this->form }}
        </form>
    </div>

    @php
        $data = $this->laporanData;
    @endphp

    {{-- Detail Financial Statement --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
        {{-- Card Header --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-center justify-between">
            <div>
                <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-gray-100">Laporan Laba Rugi</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Periode: {{ \Carbon\Carbon::parse($this->dari_tanggal)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($this->sampai_tanggal)->format('d M Y') }}
                </p>
            </div>
            <button onclick="window.print()" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-lg border border-gray-200 dark:border-gray-700 transition-colors flex items-center gap-1.5 no-print">
                <x-heroicon-o-printer class="w-4 h-4" />
                Cetak Laporan
            </button>
        </div>

        {{-- Statement Body (Formal Table Format) --}}
        <div class="p-8 overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse border border-gray-300 dark:border-gray-700">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700">
                        <th class="px-4 py-3 font-extrabold uppercase tracking-wider text-gray-900 dark:text-gray-100 text-xs border-r border-gray-300 dark:border-gray-700">Deskripsi Akun / Transaksi</th>
                        <th class="px-4 py-3 font-extrabold uppercase tracking-wider text-gray-900 dark:text-gray-100 text-xs text-right w-48 border-r border-gray-300 dark:border-gray-700">Nilai Transaksi</th>
                        <th class="px-4 py-3 font-extrabold uppercase tracking-wider text-gray-900 dark:text-gray-100 text-xs text-right w-48">Total Kelompok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    
                    {{-- 1. PENDAPATAN --}}
                    <tr class="bg-gray-50 dark:bg-gray-800/60 font-black text-green-950 dark:text-green-50 border-b border-gray-350 dark:border-gray-650">
                        <td class="py-3 px-4 text-xs uppercase tracking-wide border-r border-gray-200 dark:border-gray-700" colspan="3">1. PENDAPATAN</td>
                    </tr>
                    @forelse($data['pendapatan'] as $p)
                        <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/10 transition-colors">
                            <td class="py-2.5 px-4 pl-8 text-gray-600 dark:text-gray-400 font-medium border-r border-gray-200 dark:border-gray-800">{{ $p['nama'] }}</td>
                            <td class="py-2.5 px-4 text-right font-mono text-gray-800 dark:text-gray-200 tabular-nums border-r border-gray-200 dark:border-gray-800">Rp {{ number_format($p['saldo'], 2, ',', '.') }}</td>
                            <td class="py-2.5 px-4"></td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-2.5 px-4 pl-8 text-gray-450 italic border-r border-gray-200 dark:border-gray-800" colspan="2">Tidak ada rincian akun pendapatan.</td>
                            <td class="py-2.5 px-4"></td>
                        </tr>
                    @endforelse
                    <tr class="font-bold bg-gray-50/30 dark:bg-gray-800/20 text-emerald-600 dark:text-emerald-400">
                        <td class="py-3 px-4 pl-8 border-r border-gray-200 dark:border-gray-800">Total Pendapatan</td>
                        <td class="py-3 px-4 border-r border-gray-200 dark:border-gray-800"></td>
                        <td class="py-3 px-4 text-right font-mono tabular-nums font-extrabold text-sm">Rp {{ number_format($data['total_pendapatan'], 2, ',', '.') }}</td>
                    </tr>

                    {{-- 2. HARGA POKOK PENJUALAN (HPP) --}}
                    <tr class="bg-gray-50 dark:bg-gray-800/60 font-black text-green-950 dark:text-red-50 border-b border-gray-350 dark:border-gray-650">
                        <td class="py-3 px-4 text-xs uppercase tracking-wide border-r border-gray-200 dark:border-gray-700" colspan="3">2. HARGA POKOK PENJUALAN (HPP)</td>
                    </tr>
                    @forelse($data['hpp'] as $h)
                        <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/10 transition-colors">
                            <td class="py-2.5 px-4 pl-8 text-gray-600 dark:text-gray-400 font-medium border-r border-gray-200 dark:border-gray-800">{{ $h['nama'] }}</td>
                            <td class="py-2.5 px-4 text-right font-mono text-gray-800 dark:text-gray-200 tabular-nums border-r border-gray-200 dark:border-gray-800">Rp {{ number_format($h['saldo'], 2, ',', '.') }}</td>
                            <td class="py-2.5 px-4"></td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-2.5 px-4 pl-8 text-gray-450 italic border-r border-gray-200 dark:border-gray-800" colspan="2">Tidak ada rincian akun HPP.</td>
                            <td class="py-2.5 px-4"></td>
                        </tr>
                    @endforelse
                    <tr class="font-bold bg-gray-50/30 dark:bg-gray-800/20 text-amber-600 dark:text-amber-400">
                        <td class="py-3 px-4 pl-8 border-r border-gray-200 dark:border-gray-800">Total Harga Pokok Penjualan (HPP)</td>
                        <td class="py-3 px-4 border-r border-gray-200 dark:border-gray-800"></td>
                        <td class="py-3 px-4 text-right font-mono tabular-nums font-extrabold text-sm">Rp {{ number_format($data['total_hpp'], 2, ',', '.') }}</td>
                    </tr>

                    {{-- LABA KOTOR --}}
                    <tr class="bg-sky-50 dark:bg-sky-950/20 font-black text-sky-850 dark:text-sky-300 text-xs border-b border-gray-300 dark:border-gray-700">
                        <td class="py-3.5 px-4 uppercase tracking-wider border-r border-gray-250 dark:border-gray-750">LABA KOTOR (Gross Profit)</td>
                        <td class="py-3.5 px-4 border-r border-gray-250 dark:border-gray-750"></td>
                        <td class="py-3.5 px-4 text-right font-mono text-sky-700 dark:text-sky-400 tabular-nums text-sm font-extrabold">Rp {{ number_format($data['laba_kotor'], 2, ',', '.') }}</td>
                    </tr>

                    {{-- 3. BEBAN OPERASIONAL --}}
                    <tr class="bg-gray-50 dark:bg-gray-800/60 font-black text-green-950 dark:text-gray-50 border-b border-gray-350 dark:border-gray-650">
                        <td class="py-3 px-4 text-xs uppercase tracking-wide border-r border-gray-200 dark:border-gray-700" colspan="3">3. BEBAN OPERASIONAL &amp; LAIN-LAIN</td>
                    </tr>
                    @forelse($data['beban'] as $b)
                        <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/10 transition-colors">
                            <td class="py-2.5 px-4 pl-8 text-gray-600 dark:text-gray-400 font-medium border-r border-gray-200 dark:border-gray-800">{{ $b['nama'] }}</td>
                            <td class="py-2.5 px-4 text-right font-mono text-gray-800 dark:text-gray-200 tabular-nums border-r border-gray-200 dark:border-gray-800">Rp {{ number_format($b['saldo'], 2, ',', '.') }}</td>
                            <td class="py-2.5 px-4"></td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-2.5 px-4 pl-8 text-gray-450 italic border-r border-gray-200 dark:border-gray-800" colspan="2">Tidak ada rincian akun beban.</td>
                            <td class="py-2.5 px-4"></td>
                        </tr>
                    @endforelse
                    <tr class="font-bold bg-gray-50/30 dark:bg-gray-800/20 text-amber-600 dark:text-amber-400">
                        <td class="py-3 px-4 pl-8 border-r border-gray-200 dark:border-gray-800">Total Beban Operasional</td>
                        <td class="py-3 px-4 border-r border-gray-200 dark:border-gray-800"></td>
                        <td class="py-3 px-4 text-right font-mono tabular-nums font-extrabold text-sm">Rp {{ number_format($data['total_beban'], 2, ',', '.') }}</td>
                    </tr>

                    {{-- LABA BERSIH (Accounting Double-Underline Convention) --}}
                    <tr class="{{ $data['laba_bersih'] >= 0 ? 'bg-emerald-50/30 dark:bg-emerald-950/20 text-emerald-850 dark:text-emerald-300' : 'bg-red-50/30 dark:bg-red-950/20 text-red-850 dark:text-red-300' }} font-black text-xs">
                        <td class="py-4 px-4 text-xs uppercase tracking-wider border-r {{ $data['laba_bersih'] >= 0 ? 'border-emerald-200 dark:border-emerald-800' : 'border-red-200 dark:border-red-800' }}">
                            {{ $data['laba_bersih'] >= 0 ? 'LABA BERSIH (Net Profit)' : 'RUGI BERSIH (Net Loss)' }}
                        </td>
                        <td class="py-4 px-4 border-r {{ $data['laba_bersih'] >= 0 ? 'border-emerald-200 dark:border-emerald-800' : 'border-red-200 dark:border-red-800' }}"></td>
                        <td class="py-4 px-4 text-right font-mono {{ $data['laba_bersih'] >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400' }} text-base border-b-4 border-double {{ $data['laba_bersih'] >= 0 ? 'border-emerald-600' : 'border-red-600' }} pb-0.5 tabular-nums font-extrabold">
                            Rp {{ number_format($data['laba_bersih'], 2, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- FOOTER CATATAN DOKUMEN RESMI --}}
            <div class="pt-2 border-t border-dashed border-gray-200 dark:border-gray-800 flex flex-col md:flex-row md:justify-between md:items-center gap-1 text-[10px] text-gray-400 mt-2">
                <span>Waktu Cetak: {{ now()->format('d M Y H:i:s') }} - Kasir ID: {{ Auth::id() ?? 'System' }}</span>
            </div> 

        </div>
    </div>
</x-filament-panels::page>
