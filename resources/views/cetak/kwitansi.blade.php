<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Transaksi - {{ $transaksi->nomor_bukti }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .no-print { display: none; }
            
            /* Mengatur ukuran kertas aktual printer ke A4 Portrait */
            @page { size: A4 portrait; margin: 1cm; }
            
            .print-border { border: 2px dashed #000 !important; }
            
            /* Memaksa tinggi kwitansi tepat menjadi setengah halaman A4 */
            .kwitansi-container {
                height: 13.5cm; /* Setengah dari tinggi A4 (29.7cm) dikurangi margin */
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-start min-h-screen p-4 print:p-0">

    <div class="fixed top-5 right-5 no-print z-50">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold shadow-lg hover:bg-blue-700 transition">
            🖨️ Cetak Kwitansi
        </button>
    </div>

    <div class="bg-white w-full max-w-3xl border-2 border-gray-800 print-border rounded-xl p-6 relative shadow-2xl kwitansi-container flex flex-col justify-between">
        
        <div>
            <div class="flex justify-between items-start mb-5 border-b-2 border-gray-800 pb-3">
                <div>
                    <h1 class="text-3xl font-black tracking-widest text-gray-900 uppercase">Kwitansi</h1>
                    <p class="text-xs font-bold text-gray-500 mt-1">BUKTI MUTASI KAS & TRANSAKSI</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-gray-600 uppercase">No. Bukti</p>
                    <p class="text-lg font-mono font-bold text-gray-900">{{ $transaksi->nomor_bukti }}</p>
                    <p class="text-xs text-gray-600 mt-1">Tanggal: {{ \Carbon\Carbon::parse($transaksi->tanggal)->translatedFormat('d F Y') }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="w-40 font-bold text-gray-700 uppercase text-xs mt-1">Sudah terima dari</div>
                    <div class="font-semibold text-base text-gray-900 border-b border-gray-400 flex-1 pb-1">
                        : {{ $transaksi->akunPengirim->nama_akun }} ({{ $transaksi->akunPengirim->kode_akun }})
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-40 font-bold text-gray-700 uppercase text-xs mt-1">Uang Sejumlah</div>
                    <div class="font-mono font-black text-xl text-gray-900 border-b border-gray-400 flex-1 pb-1 bg-gray-50 px-3 italic">
                        : Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-40 font-bold text-gray-700 uppercase text-xs mt-1">Disalurkan Ke</div>
                    <div class="font-semibold text-base text-gray-900 border-b border-gray-400 flex-1 pb-1">
                        : {{ $transaksi->akunPenerima->nama_akun }} ({{ $transaksi->akunPenerima->kode_akun }})
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-40 font-bold text-gray-700 uppercase text-xs mt-1">Untuk Pembayaran</div>
                    <div class="font-semibold text-sm text-gray-900 border-b border-gray-400 flex-1 pb-1 min-h-[2.5rem]">
                        : {{ $transaksi->keterangan }}
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-end mt-4">
            <div class="border-[3px] border-gray-900 px-5 py-2 bg-gray-100 rounded-lg">
                <span class="text-lg font-black font-mono text-gray-900">Rp {{ number_format($transaksi->nominal, 0, ',', '.') }},-</span>
            </div>

            <div class="text-center w-56">
                <p class="text-xs font-bold text-gray-700 mb-12">Penerima / Petugas Kas</p>
                <div class="border-b border-gray-800 w-full mb-1"></div>
                <p class="text-[10px] text-gray-500 uppercase">( Tanda Tangan & Nama Terang )</p>
            </div>
        </div>

    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500); // Jeda setengah detik agar Tailwind selesai me-render layout
        }
    </script>
</body>
</html>