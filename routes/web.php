<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\CetakStrukController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/cetak/struk-pos/{id}', [CetakStrukController::class, 'strukPos'])->name('cetak.struk-pos');
    Route::get('/buku-besar/print', [\App\Http\Controllers\LaporanBukuBesarController::class, 'print'])->name('buku-besar.print');
    Route::get('/buku-besar/excel', [\App\Http\Controllers\LaporanBukuBesarController::class, 'excel'])->name('buku-besar.excel');
    Route::get('/penyesuaian-stok/print', [\App\Http\Controllers\PenyesuaianStokController::class, 'print'])->name('penyesuaian-stok.print');
    Route::get('/kartu-stok/print', [\App\Http\Controllers\KartuStokPrintController::class, 'print'])->name('kartu-stok.print');
    Route::get('/produk/print', [\App\Http\Controllers\ProdukPrintController::class, 'print'])->name('produk.print');
    Route::get('/audit-kas/print', [\App\Http\Controllers\AuditKasController::class, 'print'])->name('audit-kas.print');
    Route::get('/cetak/kwitansi/{id}', [\App\Http\Controllers\TransaksiKasController::class, 'cetakKwitansi'])->name('cetak.kwitansi');
});

