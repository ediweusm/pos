<?php

namespace App\Http\Controllers;

use App\Models\JurnalBarang;
use App\Models\StokPeriode;
use App\Models\Produk;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KartuStokPrintController extends Controller
{
    public function print(Request $request)
    {
        $produkId = $request->produk_id;
        $gudangId = $request->gudang_id;
        $dariTanggal = $request->dari_tanggal ?? now()->startOfMonth()->format('Y-m-d');
        $sampaiTanggal = $request->sampai_tanggal ?? now()->endOfMonth()->format('Y-m-d');

        if (!$produkId) {
            return response('Silakan pilih produk terlebih dahulu sebelum mencetak kartu stok.', 400);
        }

        $produk = Produk::findOrFail($produkId);
        $gudang = $gudangId ? Gudang::find($gudangId) : null;

        $tanggalAwal = Carbon::parse($dariTanggal)->startOfDay();
        $tanggalAkhir = Carbon::parse($sampaiTanggal)->endOfDay();
        $periodeLalu = $tanggalAwal->copy()->subMonth()->format('Y-m');

        // 1. Hitung Saldo Awal (Snapshot Bulan Lalu + Mutasi Gap)
        $querySnapshot = StokPeriode::where('periode', $periodeLalu)->where('produk_id', $produkId);
        if ($gudangId) $querySnapshot->where('gudang_id', $gudangId);
        $saldoBulanLalu = (float) $querySnapshot->sum('qty_akhir');

        $awalBulanIni = $tanggalAwal->copy()->startOfMonth();
        $mutasiGapMasuk = 0;
        $mutasiGapKeluar = 0;

        if ($tanggalAwal->greaterThan($awalBulanIni)) {
            $queryGap = JurnalBarang::where('produk_id', $produkId)
                ->whereBetween('created_at', [$awalBulanIni, $tanggalAwal->copy()->subSecond()]);
            if ($gudangId) $queryGap->where('gudang_id', $gudangId);
            
            $mutasiGapMasuk = (float) $queryGap->sum('qty_in');
            $mutasiGapKeluar = (float) $queryGap->sum('qty_out');
        }

        $saldoAwal = $saldoBulanLalu + $mutasiGapMasuk - $mutasiGapKeluar;

        // 2. Fetch data mutasi aktif
        $queryAktif = JurnalBarang::with(['gudang', 'produk', 'referensi'])
            ->where('produk_id', $produkId)
            ->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]);
        if ($gudangId) $queryAktif->where('gudang_id', $gudangId);

        $mutasiList = $queryAktif->orderBy('created_at', 'asc')->get();

        // 3. Totals
        $totalMasuk = $mutasiList->sum('qty_in');
        $totalKeluar = $mutasiList->sum('qty_out');
        $saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;

        return view('laporan.kartu-stok-print', [
            'produk' => $produk,
            'gudang' => $gudang,
            'dariTanggal' => $tanggalAwal,
            'sampaiTanggal' => $tanggalAkhir,
            'saldoAwal' => $saldoAwal,
            'mutasiList' => $mutasiList,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'saldoAkhir' => $saldoAkhir,
        ]);
    }
}
