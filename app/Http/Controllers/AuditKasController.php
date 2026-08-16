<?php

namespace App\Http\Controllers;

use App\Models\AuditKas;
use App\Models\AkunMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuditKasController extends Controller
{
    public function print(Request $request)
    {
        $this->requirePermission($request, 'ViewAny:AuditKas');

        $akunId = $request->akun_id;
        $dariTanggal = $request->dari_tanggal;
        $sampaiTanggal = $request->sampai_tanggal;

        $akun = $akunId ? AkunMaster::find($akunId) : null;

        $query = AuditKas::query()
            ->with(['akun'])
            ->when($akunId, fn ($q) => $q->where('akun_id', $akunId))
            ->when($dariTanggal, fn ($q) => $q->whereDate('tanggal', '>=', $dariTanggal))
            ->when($sampaiTanggal, fn ($q) => $q->whereDate('tanggal', '<=', $sampaiTanggal));

        $records = $query->orderBy('tanggal', 'desc')->get();

        // Hitung Ringkasan Metrik
        $totalRecords = $records->count();
        $totalSistem = $records->sum('nominal_sistem');
        $totalFisik = $records->sum('nominal_fisik');
        
        $totalSurplus = $records->where('selisih', '>', 0)->sum('selisih');
        $totalDefisit = abs($records->where('selisih', '<', 0)->sum('selisih'));

        return view('laporan.audit-kas-print', [
            'records' => $records,
            'akun' => $akun,
            'dariTanggal' => $dariTanggal ? Carbon::parse($dariTanggal) : null,
            'sampaiTanggal' => $sampaiTanggal ? Carbon::parse($sampaiTanggal) : null,
            'totalRecords' => $totalRecords,
            'totalSistem' => $totalSistem,
            'totalFisik' => $totalFisik,
            'totalSurplus' => $totalSurplus,
            'totalDefisit' => $totalDefisit,
        ]);
    }
}
