<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;

class ProdukPrintController extends Controller
{
    public function print(Request $request)
    {
        $this->requirePermission($request, 'ViewAny:Produk');

        $search = $request->search;
        $kategoriId = $request->kategori_id;

        $kategori = $kategoriId ? Kategori::find($kategoriId) : null;

        $query = Produk::query()
            ->with(['kategori', 'satuanDasar', 'stokSaldos', 'harga'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($kategoriId, fn ($q) => $q->where('kategori_id', $kategoriId));

        $records = $query->orderBy('nama', 'asc')->get();

        // Hitung Ringkasan Metrik
        $totalRecords = $records->count();
        $totalStok = $records->sum(fn ($p) => $p->stokSaldos->sum('qty_sekarang'));
        $totalAssetValue = $records->sum(fn ($p) => $p->stokSaldos->sum('qty_sekarang') * ($p->getHargaEceranDefault()?->harga ?? 0));

        return view('laporan.produk-print', [
            'records' => $records,
            'search' => $search,
            'kategori' => $kategori,
            'totalRecords' => $totalRecords,
            'totalStok' => $totalStok,
            'totalAssetValue' => $totalAssetValue,
        ]);
    }
}
