<?php

namespace App\Http\Controllers;

use App\Models\PenyesuaianStok;
use App\Models\Gudang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PenyesuaianStokController extends Controller
{
    public function print(Request $request)
    {
        $this->requirePermission($request, 'ViewAny:PenyesuaianStok');

        $gudangId = $request->gudang_id;
        if (! $request->user()->hasRole('super_admin')) {
            abort_unless($request->user()->gudang_id, 403);
            $gudangId = $request->user()->gudang_id;
        }
        $produkId = $request->produk_id;
        $dariTanggal = $request->dari_tanggal;
        $sampaiTanggal = $request->sampai_tanggal;

        $gudang = $gudangId ? Gudang::find($gudangId) : null;
        $produk = $produkId ? Produk::find($produkId) : null;

        $query = PenyesuaianStok::query()
            ->with(['gudang', 'produk'])
            ->when($gudangId, fn ($q) => $q->where('gudang_id', $gudangId))
            ->when($produkId, fn ($q) => $q->where('produk_id', $produkId))
            ->when($dariTanggal, fn ($q) => $q->whereDate('tanggal', '>=', $dariTanggal))
            ->when($sampaiTanggal, fn ($q) => $q->whereDate('tanggal', '<=', $sampaiTanggal));

        $records = $query->orderBy('tanggal', 'desc')->get();

        // Hitung Ringkasan Metrik
        $totalRecords = $records->count();
        $totalSurplusQty = $records->where('selisih', '>', 0)->sum('selisih');
        $totalDefisitQty = abs($records->where('selisih', '<', 0)->sum('selisih'));

        return view('laporan.penyesuaian-stok-print', [
            'records' => $records,
            'gudang' => $gudang,
            'produk' => $produk,
            'dariTanggal' => $dariTanggal ? Carbon::parse($dariTanggal) : null,
            'sampaiTanggal' => $sampaiTanggal ? Carbon::parse($sampaiTanggal) : null,
            'totalRecords' => $totalRecords,
            'totalSurplusQty' => $totalSurplusQty,
            'totalDefisitQty' => $totalDefisitQty,
        ]);
    }
}
