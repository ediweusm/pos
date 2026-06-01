<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Gudang;
use App\Models\Satuan;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\HargaProduk;
use App\Models\AkunMaster;
use App\Models\AkunCfg;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SimulationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles & Permissions
        $this->call(ShieldSeeder::class);

        // 2. Seed COA / AkunMaster
        $akunList = [
            [
                'id' => 1001,
                'kode_akun' => '1001',
                'nama_akun' => 'Kas Utama',
                'tipe_akun' => 'ASET',
            ],
            [
                'id' => 1005,
                'kode_akun' => '1005',
                'nama_akun' => 'Persediaan Barang Dagang',
                'tipe_akun' => 'ASET',
            ],
            [
                'id' => 1006,
                'kode_akun' => '1006',
                'nama_akun' => 'PPN Masukan / Pajak Dibayar di Muka',
                'tipe_akun' => 'ASET',
            ],
            [
                'id' => 2001,
                'kode_akun' => '2001',
                'nama_akun' => 'Utang Dagang',
                'tipe_akun' => 'KEWAJIBAN',
            ],
            [
                'id' => 3001,
                'kode_akun' => '3001',
                'nama_akun' => 'Modal Pemilik',
                'tipe_akun' => 'EKUITAS',
            ],
            [
                'id'        => 1002,
                'kode_akun' => '1002',
                'nama_akun' => 'Piutang Usaha',
                'tipe_akun' => 'ASET',
            ],
            [
                'id' => 4001,
                'kode_akun' => '4001',
                'nama_akun' => 'Pendapatan Penjualan',
                'tipe_akun' => 'PENDAPATAN',
            ],
            [
                'id' => 5001,
                'kode_akun' => '5001',
                'nama_akun' => 'Harga Pokok Penjualan (HPP)',
                'tipe_akun' => 'BEBAN',
            ],
            [
                'id' => 5002,
                'kode_akun' => '5002',
                'nama_akun' => 'Beban Selisih / Penyusutan Stok',
                'tipe_akun' => 'BEBAN',
            ],
            [
                'id' => 5003,
                'kode_akun' => '5003',
                'nama_akun' => 'Beban Selisih / Kerugian Kas',
                'tipe_akun' => 'BEBAN',
            ],
        ];

        foreach ($akunList as $akun) {
            AkunMaster::updateOrCreate(
                ['id' => $akun['id']],
                [
                    'kode_akun' => $akun['kode_akun'],
                    'nama_akun' => $akun['nama_akun'],
                    'tipe_akun' => $akun['tipe_akun'],
                    'is_aktif' => true,
                ]
            );
        }

        // 3. Seed Event AkunCfg
        AkunCfg::updateOrCreate(
            ['kode_event' => 'POS_SALE_TUNAI'],
            [
                'nama_event' => 'Penjualan POS Tunai',
                'akun_debit_id' => 1001,  // Kas Utama
                'akun_kredit_id' => 4001, // Pendapatan Penjualan
            ]
        );

        AkunCfg::updateOrCreate(
            ['kode_event' => 'POS_HPP'],
            [
                'nama_event' => 'Pencatatan HPP Penjualan',
                'akun_debit_id' => 5001,  // Beban HPP
                'akun_kredit_id' => 1005, // Persediaan Barang Dagang
            ]
        );

        AkunCfg::updateOrCreate(
            ['kode_event' => 'POS_SALE_TEMPO'],
            [
                'nama_event'     => 'Penjualan POS Tempo/Kredit',
                'akun_debit_id'  => 1002,  // Piutang Usaha
                'akun_kredit_id' => 4001,  // Pendapatan Penjualan
            ]
        );

        // Seed Event Stock Adjustment
        AkunCfg::updateOrCreate(
            ['kode_event' => 'STOK_DEFISIT'],
            [
                'nama_event'     => 'Stock Opname: Defisit/Hilang',
                'akun_debit_id'  => 5002,  // Beban Selisih Stok
                'akun_kredit_id' => 1005,  // Persediaan Barang
            ]
        );

        AkunCfg::updateOrCreate(
            ['kode_event' => 'STOK_SURPLUS'],
            [
                'nama_event'     => 'Stock Opname: Surplus/Lebih',
                'akun_debit_id'  => 1005,  // Persediaan Barang
                'akun_kredit_id' => 5002,  // Beban Selisih Stok
            ]
        );

        // Seed Event Cash Reconciliation
        AkunCfg::updateOrCreate(
            ['kode_event' => 'KAS_DEFISIT'],
            [
                'nama_event'     => 'Audit Kas: Defisit/Hilang',
                'akun_debit_id'  => 5003,  // Beban Selisih Kas
                'akun_kredit_id' => 1001,  // Kas Utama
            ]
        );

        AkunCfg::updateOrCreate(
            ['kode_event' => 'KAS_SURPLUS'],
            [
                'nama_event'     => 'Audit Kas: Surplus/Lebih',
                'akun_debit_id'  => 1001,  // Kas Utama
                'akun_kredit_id' => 5003,  // Beban Selisih Kas
            ]
        );

        // 3B. Seed Supplier

        $supplierList = [
            [
                'kode_supplier' => 'SUP-001',
                'nama_perusahaan' => 'PT. Jaya Mandiri',
                'nama_pic' => 'Budi Santoso',
                'no_telepon' => '081234567890',
                'alamat' => 'Jl. Kawasan Industri No. 45, Jakarta',
                'email' => 'sales@jayamandiri.com',
            ],
            [
                'kode_supplier' => 'SUP-002',
                'nama_perusahaan' => 'CV. Pakan Makmur',
                'nama_pic' => 'Siti Aminah',
                'no_telepon' => '082345678901',
                'alamat' => 'Jl. Pertanian Raya No. 12, Bogor',
                'email' => 'info@pakanmakmur.com',
            ]
        ];

        foreach ($supplierList as $sup) {
            \App\Models\Supplier::updateOrCreate(
                ['kode_supplier' => $sup['kode_supplier']],
                [
                    'nama_perusahaan' => $sup['nama_perusahaan'],
                    'nama_pic' => $sup['nama_pic'],
                    'no_telepon' => $sup['no_telepon'],
                    'alamat' => $sup['alamat'],
                    'email' => $sup['email'],
                    'is_aktif' => true,
                ]
            );
        }

        // 3C. Seed Pelanggan
        $pelangganList = [
            [
                'kode_pelanggan' => 'CUST-001',
                'nama' => 'Adi Wijaya',
                'no_telepon' => '089876543210',
                'alamat' => 'Jl. Mawar Merah No. 3, Bandung',
                'poin_loyalitas' => 120,
            ],
            [
                'kode_pelanggan' => 'CUST-002',
                'nama' => 'Rina Kartika',
                'no_telepon' => '088765432109',
                'alamat' => 'Jl. Melati Putih No. 7, Surabaya',
                'poin_loyalitas' => 50,
            ]
        ];

        foreach ($pelangganList as $cust) {
            \App\Models\Pelanggan::updateOrCreate(
                ['kode_pelanggan' => $cust['kode_pelanggan']],
                [
                    'nama' => $cust['nama'],
                    'no_telepon' => $cust['no_telepon'],
                    'alamat' => $cust['alamat'],
                    'poin_loyalitas' => $cust['poin_loyalitas'],
                    'is_aktif' => true,
                ]
            );
        }

        // 4. Seed Gudang
        $gudangUtama = Gudang::updateOrCreate(
            ['nama' => 'Gudang Utama'],
            ['is_aktif' => true]
        );

        $gudangEtalase = Gudang::updateOrCreate(
            ['nama' => 'Etalase Depan'],
            ['is_aktif' => true]
        );

        // 5. Seed Satuan
        $satuanPcs = Satuan::updateOrCreate(
            ['nama' => 'Pcs'],
            ['simbol' => 'pcs']
        );

        $satuanBox = Satuan::updateOrCreate(
            ['nama' => 'Box'],
            ['simbol' => 'box']
        );

        // 6. Seed Kategori
        $katMakanan = Kategori::updateOrCreate(
            ['nama' => 'Makanan & Minuman'],
            ['deskripsi' => 'Kategori untuk makanan ringan, mie instan, dan minuman botol']
        );

        $katAtk = Kategori::updateOrCreate(
            ['nama' => 'Alat Tulis Kantor'],
            ['deskripsi' => 'Kategori untuk perlengkapan tulis, kertas, dll']
        );

        // 7. Seed Produk
        Produk::updateOrCreate(
            ['sku' => 'PROD-001'],
            [
                'kategori_id' => $katMakanan->id,
                'satuan_dasar_id' => $satuanPcs->id,
                'nama' => 'Indomie Goreng Aceh',
                'barcode' => '8998866200251',
                'stok_minimum' => 10,
                'is_aktif' => true,
            ]
        );

        Produk::updateOrCreate(
            ['sku' => 'PROD-002'],
            [
                'kategori_id' => $katMakanan->id,
                'satuan_dasar_id' => $satuanPcs->id,
                'nama' => 'Teh Botol Sosro 350ml',
                'barcode' => '8999908000004',
                'stok_minimum' => 5,
                'is_aktif' => true,
            ]
        );

        Produk::updateOrCreate(
            ['sku' => 'PROD-003'],
            [
                'kategori_id' => $katAtk->id,
                'satuan_dasar_id' => $satuanBox->id,
                'nama' => 'Kertas A4 Sinar Dunia 80gr',
                'barcode' => '8993215104256',
                'stok_minimum' => 2,
                'is_aktif' => true,
            ]
        );

        // 8. Seed Harga Produk (Tiered Pricing)
        // Indomie Goreng Aceh — eceran & grosir
        $produk001 = Produk::where('sku', 'PROD-001')->first();
        HargaProduk::updateOrCreate(
            ['produk_id' => $produk001->id, 'satuan_id' => $satuanPcs->id, 'tipe_harga' => 'ECERAN'],
            ['minimal_qty' => 1, 'harga' => 3500]
        );
        HargaProduk::updateOrCreate(
            ['produk_id' => $produk001->id, 'satuan_id' => $satuanPcs->id, 'tipe_harga' => 'GROSIR'],
            ['minimal_qty' => 10, 'harga' => 3000]
        );

        // Teh Botol Sosro — eceran & grosir
        $produk002 = Produk::where('sku', 'PROD-002')->first();
        HargaProduk::updateOrCreate(
            ['produk_id' => $produk002->id, 'satuan_id' => $satuanPcs->id, 'tipe_harga' => 'ECERAN'],
            ['minimal_qty' => 1, 'harga' => 5000]
        );
        HargaProduk::updateOrCreate(
            ['produk_id' => $produk002->id, 'satuan_id' => $satuanPcs->id, 'tipe_harga' => 'GROSIR'],
            ['minimal_qty' => 12, 'harga' => 4500]
        );

        // Kertas A4 — eceran & grosir (per box)
        $produk003 = Produk::where('sku', 'PROD-003')->first();
        HargaProduk::updateOrCreate(
            ['produk_id' => $produk003->id, 'satuan_id' => $satuanBox->id, 'tipe_harga' => 'ECERAN'],
            ['minimal_qty' => 1, 'harga' => 45000]
        );
        HargaProduk::updateOrCreate(
            ['produk_id' => $produk003->id, 'satuan_id' => $satuanBox->id, 'tipe_harga' => 'GROSIR'],
            ['minimal_qty' => 5, 'harga' => 42000]
        );

        // 9. Seed Default Super Admin User
        $adminUser = User::updateOrCreate(
            ['email' => 'ediwidodo@gmail.com'],
            [
                'name' => 'Administrator POS',
                'password' => Hash::make('admin123'),
            ]
        );

        // Pastikan role super_admin ditugaskan ke user ini
        $adminUser->assignRole('super_admin');
    }
}
