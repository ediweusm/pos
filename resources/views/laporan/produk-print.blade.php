<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk &amp; Persediaan</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1a202c;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            background-color: #fff;
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
        }
        .company-title {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-title {
            font-size: 22px;
            font-weight: 900;
            margin-top: 5px;
            color: #2b6cb0;
        }
        .meta-grid {
            display: grid;
            grid-template-cols: 1fr 1fr;
            margin-top: 10px;
        }
        .meta-right {
            text-align: right;
        }
        .meta-label {
            font-weight: 700;
            color: #4a5568;
        }
        .summary-cards {
            display: grid;
            grid-template-cols: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .card {
            border: 1px solid #e2e8f0;
            border-left: 4px solid #cbd5e0;
            padding: 12px;
            border-radius: 8px;
            background-color: #f7fafc;
        }
        .card-total { border-left-color: #718096; }
        .card-stok { border-left-color: #3182ce; }
        .card-asset { border-left-color: #38a169; }
        
        .card-title {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: #718096;
            letter-spacing: 0.5px;
        }
        .card-value {
            font-size: 15px;
            font-family: monospace;
            font-weight: 700;
            margin-top: 4px;
        }
        .card-stok .card-value {
            color: #2b6cb0;
        }
        .card-asset .card-value {
            color: #2f855a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        th {
            background-color: #edf2f7;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            color: #4a5568;
            border-top: 1px solid #cbd5e0;
            border-bottom: 2px solid #cbd5e0;
        }
        .text-right {
            text-align: right;
        }
        .font-mono {
            font-family: monospace;
            font-size: 11px;
        }
        .total-row td {
            font-weight: 800;
            background-color: #f7fafc;
            border-top: 2px solid #cbd5e0;
            border-bottom: 2px solid #cbd5e0;
        }
        .stok-warning {
            color: #c53030;
            font-weight: 700;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
        }
        .btn-print-box {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-print {
            background-color: #3182ce;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            font-size: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-print:hover {
            background-color: #2b6cb0;
        }
    </style>
</head>
<body>
    <div class="btn-print-box no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="header">
        <div class="meta-grid">
            <div>
                <div class="company-title">SISTEM KASIR &amp; LOGISTIK POS</div>
                <div class="report-title">Daftar Produk &amp; Persediaan</div>
            </div>
            <div class="meta-right">
                <div><span class="meta-label">Kategori:</span> {{ $kategori ? $kategori->nama : 'Semua Kategori' }}</div>
                @if($search)
                    <div><span class="meta-label">Pencarian:</span> "{{ $search }}"</div>
                @endif
                <div><span class="meta-label">Tanggal Cetak:</span> {{ now()->format('d M Y, H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="summary-cards">
        <div class="card card-total">
            <div class="card-title">Total Jenis Produk</div>
            <div class="card-value">{{ $totalRecords }} Item</div>
        </div>
        <div class="card card-stok">
            <div class="card-title">Total Stok Fisik</div>
            <div class="card-value">{{ number_format($totalStok, 2, ',', '.') }} Item</div>
        </div>
        <div class="card card-asset">
            <div class="card-title">Estimasi Aset (Harga Jual)</div>
            <div class="card-value">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">SKU</th>
                <th>Nama Produk</th>
                <th style="width: 15%;">Kategori</th>
                <th class="text-right" style="width: 12%;">Stok</th>
                <th style="width: 10%;">Satuan</th>
                <th class="text-right" style="width: 15%;">Harga Eceran</th>
                <th class="text-right" style="width: 18%;">Subtotal Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $p)
                @php
                    $pStok = $p->stokSaldos->sum('qty_sekarang');
                    $pHarga = $p->getHargaEceranDefault()?->harga ?? 0;
                    $pSubtotal = $pStok * $pHarga;
                @endphp
                <tr>
                    <td class="font-mono">{{ $p->sku ?? '-' }}</td>
                    <td style="font-weight: 700;">{{ $p->nama }}</td>
                    <td>{{ $p->kategori->nama ?? '-' }}</td>
                    <td class="text-right font-mono {{ $pStok <= 0 ? 'stok-warning' : '' }}">
                        {{ number_format($pStok, 2, ',', '.') }}
                    </td>
                    <td>{{ $p->satuanDasar->nama ?? '-' }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($pHarga, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="font-weight: 700;">Rp {{ number_format($pSubtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            @if($records->isEmpty())
                <tr>
                    <td colspan="7" style="text-align: center; color: #a0aec0; padding: 30px;">
                        Tidak ada produk yang terdaftar.
                    </td>
                </tr>
            @endif

            <!-- Baris Total Aset -->
            <tr class="total-row">
                <td colspan="3">TOTAL ESTIMASI PERSEDIAAN</td>
                <td class="text-right font-mono" style="color: #2b6cb0;">{{ number_format($totalStok, 2, ',', '.') }}</td>
                <td>-</td>
                <td>-</td>
                <td class="text-right font-mono" style="color: #2f855a;">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        }
    </script>
</body>
</html>
