<?php

namespace App\Http\Controllers;

use App\Models\TransaksiKas;
use Illuminate\Http\Request;

class TransaksiKasController extends Controller
{
    public function cetakKwitansi($id)
    {
        $transaksi = TransaksiKas::with(['akunPengirim', 'akunPenerima'])->findOrFail($id);

        return view('cetak.kwitansi', compact('transaksi'));
    }
}
