<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="6" style="font-weight: bold; font-size: 14px; text-align: center;">LAPORAN BUKU BESAR</th>
            </tr>
            <tr>
                <th colspan="6" style="font-weight: bold; text-align: center;">SISTEM KASIR &amp; LOGISTIK POS</th>
            </tr>
            <tr>
                <th colspan="2" style="text-align: left;">Kode Akun:</th>
                <th colspan="4" style="text-align: left; font-weight: bold;">{{ $akun->kode_akun }}</th>
            </tr>
            <tr>
                <th colspan="2" style="text-align: left;">Nama Akun:</th>
                <th colspan="4" style="text-align: left; font-weight: bold;">{{ $akun->nama_akun }}</th>
            </tr>
            <tr>
                <th colspan="2" style="text-align: left;">Periode:</th>
                <th colspan="4" style="text-align: left; font-weight: bold;">{{ $dariTanggal->format('d M Y') }} s/d {{ $sampaiTanggal->format('d M Y') }}</th>
            </tr>
            <tr><th colspan="6"></th></tr> <!-- Spacer -->
            <tr>
                <th style="background-color: #edf2f7; font-weight: bold; border: 1px solid #000000;">Tanggal</th>
                <th style="background-color: #edf2f7; font-weight: bold; border: 1px solid #000000;">No. Bukti</th>
                <th style="background-color: #edf2f7; font-weight: bold; border: 1px solid #000000;">Keterangan / Uraian</th>
                <th style="background-color: #edf2f7; font-weight: bold; border: 1px solid #000000; text-align: right;">Debit</th>
                <th style="background-color: #edf2f7; font-weight: bold; border: 1px solid #000000; text-align: right;">Kredit</th>
                <th style="background-color: #edf2f7; font-weight: bold; border: 1px solid #000000; text-align: right;">Saldo Berjalan</th>
            </tr>
        </thead>
        <tbody>
            <!-- Saldo Awal -->
            <tr>
                <td style="border: 1px solid #000000;">{{ $dariTanggal->format('d M Y') }}</td>
                <td style="border: 1px solid #000000; color: #a0aec0; text-align: center;">-</td>
                <td style="border: 1px solid #000000; font-weight: bold; color: #4a5568;">SALDO AWAL PERIODE</td>
                <td style="border: 1px solid #000000; text-align: right;">-</td>
                <td style="border: 1px solid #000000; text-align: right;">-</td>
                <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $saldoAwal }}</td>
            </tr>

            @php $running = $saldoAwal; @endphp
            @foreach($transaksi as $tx)
                @php $running += ($tx->debit - $tx->kredit); @endphp
                <tr>
                    <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::parse($tx->jurnal->tanggal)->format('d M Y') }}</td>
                    <td style="border: 1px solid #000000;">{{ $tx->jurnal->nomor_jurnal }}</td>
                    <td style="border: 1px solid #000000;">{{ $tx->jurnal->keterangan }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $tx->debit > 0 ? $tx->debit : 0 }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $tx->kredit > 0 ? $tx->kredit : 0 }}</td>
                    <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $running }}</td>
                </tr>
            @endforeach

            <!-- Total Mutasi -->
            <tr style="font-weight: bold; background-color: #f7fafc;">
                <td colspan="3" style="border: 1px solid #000000; font-weight: bold;">MUTASI BERJALAN &amp; SALDO AKHIR</td>
                <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $totalDebit }}</td>
                <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $totalKredit }}</td>
                <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $saldoAkhir }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
