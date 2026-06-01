<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Besar - {{ $akun->nama_akun }}</title>
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
            grid-template-cols: repeat(4, 1fr);
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
        .card-awal { border-left-color: #718096; }
        .card-debit { border-left-color: #3182ce; }
        .card-kredit { border-left-color: #e53e3e; }
        .card-akhir { border-left-color: #38a169; }
        .card-title {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: #718096;
            letter-spacing: 0.5px;
        }
        .card-value {
            font-size: 14px;
            font-family: monospace;
            font-weight: 700;
            margin-top: 4px;
        }
        .card-akhir .card-value {
            font-size: 15px;
            color: #2f855a;
        }
        .card-akhir .card-value.negative {
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
        .total-row td {
            font-weight: 800;
            background-color: #f7fafc;
            border-top: 2px solid #cbd5e0;
            border-bottom: 2px solid #cbd5e0;
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
                <div class="report-title">Buku Besar</div>
            </div>
            <div class="meta-right">
                <div><span class="meta-label">Kode Akun:</span> {{ $akun->kode_akun }}</div>
                <div><span class="meta-label">Nama Akun:</span> {{ $akun->nama_akun }}</div>
                <div><span class="meta-label">Periode:</span> {{ $dariTanggal->format('d M Y') }} s/d {{ $sampaiTanggal->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    <div class="summary-cards">
        <div class="card card-awal">
            <div class="card-title">Saldo Awal</div>
            <div class="card-value">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</div>
        </div>
        <div class="card card-debit">
            <div class="card-title">Total Debit</div>
            <div class="card-value">Rp {{ number_format($totalDebit, 0, ',', '.') }}</div>
        </div>
        <div class="card card-kredit">
            <div class="card-title">Total Kredit</div>
            <div class="card-value">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
        </div>
        <div class="card card-akhir">
            <div class="card-title">Saldo Akhir</div>
            <div class="card-value {{ $saldoAkhir < 0 ? 'negative' : '' }}">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 15%;">No. Bukti</th>
                <th>Keterangan / Uraian</th>
                <th class="text-right" style="width: 15%;">Debit</th>
                <th class="text-right" style="width: 15%;">Kredit</th>
                <th class="text-right" style="width: 15%;">Saldo Berjalan</th>
            </tr>
        </thead>
        <tbody>
            <!-- Baris Saldo Awal -->
            <tr style="background-color: #f7fafc;">
                <td class="font-mono">{{ $dariTanggal->format('d M Y') }}</td>
                <td class="font-mono" style="color: #a0aec0;">-</td>
                <td style="font-weight: 700; color: #4a5568;">SALDO AWAL PERIODE</td>
                <td class="text-right font-mono" style="color: #a0aec0;">-</td>
                <td class="text-right font-mono" style="color: #a0aec0;">-</td>
                <td class="text-right font-mono" style="font-weight: 700;">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>

            @php $running = $saldoAwal; @endphp
            @foreach($transaksi as $tx)
                @php $running += ($tx->debit - $tx->kredit); @endphp
                <tr>
                    <td class="font-mono">{{ \Carbon\Carbon::parse($tx->jurnal->tanggal)->format('d M Y') }}</td>
                    <td class="font-mono">{{ $tx->jurnal->nomor_jurnal }}</td>
                    <td>{{ $tx->jurnal->keterangan }}</td>
                    <td class="text-right font-mono" style="color: #2b6cb0;">
                        {{ $tx->debit > 0 ? 'Rp ' . number_format($tx->debit, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right font-mono" style="color: #c53030;">
                        {{ $tx->kredit > 0 ? 'Rp ' . number_format($tx->kredit, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right font-mono" style="font-weight: 700; color: {{ $running < 0 ? '#c53030' : '#2d3748' }}">
                        Rp {{ number_format($running, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach

            @if($transaksi->isEmpty())
                <tr>
                    <td colspan="6" style="text-align: center; color: #a0aec0; padding: 30px;">
                        Tidak ada transaksi mutasi selama periode ini.
                    </td>
                </tr>
            @endif

            <!-- Baris Total Mutasi -->
            <tr class="total-row">
                <td colspan="3">MUTASI BERJALAN &amp; SALDO AKHIR</td>
                <td class="text-right font-mono" style="color: #2b6cb0;">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td class="text-right font-mono" style="color: #c53030;">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                <td class="text-right font-mono" style="color: {{ $saldoAkhir < 0 ? '#c53030' : '#2f855a' }}">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
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
