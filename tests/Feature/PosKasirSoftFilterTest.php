<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produk;
use App\Models\StokSaldo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Filament\Facades\Filament;
use App\Filament\Pages\PosKasir;
use Filament\Notifications\Notification;

class PosKasirSoftFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_product_search_contains_stock_information(): void
    {
        $this->seed();

        $user = User::factory()->create([
            'cabang_id' => 1,
            'gudang_id' => 1,
        ]);
        $user->assignRole('super_admin');
        $this->actingAs($user);

        // Open shift for user to bypass mount guard
        \App\Models\PosShift::create([
            'user_id' => $user->id,
            'cabang_id' => 1,
            'gudang_id' => 1,
            'waktu_buka' => now(),
            'modal_awal' => 0,
            'status' => 'OPEN',
        ]);

        // Create a product and set its stock
        $produk = Produk::create([
            'kategori_id' => 1,
            'satuan_dasar_id' => 1,
            'nama' => 'Pakan Ternak Super',
            'sku' => 'PRD-X01',
            'barcode' => '1234567890',
            'stok_minimum' => 5,
            'is_aktif' => true,
        ]);

        StokSaldo::updateOrCreate(
            ['produk_id' => $produk->id, 'gudang_id' => 1],
            ['qty_sekarang' => 10, 'harga_pokok_rata_rata' => 5000]
        );

        Livewire::test(PosKasir::class)
            ->set('search', 'Ternak Super')
            ->assertSet('searchResults.0.stok', 10.0)
            ->assertSet('searchResults.0.nama', 'Pakan Ternak Super');
    }

    public function test_adding_qty_exceeding_stock_triggers_warning_notification(): void
    {
        $this->seed();

        $user = User::factory()->create([
            'cabang_id' => 1,
            'gudang_id' => 1,
        ]);
        $user->assignRole('super_admin');
        $this->actingAs($user);

        \App\Models\PosShift::create([
            'user_id' => $user->id,
            'cabang_id' => 1,
            'gudang_id' => 1,
            'waktu_buka' => now(),
            'modal_awal' => 0,
            'status' => 'OPEN',
        ]);

        $produk = Produk::create([
            'kategori_id' => 1,
            'satuan_dasar_id' => 1,
            'nama' => 'Pakan Ayam',
            'sku' => 'PRD-X02',
            'barcode' => '1234567891',
            'stok_minimum' => 5,
            'is_aktif' => true,
        ]);

        // Ensure price is set
        \App\Models\HargaProduk::create([
            'produk_id' => $produk->id,
            'satuan_id' => 1,
            'minimal_qty' => 1,
            'harga' => 10000,
            'tipe_harga' => 'ECERAN',
        ]);

        StokSaldo::updateOrCreate(
            ['produk_id' => $produk->id, 'gudang_id' => 1],
            ['qty_sekarang' => 2, 'harga_pokok_rata_rata' => 5000]
        );

        Livewire::test(PosKasir::class)
            // Initial add (qty = 1, stock = 2)
            ->call('pilihProduk', $produk->id)
            // Second add (qty = 2, stock = 2)
            ->call('pilihProduk', $produk->id)
            // Third add (qty = 3, stock = 2) -> Exceeds stock!
            ->call('pilihProduk', $produk->id)
            ->assertNotified(
                Notification::make()
                    ->warning()
                    ->title('Peringatan: Stok Kurang')
                    ->body("Kuantitas (3) melebihi stok sistem untuk produk Pakan Ayam. Sisa stok: 2. Stok akan menjadi minus.")
            );
    }
}
