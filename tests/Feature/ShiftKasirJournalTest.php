<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PosShift;
use App\Models\Jurnal;
use App\Models\AkunTrans;
use App\Models\AkunCfg;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Filament\Facades\Filament;
use App\Filament\Resources\ShiftKasirResource\Pages\CreateShiftKasir;

class ShiftKasirJournalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Pastikan Filament panel admin ter-registrasi dan aktif untuk pengujian Livewire
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_creating_shift_with_modal_awal_creates_journal_entries(): void
    {
        // 1. Run seeders to get all configs, including AkunCfgSeeder, Gudang, and Shield roles
        $this->seed();

        // 2. Create and authenticate a user with cabang_id and gudang_id, and assign super_admin
        $user = User::factory()->create([
            'cabang_id' => 1,
            'gudang_id' => 1,
        ]);
        $user->assignRole('super_admin');
        
        $this->actingAs($user);

        // 3. Open a shift using Livewire page
        Livewire::test(CreateShiftKasir::class)
            ->fillForm([
                'modal_awal' => 250000,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // 4. Verify that a PosShift record was created
        $shift = PosShift::where('user_id', $user->id)->first();
        $this->assertNotNull($shift);
        $this->assertEquals(250000, $shift->modal_awal);

        // 5. Verify Jurnal was created
        $jurnal = Jurnal::where('referensi_tipe', get_class($shift))
            ->where('referensi_id', $shift->id)
            ->first();
        $this->assertNotNull($jurnal);
        $this->assertEquals($shift->cabang_id, $jurnal->cabang_id);

        // 6. Verify AkunTrans debit and credit records
        $cfg = AkunCfg::where('kode_event', 'SHIFT_KASIR')->first();
        $this->assertNotNull($cfg);

        // Debit: Kasir POS (cfg->akun_debit_id)
        $debitTrans = AkunTrans::where('jurnal_id', $jurnal->id)
            ->where('akun_id', $cfg->akun_debit_id)
            ->first();
        $this->assertNotNull($debitTrans);
        $this->assertEquals(250000, $debitTrans->debit);
        $this->assertEquals(0, $debitTrans->kredit);

        // Kredit: Kas Besar / Brankas (cfg->akun_kredit_id)
        $kreditTrans = AkunTrans::where('jurnal_id', $jurnal->id)
            ->where('akun_id', $cfg->akun_kredit_id)
            ->first();
        $this->assertNotNull($kreditTrans);
        $this->assertEquals(0, $kreditTrans->debit);
        $this->assertEquals(250000, $kreditTrans->kredit);
    }

    public function test_creating_shift_without_modal_awal_does_not_create_journal_entries(): void
    {
        $this->seed();

        $user = User::factory()->create([
            'cabang_id' => 1,
            'gudang_id' => 1,
        ]);
        $user->assignRole('super_admin');
        
        $this->actingAs($user);

        // Open a shift with 0 modal_awal
        Livewire::test(CreateShiftKasir::class)
            ->fillForm([
                'modal_awal' => 0,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $shift = PosShift::where('user_id', $user->id)->first();
        $this->assertNotNull($shift);
        $this->assertEquals(0, $shift->modal_awal);

        // Verify Jurnal was NOT created
        $jurnal = Jurnal::where('referensi_tipe', get_class($shift))
            ->where('referensi_id', $shift->id)
            ->first();
        $this->assertNull($jurnal);
    }
}
