<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasirCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_kasir_screen_can_be_rendered(): void
    {
        $bendahara = User::factory()->create([
            'role' => 'bendahara',
        ]);

        $response = $this->actingAs($bendahara)->get(route('kasir.index'));

        $response->assertStatus(200);
        $response->assertSee('QRIS Dinamis');
        $response->assertSee('Tunai');
    }

    public function test_bendahara_can_checkout_with_tunai(): void
    {
        $bendahara = User::factory()->create([
            'role' => 'bendahara',
        ]);

        $kategori = KategoriBarang::create(['nama_kategori' => 'Makanan']);

        $barang = Barang::create([
            'kode_barang' => 'BRG-001',
            'user_id' => $bendahara->id,
            'kategori_barang_id' => $kategori->id,
            'nama_barang' => 'Keripik Tempe',
            'harga_beli' => 8000,
            'harga_jual' => 10000,
            'stok_saat_ini' => 10,
            'status' => 'aktif',
        ]);

        $items = json_encode([
            ['id' => $barang->id, 'qty' => 2, 'harga' => 10000],
        ]);

        $response = $this->actingAs($bendahara)->post(route('kasir.checkout'), [
            'items' => $items,
            'bayar' => 50000,
            'metode_pembayaran' => 'tunai',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('transaksi_penjualans', [
            'total_nominal' => 20000,
            'bayar' => 50000,
            'kembalian' => 30000,
            'metode_pembayaran' => 'tunai',
        ]);

        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'stok_saat_ini' => 8,
        ]);
    }

    public function test_bendahara_can_checkout_with_qris(): void
    {
        $bendahara = User::factory()->create([
            'role' => 'bendahara',
        ]);

        $kategori = KategoriBarang::create(['nama_kategori' => 'Minuman']);

        $barang = Barang::create([
            'kode_barang' => 'BRG-002',
            'user_id' => $bendahara->id,
            'kategori_barang_id' => $kategori->id,
            'nama_barang' => 'Sinom Segar',
            'harga_beli' => 5000,
            'harga_jual' => 7000,
            'stok_saat_ini' => 5,
            'status' => 'aktif',
        ]);

        $items = json_encode([
            ['id' => $barang->id, 'qty' => 3, 'harga' => 7000],
        ]);

        $response = $this->actingAs($bendahara)->post(route('kasir.checkout'), [
            'items' => $items,
            'bayar' => 21000,
            'metode_pembayaran' => 'qris',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('transaksi_penjualans', [
            'total_nominal' => 21000,
            'bayar' => 21000,
            'kembalian' => 0,
            'metode_pembayaran' => 'qris',
        ]);

        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'stok_saat_ini' => 2,
        ]);
    }
}
