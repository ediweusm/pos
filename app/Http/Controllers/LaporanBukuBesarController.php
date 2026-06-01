<?php

namespace App\Http\Controllers;

use App\Models\AkunMaster;
use App\Models\AkunTrans;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanBukuBesarController extends Controller
{
    private function getLaporanData(Request $request)
    {
        $request->validate([
            'akun_id' => 'required|exists:akun_master,id',
            'dari_tanggal' => 'required|date',
            'sampai_tanggal' => 'required|date',
        ]);

        $akun = AkunMaster::findOrFail($request->akun_id);
        $dariTanggal = Carbon::parse($request->dari_tanggal)->startOfDay();
        $sampaiTanggal = Carbon::parse($request->sampai_tanggal)->endOfDay();

        // 1. Hitung Saldo Awal (Semua mutasi SEBELUM tanggal filter)
        $saldoAwal = AkunTrans::where('akun_id', $akun->id)
            ->whereHas('jurnal', fn ($q) => $q->where('tanggal', '<', $dariTanggal))
            ->selectRaw('SUM(debit) - SUM(kredit) as saldo')
            ->value('saldo') ?? 0;

        // 2. Transaksi (Pada rentang filter terpilih)
        $transaksi = AkunTrans::query()
            ->select('akun_trans.*')
            ->join('jurnal', 'jurnal.id', '=', 'akun_trans.jurnal_id')
            ->with('jurnal')
            ->where('akun_trans.akun_id', $akun->id)
            ->whereBetween('jurnal.tanggal', [$dariTanggal, $sampaiTanggal])
            ->orderBy('jurnal.tanggal', 'asc')
            ->get();

        // 3. Hitung Total Debit & Kredit
        $totalDebit = $transaksi->sum('debit');
        $totalKredit = $transaksi->sum('kredit');
        $saldoAkhir = $saldoAwal + $totalDebit - $totalKredit;

        return [
            'akun' => $akun,
            'dariTanggal' => $dariTanggal,
            'sampaiTanggal' => $sampaiTanggal,
            'saldoAwal' => $saldoAwal,
            'transaksi' => $transaksi,
            'totalDebit' => $totalDebit,
            'totalKredit' => $totalKredit,
            'saldoAkhir' => $saldoAkhir,
        ];
    }

    public function print(Request $request)
    {
        $data = $this->getLaporanData($request);
        return view('laporan.buku-besar-print', $data);
    }

    public function excel(Request $request)
    {
        $data = $this->getLaporanData($request);

        $filename = 'Laporan_Buku_Besar_' . str_replace(' ', '_', $data['akun']->nama_akun) . '_' . $data['dariTanggal']->format('Ymd') . '_' . $data['sampaiTanggal']->format('Ymd') . '.xls';

        return response()->view('laporan.buku-besar-excel', $data)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }
}
