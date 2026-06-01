<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk POS - {{ $transaksi->nomor_nota }}</title>
    <style>
        @page {
            margin: 0; /* Menghilangkan margin bawaan browser */
        }
        body {
            font-family: 'Courier New', Courier, monospace; /* Font standar struk */
            font-size: 12px;
            color: #000;
            width: 58mm; /* Sesuaikan dengan ukuran printer Anda, bisa diubah ke 80mm */
            margin: 0 auto;
            padding: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .w-full { width: 100%; }
        .border-top { border-top: 1px dashed #000; }
        .border-bottom { border-bottom: 1px dashed #000; }
        .mb-1 { margin-bottom: 5px; }
        .mt-1 { margin-top: 5px; }
        .py-1 { padding-top: 5px; padding-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        
        /* Menyembunyikan elemen tertentu saat dicetak (opsional) */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">

    <div class="text-center mb-1">
        <h2 style="margin: 0; font-size: 16px;">TOKO PAKAN TERNAK</h2>
        <p style="margin: 2px 0;">Jl. Raya Solo - Semarang No. 123</p>
        <p style="margin: 0;">Telp: 0812-3456-7890</p>
    </div>

    <div class="border-top border-bottom py-1 mb-1 mt-1">
        <table class="w-full">
            <tr>
                <td class="text-left">Nota:</td>
                <td class="text-right">{{ $transaksi->nomor_nota }}</td>
            </tr>
            <tr>
                <td class="text-left">Tgl:</td>
                <td class="text-right">{{ \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="text-left">Kasir:</td>
                <td class="text-right">{{ $transaksi->kasir->name ?? 'Sistem' }}</td>
            </tr>
            @if($transaksi->pelanggan)
            <tr>
                <td class="text-left">Cust:</td>
                <td class="text-right">{{ $transaksi->pelanggan->nama }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table class="w-full mb-1">
        @foreach($transaksi->detail as $item)
        <tr>
            <td colspan="3" class="text-left font-bold">{{ $item->produk->nama }}</td>
        </tr>
        <tr>
            <td class="text-left">{{ $item->qty }} x</td>
            <td class="text-left">{{ number_format($item->harga_jual_satuan, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="border-top py-1">
        <table class="w-full font-bold">
            <tr>
                <td class="text-left">TOTAL</td>
                <td class="text-right">Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</td>
            </tr>
            @if($transaksi->status_pembayaran === 'LUNAS')
            <tr>
                <td class="text-left">TUNAI</td>
                <td class="text-right">Rp {{ number_format($transaksi->tunai_diterima, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-left">KEMBALI</td>
                <td class="text-right">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</td>
            </tr>
            @else
            <tr>
                <td colspan="2" class="text-center mt-1">*** BELUM LUNAS (TEMPO) ***</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="text-center mt-1 border-top py-1">
        <p style="margin: 0;">Terima kasih atas kunjungan Anda!</p>
        <p style="margin: 0; font-size: 10px;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
    </div>

</body>
</html>
