<?php

namespace App\Filament\Resources\Pembelians\Schemas;

use App\Models\Supplier;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class PembelianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: HEADER FAKTUR
                Section::make('Informasi Faktur')
                    ->columnSpan('full')
                    ->schema([
                        TextInput::make('nomor_faktur')
                            ->required()
                            ->unique(ignoreRecord: true),
                        DatePicker::make('tanggal')
                            ->default(now())
                            ->required(),
                        Select::make('gudang_id')
                            ->relationship('gudang', 'nama')
                            ->label('Lokasi Bongkar/Gudang')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship('supplier', 'nama_perusahaan')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('kode_supplier')
                                    ->label('Kode Supplier')
                                    ->unique('supplier', 'kode_supplier')
                                    ->maxLength(20),
                                TextInput::make('nama_perusahaan')
                                    ->label('Nama Perusahaan')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('nama_kontak')
                                    ->label('Nama Kontak')
                                    ->maxLength(255),
                                TextInput::make('telepon')
                                    ->label('Telepon')
                                    ->tel()
                                    ->maxLength(50),
                                TextInput::make('npwp')
                                    ->label('NPWP')
                                    ->maxLength(30),
                                Select::make('is_pkp')
                                    ->label('Status PKP')
                                    ->options(['1' => 'PKP (Penerbit Faktur Pajak)', '0' => 'Non-PKP'])
                                    ->default('0')
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return Supplier::create($data)->getKey();
                            }),
                        Select::make('status_pembayaran')
                            ->options([
                                'LUNAS' => 'Lunas / Tunai',
                                'UTANG' => 'Utang / Tempo',
                            ])
                            ->default('LUNAS')
                            ->required(),
                    ])->columns(3),

                // SECTION 2: REPEATER BARANG
                Section::make('Rincian Barang')
                    ->columnSpan('full')
                    ->schema([
                        Repeater::make('detail')
                            ->relationship('detail') // Mengikat ke relasi hasMany di Model Pembelian
                            ->schema([
                                Select::make('produk_id')
                                    ->relationship('produk', 'nama')
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('satuan_id', null))
                                    ->columnSpan(3),
                                Select::make('satuan_id')
                                    ->label('Satuan')
                                    ->options(function (Get $get) {
                                        $produkId = $get('produk_id');
                                        if (!$produkId) {
                                            return [];
                                        }
                                        
                                        $produk = \App\Models\Produk::with('satuanDasar')->find($produkId);
                                        $options = [];
                                        if ($produk && $produk->satuanDasar) {
                                            $options[$produk->satuan_dasar_id] = $produk->satuanDasar->nama . ' (Utama)';
                                        }

                                        $konversis = \App\Models\ProdukKonversi::where('produk_id', $produkId)
                                            ->with('satuan')
                                            ->get();

                                        foreach ($konversis as $konversi) {
                                            if ($konversi->satuan) {
                                                $options[$konversi->satuan_id] = $konversi->satuan->nama;
                                            }
                                        }

                                        return $options;
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('qty')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateBarisTotal($get, $set))
                                    ->columnSpan(2),
                                TextInput::make('harga_beli_satuan')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateBarisTotal($get, $set))
                                    ->columnSpan(2),
                                TextInput::make('subtotal')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated() // Penting: Agar kolom yang didisable tetap disimpan ke database
                                    ->columnSpan(3),
                            ])
                            ->columns(12)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateGrandTotal($get, $set))
                    ]),

                // SECTION 3: KALKULASI PPN & GRAND TOTAL
                Section::make('Kalkulasi Total')
                    ->columnSpan('full')
                    ->schema([
                        TextInput::make('subtotal_barang')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('ppn_persen')
                            ->label('PPN (%)')
                            ->numeric()
                            ->default(0) // Default 0 untuk non-PKP
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateGrandTotal($get, $set)),
                        TextInput::make('ppn_nominal')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('grand_total')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(4),
            ]);
    }

    // --- FUNGSI PEMBANTU UNTUK KALKULASI OTOMATIS DI FORM ---

    public static function updateBarisTotal(Get $get, Set $set): void
    {
        $qty = (float) ($get('qty') ?? 0);
        $harga = (float) ($get('harga_beli_satuan') ?? 0);
        
        $set('subtotal', $qty * $harga);

        // Setelah subtotal baris diupdate, langsung kalkulasikan ulang seluruh total
        self::updateGrandTotal($get, $set);
    }

    public static function updateGrandTotal(Get $get, Set $set): void
    {
        // Cari tahu apakah kita sedang dipanggil dari dalam baris repeater (context relative)
        // atau dari tingkat form utama (context root).
        $detail = $get('detail');
        $isRepeaterContext = false;

        if ($detail === null) {
            $detail = $get('../../detail');
            if ($detail !== null) {
                $isRepeaterContext = true;
            }
        }

        $detail = $detail ?? [];

        // 2. Hitung subtotal seluruh barang
        $subtotalBarang = 0;
        foreach ($detail as $item) {
            $subtotalBarang += (float) ($item['subtotal'] ?? 0);
        }

        // 3. Ambil persentase PPN (menyesuaikan dengan context)
        $ppnPersen = $isRepeaterContext
            ? (float) ($get('../../ppn_persen') ?? 0)
            : (float) ($get('ppn_persen') ?? 0);

        // 4. Hitung PPN Nominal dan Grand Total
        $ppnNominal = $subtotalBarang * ($ppnPersen / 100);
        $grandTotal = $subtotalBarang + $ppnNominal;

        // 5. Update state pada UI (menyesuaikan dengan context path)
        if ($isRepeaterContext) {
            $set('../../subtotal_barang', $subtotalBarang);
            $set('../../ppn_nominal', $ppnNominal);
            $set('../../grand_total', $grandTotal);
        } else {
            $set('subtotal_barang', $subtotalBarang);
            $set('ppn_nominal', $ppnNominal);
            $set('grand_total', $grandTotal);
        }
    }
}
