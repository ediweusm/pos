<?php

namespace App\Http\Controllers;

use App\Models\PosTransaksi;
use Illuminate\Http\Request;

class CetakStrukController extends Controller
{
    public function strukPos($id)
    {
        $transaksi = PosTransaksi::with(['detail.produk', 'kasir', 'pelanggan'])->findOrFail($id);
        
        return view('cetak.struk-pos', compact('transaksi'));
    }
}
