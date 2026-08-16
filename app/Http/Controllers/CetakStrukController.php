<?php

namespace App\Http\Controllers;

use App\Models\PosTransaksi;
use Illuminate\Http\Request;

class CetakStrukController extends Controller
{
    public function strukPos(Request $request, $id)
    {
        $transaksi = PosTransaksi::with(['detail.produk', 'kasir', 'pelanggan'])->findOrFail($id);

        $user = $request->user();
        abort_unless(
            $user && ($user->hasRole('super_admin') || $transaksi->kasir_id === $user->id),
            403
        );
        
        return view('cetak.struk-pos', compact('transaksi'));
    }
}
