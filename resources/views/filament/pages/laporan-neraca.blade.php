<x-filament-panels::page>
    <style>
        /* Desain Cetak Khusus Laporan Neraca T-Account (Bypass Theme & Garansi Resolusi) */
        @media print {
            /* 1. SEMBUNYIKAN SEMUA ELEMEN DASHBOARD FILAMENT */
            aside, header, nav, footer, button, form,
            .fi-sidebar, 
            .fi-topbar, 
            .fi-sidebar-overlay, 
            .fi-modal-backdrop,
            .fi-actions,
            .fi-header,
            [x-cloak],
            .no-print {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
                z-index: -9999 !important;
            }

            /* 2. PAKSA BACKGROUND PUTIH TOTAL & TEKS HITAM (BYPASS DARK MODE SCREEN) */
            :root, html, body, .fi-main, .fi-main-ctn, .fi-content, main, body > div {
                background: white !important;
                background-color: white !important;
                color: #000000 !important;
                --fi-bg: #ffffff !important;
                --bg-gray-900: #ffffff !important; 
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
                border: none !important;
                position: static !important;
                transform: none !important;
            }

            /* 3. PAKSA KONTEN UTAMA & AKUN MENJADI HITAM PEKAT */
            .bg-white, .dark\:bg-gray-900, .bg-gray-900, .bg-gray-50, .dark\:bg-gray-800\/40, .bg-gray-50\/50, .dark\:bg-gray-800\/10 {
                background-color: #ffffff !important;
                background: #ffffff !important;
            }
            
            /* Paksa teks berwarna gelap agar terbaca jelas */
            .text-gray-900, .dark\:text-gray-150, .dark\:text-gray-200, .dark\:text-gray-300, 
            .dark\:text-gray-400, .text-gray-700, .dark\:text-gray-300, .text-gray-800, 
            .dark\:text-gray-250, .text-gray-600, .font-medium, .font-bold, .font-black {
                color: #000000 !important;
            }

            /* Pertahankan warna teks khusus (Hijau/Merah) dengan kontras cetak */
            .text-emerald-600, .dark\:text-emerald-400 {
                color: #047857 !important; /* Hijau zamrud gelap */
            }
            .text-red-600, .dark\:text-red-400 {
                color: #b91c1c !important; /* Merah gelap */
            }

            /* 4. PAKSA DIVIDER & BORDER UNTUK T-ACCOUNT GRID */
            .border-2, .border, .border-gray-800, .dark\:border-gray-700, .border-b-2, .border-b,
            .divide-y, .divide-gray-100, .dark\:divide-gray-800\/40, .divide-gray-200, .dark\:divide-gray-700 {
                border-color: #374151 !important; /* Abu-abu gelap agar garis tercetak tajam */
            }

            /* 5. PAKSA LAYOUT NERACA T TETAP 2 KOLOM SAAT DICETAK */
            .neraca-t-grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                width: 100% !important;
                border-width: 2px !important;
                border-style: solid !important;
                border-color: #374151 !important;
            }

            .md\:border-r-2 {
                border-right-width: 2px !important;
                border-right-style: solid !important;
                border-right-color: #374151 !important;
            }

            /* 6. ATUR MARGIN KERTAS A4 PORTRAIT */
            @page { 
                size: A4 landscape; 
                margin: 1.5cm; 
            }

            /* 7. MATIKAN BAYANGAN & FILTER */
            * { 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
                box-shadow: none !important;
                text-shadow: none !important;
                filter: none !important;
            }
        }
    </style>

    {{-- Filter Panel --}}
    <div class="bg-white dark:bg-gray-900 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 mb-4 no-print">
        <form wire:submit.prevent>
            {{ $this->form }}
        </form>
    </div>

    @php
        $data = $this->laporanData;
        $isBalanced = round($data['total_aset'], 2) == round($data['total_pasiva'], 2);
    @endphp

    {{-- Balance Alert Banner (Atas) --}}
    <div class="mb-6 no-print">
        @if($isBalanced)
            <div class="bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-400 p-4 rounded-xl border border-emerald-250 dark:border-emerald-900 flex items-center gap-3 shadow-sm">
                <x-heroicon-o-check-circle class="w-6 h-6 text-emerald-600 dark:text-emerald-400 flex-shrink-0" />
                <div>
                    <span class="font-bold text-sm">STATUS NERACA: SEIMBANG (BALANCED)</span>
                    <p class="text-xs mt-0.5">Seluruh nilai Aktiva (Aset) sama persis dengan Pasiva (Kewajiban &amp; Ekuitas). Buku besar akuntansi berada dalam kondisi seimbang.</p>
                </div>
            </div>
        @else
            <div class="bg-red-50 dark:bg-red-950/40 text-red-800 dark:text-red-400 p-4 rounded-xl border border-red-250 dark:border-red-900 flex items-center gap-3 shadow-sm">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0" />
                <div>
                    <span class="font-bold text-sm">STATUS NERACA: TIDAK SEIMBANG (UNBALANCED)</span>
                    <p class="text-xs mt-0.5">Terdapat selisih sebesar <strong>Rp {{ number_format(abs($data['total_aset'] - $data['total_pasiva']), 2, ',', '.') }}</strong> antara total Aktiva dan total Pasiva. Silakan lakukan audit transaksi pembukuan jurnal.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Detail Financial Statement --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
        {{-- Card Header --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200">Laporan Neraca (Balance Sheet)</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    Posisi Keuangan Per Tanggal: {{ \Carbon\Carbon::parse($this->per_tanggal)->translatedFormat('d F Y') }}
                </p>
            </div>
            <button onclick="window.print()" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-lg border border-gray-200 dark:border-gray-700 transition-colors flex items-center gap-1.5 no-print">
                <x-heroicon-o-printer class="w-4 h-4" />
                Cetak Laporan
            </button>
        </div>

        {{-- Statement Body (Neraca T format: Dua Kolom) --}}
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0 border-2 border-gray-800 dark:border-gray-700 rounded-xl overflow-hidden bg-white dark:bg-gray-900 neraca-t-grid">
                
                {{-- KOLOM KIRI: AKTIVA (ASET) --}}
                <div class="p-6 md:border-r-2 border-b-2 md:border-b-0 border-gray-800 dark:border-gray-700 flex flex-col justify-between min-h-[450px]">
                    <div>
                        <h4 class="text-sm font-black text-gray-900 dark:text-gray-100 uppercase tracking-wider border-b-2 border-gray-800 dark:border-gray-700 pb-2 mb-4">AKTIVA (ASET)</h4>
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-800 text-gray-400 dark:text-gray-500 uppercase tracking-wider text-[10px]">
                                    <th class="py-2 font-bold">Kode &amp; Nama Akun</th>
                                    <th class="py-2 font-bold text-right w-36">Saldo (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800/40">
                                @forelse($data['aset'] as $a)
                                    <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/10 transition-colors">
                                        <td class="py-2.5 font-medium text-gray-700 dark:text-gray-300">
                                            <span class="font-mono text-gray-400 dark:text-gray-500 mr-2">{{ $a['kode'] }}</span>
                                            {{ $a['nama'] }}
                                        </td>
                                        <td class="py-2.5 text-right font-mono text-gray-800 dark:text-gray-200 tabular-nums">
                                            {{ number_format($a['saldo'], 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-4 text-gray-450 italic">Tidak ada rincian akun aset.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 pt-4 border-t-2 border-gray-800 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40 p-3 rounded-lg">
                        <span class="font-black text-xs uppercase tracking-wider text-gray-900 dark:text-gray-100">TOTAL AKTIVA (A)</span>
                        <span class="font-black font-mono text-sm text-gray-950 dark:text-gray-50 border-b-4 border-double border-gray-900 dark:border-gray-400 pb-0.5 tabular-nums">
                            Rp {{ number_format($data['total_aset'], 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- KOLOM KANAN: PASIVA (KEWAJIBAN & EKUITAS) --}}
                <div class="p-6 flex flex-col justify-between min-h-[450px]">
                    <div>
                        <h4 class="text-sm font-black text-gray-900 dark:text-gray-100 uppercase tracking-wider border-b-2 border-gray-800 dark:border-gray-700 pb-2 mb-4">PASIVA (KEWAJIBAN &amp; EKUITAS)</h4>
                        
                        {{-- 1. KEWAJIBAN (Hutang) --}}
                        <div class="mb-6">
                            <h5 class="text-[10px] font-extrabold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-2">1. KEWAJIBAN (HUTANG JANGKA PENDEK &amp; PANJANG)</h5>
                            <table class="w-full text-left text-xs">
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800/40">
                                    @forelse($data['kewajiban'] as $k)
                                        <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/10 transition-colors">
                                            <td class="py-2.5 font-medium text-gray-700 dark:text-gray-300">
                                                <span class="font-mono text-gray-400 dark:text-gray-500 mr-2">{{ $k['kode'] }}</span>
                                                {{ $k['nama'] }}
                                            </td>
                                            <td class="py-2.5 text-right font-mono text-gray-800 dark:text-gray-200 tabular-nums w-36">
                                                {{ number_format($k['saldo'], 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-3 text-gray-450 italic">Tidak ada rincian akun kewajiban.</td>
                                        </tr>
                                    @endforelse
                                     <tr class="font-bold text-gray-900 dark:text-gray-200 bg-gray-50/10 dark:bg-gray-800/10">
                                         <td class="py-2.5 pl-2 text-gray-900 dark:text-gray-200">Subtotal Kewajiban</td>
                                         <td class="py-2.5 text-right font-mono text-gray-900 dark:text-gray-200 tabular-nums">
                                             {{ number_format($data['total_kewajiban'], 2, ',', '.') }}
                                         </td>
                                     </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- 2. EKUITAS (Modal) --}}
                        <div>
                            <h5 class="text-[10px] font-extrabold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-2">2. EKUITAS (MODAL &amp; LABA BERJALAN)</h5>
                            <table class="w-full text-left text-xs">
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800/40">
                                    @forelse($data['ekuitas'] as $e)
                                        <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/10 transition-colors">
                                            <td class="py-2.5 font-medium text-gray-700 dark:text-gray-300">
                                                @if($e['kode'] !== '3999')
                                                    <span class="font-mono text-gray-400 dark:text-gray-500 mr-2">{{ $e['kode'] }}</span>
                                                @else
                                                    <span class="font-mono text-emerald-500 dark:text-emerald-450 mr-2">VIRT</span>
                                                @endif
                                                {{ $e['nama'] }}
                                            </td>
                                            <td class="py-2.5 text-right font-mono {{ $e['kode'] === '3999' ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-gray-800 dark:text-gray-200' }} tabular-nums w-36">
                                                {{ number_format($e['saldo'], 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-3 text-gray-450 italic">Tidak ada rincian akun ekuitas.</td>
                                        </tr>
                                    @endforelse
                                     <tr class="font-bold text-gray-900 dark:text-gray-200 bg-gray-50/10 dark:bg-gray-800/10">
                                         <td class="py-2.5 pl-2 text-gray-900 dark:text-gray-200">Subtotal Ekuitas</td>
                                         <td class="py-2.5 text-right font-mono text-gray-900 dark:text-gray-200 tabular-nums">
                                             {{ number_format($data['total_ekuitas'], 2, ',', '.') }}
                                         </td>
                                     </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t-2 border-gray-800 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40 p-3 rounded-lg">
                        <span class="font-black text-xs uppercase tracking-wider text-gray-900 dark:text-gray-100">TOTAL PASIVA (B)</span>
                        <span class="font-black font-mono text-sm text-gray-950 dark:text-gray-50 border-b-4 border-double border-gray-900 dark:border-gray-400 pb-0.5 tabular-nums">
                            Rp {{ number_format($data['total_pasiva'], 2, ',', '.') }}
                        </span>
                    </div>
                </div>

            </div>

            {{-- FOOTER CATATAN DOKUMEN RESMI --}}
            <div class="pt-4 border-t border-dashed border-gray-200 dark:border-gray-800 flex flex-col md:flex-row md:justify-between md:items-center gap-1 text-[10px] text-gray-400 mt-6 no-print">
                <span>* Dokumen Laporan Neraca ini dihasilkan secara otomatis oleh sistem ERP POS terintegrasi.</span>
                <span>Waktu Cetak: {{ now()->format('d M Y H:i:s') }} &middot; Staf ID: {{ Auth::id() ?? 'System' }}</span>
            </div> 

        </div>
    </div>
</x-filament-panels::page>
