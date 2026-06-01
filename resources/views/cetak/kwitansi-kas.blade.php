@php
    function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
        }     
        return $temp;
    }
 
    function terbilang($nilai) {
        if($nilai < 0) {
            $hasil = "minus ". trim(penyebut($nilai));
        } else {
            $hasil = trim(penyebut($nilai));
        }     
        return ucwords($hasil) . " Rupiah";
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=device-width, initial-scale=device-width, initial-scale=1.0">
    <title>Kwitansi Transaksi Kas - {{ $transaksi->nomor_bukti }}</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-shadow: none !important;
            text-shadow: none !important;
            filter: none !important;
        }
        body {
            font-family: 'Courier New', Courier, monospace, Arial, sans-serif;
            color: #1a202c;
            background: #fff;
            margin: 0;
            padding: 20px;
            font-size: 13px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #2d3748;
            padding: 25px;
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px double #2d3748;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-info h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .company-info p {
            margin: 3px 0 0 0;
            font-size: 11px;
            color: #4a5568;
        }
        .voucher-title {
            text-align: right;
        }
        .voucher-title h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            text-decoration: underline;
            color: #2d3748;
        }
        .voucher-title p {
            margin: 5px 0 0 0;
            font-size: 12px;
            font-weight: bold;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .content-table td {
            padding: 8px 5px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 200px;
            position: relative;
        }
        .label::after {
            content: ":";
            position: absolute;
            right: 10px;
        }
        .value {
            color: #1a202c;
        }
        .amount-box {
            background-color: #edf2f7;
            border: 1px dashed #2d3748;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 800;
            display: inline-block;
            margin-top: 10px;
            font-family: 'Courier New', Courier, monospace;
        }
        .spelled-out {
            font-style: italic;
            font-weight: bold;
            color: #4a5568;
            background: #f7fafc;
            padding: 10px;
            border-left: 3px solid #4a5568;
            margin-top: 5px;
        }
        .footer-signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-box {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 110px;
        }
        .signature-box p {
            margin: 0;
        }
        .signature-line {
            border-bottom: 1px solid #718096;
            width: 80%;
            margin: 0 auto;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 60px;
            font-weight: 900;
            color: rgba(226, 232, 240, 0.4);
            z-index: 0;
            pointer-events: none;
            letter-spacing: 5px;
            text-transform: uppercase;
        }
        @media print {
            body {
                padding: 0;
            }
            .container {
                border: 2px solid #000;
            }
            .amount-box {
                border: 1px dashed #000;
                background-color: #f0f0f0 !important;
            }
            .spelled-out {
                background: #f9f9f9 !important;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="watermark">MUTASI KAS</div>
    
    <div class="header">
        <div class="company-info">
            <h2>ERP POS SYSTEM</h2>
            <p>Sistem Keuangan & Akuntansi Terintegrasi</p>
            <p>Dicetak pada: {{ now()->format('d M Y H:i:s') }}</p>
        </div>
        <div class="voucher-title">
            <h1>BUKTI MUTASI KAS</h1>
            <p>No. {{ $transaksi->nomor_bukti }}</p>
        </div>
    </div>

    <table class="content-table">
        <tr>
            <td class="label">Tanggal Transaksi</td>
            <td class="value">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Sumber Dana (Kredit)</td>
            <td class="value"><strong>[{{ $transaksi->akunPengirim?->kode_akun }}] {{ $transaksi->akunPengirim?->nama_akun }}</strong></td>
        </tr>
        <tr>
            <td class="label">Tujuan Dana (Debit)</td>
            <td class="value"><strong>[{{ $transaksi->akunPenerima?->kode_akun }}] {{ $transaksi->akunPenerima?->nama_akun }}</strong></td>
        </tr>
        <tr>
            <td class="label">Jumlah Nominal</td>
            <td class="value">
                <div class="amount-box">
                    Rp {{ number_format($transaksi->nominal, 2, ',', '.') }}
                </div>
            </td>
        </tr>
        <tr>
            <td class="label">Terbilang</td>
            <td class="value">
                <div class="spelled-out">
                    # {{ terbilang($transaksi->nominal) }} #
                </div>
            </td>
        </tr>
        <tr>
            <td class="label">Keterangan</td>
            <td class="value">{{ $transaksi->keterangan }}</td>
        </tr>
    </table>

    <div class="footer-signatures">
        <div class="signature-box">
            <p>Dibuat Oleh,</p>
            <div class="signature-line"></div>
            <p>( Staf Keuangan )</p>
        </div>
        <div class="signature-box">
            <p>Disetujui Oleh,</p>
            <div class="signature-line"></div>
            <p>( Pimpinan / Kasir )</p>
        </div>
        <div class="signature-box">
            <p>Penerima,</p>
            <div class="signature-line"></div>
            <p>( Yang Menerima )</p>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
    };
</script>
</body>
</html>
