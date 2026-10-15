<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\KategoriKas;
use App\Models\MutasiStok;
use App\Models\Pengaturan;
use App\Models\TransaksiKas;
use App\Models\TransaksiPenjualan;
use App\Models\TransaksiPenjualanDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users (3 Roles)
        $bendahara = User::create([
            'name' => 'Ibu Rahmawati (Bendahara)',
            'email' => 'bendahara@pkk.local',
            'no_hp' => '081234567890',
            'role' => 'bendahara',
            'password' => Hash::make('123456'),
        ]);

        $ketua = User::create([
            'name' => 'Ibu Hj. Aminah (Ketua PKK)',
            'email' => 'ketua@pkk.local',
            'no_hp' => '081234567891',
            'role' => 'ketua',
            'password' => Hash::make('123456'),
        ]);

        $anggota1 = User::create([
            'name' => 'Ibu Siti Khodijah (Anggota)',
            'email' => 'anggota@pkk.local',
            'no_hp' => '081234567892',
            'role' => 'anggota',
            'password' => Hash::make('123456'),
        ]);

        $anggota2 = User::create([
            'name' => 'Ibu Rini Astuti (Anggota)',
            'email' => 'rini@pkk.local',
            'no_hp' => '081234567893',
            'role' => 'anggota',
            'password' => Hash::make('123456'),
        ]);

        // 2. Create Kategori Kas
        $katKasMasuk1 = KategoriKas::create(['nama_kategori' => 'Kas Modal Awal PKK', 'jenis' => 'masuk']);
        $katKasMasuk2 = KategoriKas::create(['nama_kategori' => 'Iuran Rutin Anggota', 'jenis' => 'masuk']);
        $katKasMasuk3 = KategoriKas::create(['nama_kategori' => 'Bantuan Kelurahan Ketintang', 'jenis' => 'masuk']);

        $katKasKeluar1 = KategoriKas::create(['nama_kategori' => 'Biaya Operasional & Listrik', 'jenis' => 'keluar']);
        $katKasKeluar2 = KategoriKas::create(['nama_kategori' => 'Konsumsi Rapat Pertemuan', 'jenis' => 'keluar']);
        $katKasKeluar3 = KategoriKas::create(['nama_kategori' => 'Pembelian Kantong & Mika Plastik', 'jenis' => 'keluar']);

        // 3. Create Kategori Barang
        $katCamilan = KategoriBarang::create([
            'nama_kategori' => 'Camilan Kering',
            'deskripsi' => 'Keripik, peyek, stik bawang, kue kering',
        ]);
        $katBasah = KategoriBarang::create([
            'nama_kategori' => 'Jajanan Basah',
            'deskripsi' => 'Lemper, pastel, risoles, kue lapis, tahu bakso',
        ]);
        $katMinuman = KategoriBarang::create([
            'nama_kategori' => 'Minuman Tradisional',
            'deskripsi' => 'Sinom, beras kencur, sari jahe, es mambo',
        ]);
        $katLainnya = KategoriBarang::create([
            'nama_kategori' => 'Lainnya',
            'deskripsi' => 'Produk olahan dan kerajinan tangan anggota',
        ]);

        // 4. Create Barangs (titipan anggota)
        $barang1 = Barang::create([
            'user_id' => $anggota1->id,
            'kategori_barang_id' => $katCamilan->id,
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Kripik Pisang',
            'satuan' => 'pcs',
            'harga_beli' => 10000,
            'harga_jual' => 11500,
            'stok_saat_ini' => 8,
            'status' => 'aktif',
            'catatan' => 'Keripik pisang manis gurih khas Ketintang',
        ]);

        $barang2 = Barang::create([
            'user_id' => $anggota1->id,
            'kategori_barang_id' => $katLainnya->id,
            'kode_barang' => 'BRG-002',
            'nama_barang' => 'Sate Kelinci',
            'satuan' => 'porsi',
            'harga_beli' => 2000,
            'harga_jual' => 2500,
            'stok_saat_ini' => 9,
            'status' => 'aktif',
            'catatan' => 'Sate kelinci bumbu kacang mini',
        ]);

        $barang3 = Barang::create([
            'user_id' => $anggota2->id,
            'kategori_barang_id' => $katBasah->id,
            'kode_barang' => 'BRG-003',
            'nama_barang' => 'Pastel Ayam Sayur',
            'satuan' => 'pcs',
            'harga_beli' => 3500,
            'harga_jual' => 4500,
            'stok_saat_ini' => 2,
            'status' => 'aktif',
            'catatan' => 'Pastel renyah isi wortel & ayam',
        ]);

        $barang4 = Barang::create([
            'user_id' => $anggota2->id,
            'kategori_barang_id' => $katMinuman->id,
            'kode_barang' => 'BRG-004',
            'nama_barang' => 'Es Mambo Buah',
            'satuan' => 'pcs',
            'harga_beli' => 1500,
            'harga_jual' => 2000,
            'stok_saat_ini' => 0,
            'status' => 'aktif',
            'catatan' => 'Es lilin buah segar',
        ]);

        // Pending barang (waiting for Ketua PKK approval)
        $barang5 = Barang::create([
            'user_id' => $anggota1->id,
            'kategori_barang_id' => $katBasah->id,
            'kode_barang' => 'BRG-005',
            'nama_barang' => 'Kue Lumpur Kentang',
            'satuan' => 'pcs',
            'harga_beli' => 2000,
            'harga_jual' => 0, // Belum ditentukan ketua
            'stok_saat_ini' => 0,
            'status' => 'pending',
            'catatan' => 'Pengajuan titipan baru dari Ibu Siti',
        ]);

        // 5. Mutasi Stok Awal
        MutasiStok::create([
            'barang_id' => $barang1->id,
            'user_id' => $bendahara->id,
            'jenis_mutasi' => 'masuk',
            'jumlah' => 10,
            'tanggal' => now()->subDays(2),
            'keterangan' => 'Penerimaan awal titipan 10 pcs dari Ibu Siti',
        ]);

        MutasiStok::create([
            'barang_id' => $barang2->id,
            'user_id' => $bendahara->id,
            'jenis_mutasi' => 'masuk',
            'jumlah' => 10,
            'tanggal' => now()->subDays(2),
            'keterangan' => 'Penerimaan awal titipan 10 porsi dari Ibu Siti',
        ]);

        MutasiStok::create([
            'barang_id' => $barang3->id,
            'user_id' => $bendahara->id,
            'jenis_mutasi' => 'masuk',
            'jumlah' => 5,
            'tanggal' => now()->subDays(1),
            'keterangan' => 'Penerimaan awal titipan 5 pcs dari Ibu Rini',
        ]);

        // 6. Transaksi Penjualan Kasir (Simulasi penjualan)
        $trx1 = TransaksiPenjualan::create([
            'kode_transaksi' => 'TRX-'.now()->format('Ymd').'-0001',
            'user_id' => $bendahara->id,
            'tanggal' => now()->subDay(),
            'total_nominal' => 25500,
            'bayar' => 30000,
            'kembalian' => 4500,
            'metode_pembayaran' => 'tunai',
            'catatan' => 'Penjualan stan bazar PKK',
        ]);

        TransaksiPenjualanDetail::create([
            'transaksi_penjualan_id' => $trx1->id,
            'barang_id' => $barang1->id,
            'harga_beli' => 10000,
            'harga_jual' => 11500,
            'jumlah' => 2,
            'subtotal' => 23000,
            'laba_umkm' => 3000,
            'hak_anggota' => 20000,
        ]);

        TransaksiPenjualanDetail::create([
            'transaksi_penjualan_id' => $trx1->id,
            'barang_id' => $barang2->id,
            'harga_beli' => 2000,
            'harga_jual' => 2500,
            'jumlah' => 1,
            'subtotal' => 2500,
            'laba_umkm' => 500,
            'hak_anggota' => 2000,
        ]);

        // Catat mutasi keluar penjualan
        MutasiStok::create([
            'barang_id' => $barang1->id,
            'user_id' => $bendahara->id,
            'jenis_mutasi' => 'penjualan',
            'jumlah' => 2,
            'tanggal' => now()->subDay(),
            'keterangan' => 'Penjualan kasir #TRX-'.now()->format('Ymd').'-0001',
        ]);

        MutasiStok::create([
            'barang_id' => $barang2->id,
            'user_id' => $bendahara->id,
            'jenis_mutasi' => 'penjualan',
            'jumlah' => 1,
            'tanggal' => now()->subDay(),
            'keterangan' => 'Penjualan kasir #TRX-'.now()->format('Ymd').'-0001',
        ]);

        // 7. Transaksi Kas Umum
        TransaksiKas::create([
            'user_id' => $bendahara->id,
            'kategori_kas_id' => $katKasMasuk1->id,
            'tanggal' => now()->subDays(5),
            'nominal' => 500000,
            'keterangan' => 'Saldo awal kas kasir & kas usaha PKK Ketintang',
            'bukti_nota' => null,
        ]);

        TransaksiKas::create([
            'user_id' => $bendahara->id,
            'kategori_kas_id' => $katKasKeluar3->id,
            'tanggal' => now()->subDays(3),
            'nominal' => 35000,
            'keterangan' => 'Beli plastik kemasan dan mika kue di Pasar Ketintang',
            'bukti_nota' => null,
        ]);

        // 8. Pengaturan Default QRIS (Diatur oleh Ketua)
        Pengaturan::setValue('qris_payload', Pengaturan::DEFAULT_QRIS_PAYLOAD, 'Payload base string QRIS Statis PKK Ketintang');
        Pengaturan::setValue('qris_merchant_name', Pengaturan::DEFAULT_MERCHANT_NAME, 'Nama Merchant QRIS');
        Pengaturan::setValue('qris_nmid', Pengaturan::DEFAULT_NMID, 'NMID Resmi QRIS PKK');
        Pengaturan::setValue('qris_kota', Pengaturan::DEFAULT_KOTA, 'Kota Merchant');
    }
}
