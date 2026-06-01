<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stock Opname</title>
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
        .card-surplus { border-left-color: #38a169; }
        .card-defisit { border-left-color: #e53e3e; }
        
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
        .card-surplus .card-value {
            color: #2f855a;
        }
        .card-defisit .card-value {
            color: #c53030;
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
        .surplus-text {
            color: #2f855a;
            font-weight: 700;
        }
        .defisit-text {
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
                size: A4 landscape;
                margin: 1.2cm;
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
                <div class="report-title">Laporan Stock Opname (Penyesuaian Stok)</div>
            </div>
            <div class="meta-right">
                <div><span class="meta-label">Gudang:</span> {{ $gudang ? $gudang->nama : 'Semua Gudang' }}</div>
                <div><span class="meta-label">Produk:</span> {{ $produk ? $produk->nama : 'Semua Produk' }}</div>
                <div>
                    <span class="meta-label">Periode:</span> 
                    @if($dariTanggal && $sampaiTanggal)
                        {{ $dariTanggal->format('d M Y') }} s/d {{ $sampaiTanggal->format('d M Y') }}
                    @elseif($dariTanggal)
                        Mulai {{ $dariTanggal->format('d M Y') }}
                    @elseif($sampaiTanggal)
                        Sampai {{ $sampaiTanggal->format('d M Y') }}
                    @else
                        Semua Periode
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="summary-cards">
        <div class="card card-total">
            <div class="card-title">Total Baris Koreksi</div>
            <div class="card-value">{{ $totalRecords }} Transaksi</div>
        </div>
        <div class="card card-surplus">
            <div class="card-title">Total Surplus Fisik</div>
            <div class="card-value">+{{ number_format($totalSurplusQty, 2, ',', '.') }} Item</div>
        </div>
        <div class="card card-defisit">
            <div class="card-title">Total Defisit Fisik (Hilang)</div>
            <div class="card-value">-{{ number_format($totalDefisitQty, 2, ',', '.') }} Item</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 15%;">Gudang</th>
                <th style="width: 25%;">Produk</th>
                <th class="text-right" style="width: 10%;">Sistem</th>
                <th class="text-right" style="width: 10%;">Fisik</th>
                <th class="text-right" style="width: 10%;">Selisih</th>
                <th>Keterangan / Alasan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $rec)
                <tr>
                    <td class="font-mono">{{ \Carbon\Carbon::parse($rec->tanggal)->format('d M Y') }}</td>
                    <td>{{ $rec->gudang->nama }}</td>
                    <td style="font-weight: 700;">{{ $rec->produk->nama }}</td>
                    <td class="text-right font-mono">{{ number_format($rec->qty_sistem, 2, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($rec->qty_fisik, 2, ',', '.') }}</td>
                    <td class="text-right font-mono {{ $rec->selisih < 0 ? 'defisit-text' : ($rec->selisih > 0 ? 'surplus-text' : '') }}">
                        {{ $rec->selisih > 0 ? '+' : '' }}{{ number_format($rec->selisih, 2, ',', '.') }}
                    </td>
                    <td>{{ $rec->keterangan }}</td>
                </tr>
            @endforeach

            @if($records->isEmpty())
                <tr>
                    <td colspan="7" style="text-align: center; color: #a0aec0; padding: 30px;">
                        Tidak ada riwayat stock opname yang cocok dengan kriteria filter.
                    </td>
                </tr>
            @endif
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
