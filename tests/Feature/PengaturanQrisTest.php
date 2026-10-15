<?php

namespace Tests\Feature;

use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengaturanQrisTest extends TestCase
{
    use RefreshDatabase;

    public function test_ketua_can_view_pengaturan_qris_page(): void
    {
        $ketua = User::factory()->create([
            'role' => 'ketua',
        ]);

        $response = $this->actingAs($ketua)->get(route('pengaturan.qris'));

        $response->assertStatus(200);
        $response->assertSee('Master QRIS Toko PKK');
        $response->assertSee('PKK KELURAHAN KETINTANG');
    }

    public function test_bendahara_cannot_access_pengaturan_qris_page(): void
    {
        $bendahara = User::factory()->create([
            'role' => 'bendahara',
        ]);

        $response = $this->actingAs($bendahara)->get(route('pengaturan.qris'));

        // Role middleware redirects or aborts 403
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_anggota_cannot_access_pengaturan_qris_page(): void
    {
        $anggota = User::factory()->create([
            'role' => 'anggota',
        ]);

        $response = $this->actingAs($anggota)->get(route('pengaturan.qris'));

        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_ketua_can_update_qris_configuration(): void
    {
        $ketua = User::factory()->create([
            'role' => 'ketua',
        ]);

        $newPayload = '00020101021126680014ID.SEABANK.WWW01189360091100202688880215ID10202688800010303UMI51450015ID.OR.GPN.WWW0215ID10202688800010303UMI5204541153033605802ID5920WARUNG PKK KETINTANG6008SURABAYA61056023162070703A016304';

        $response = $this->actingAs($ketua)->post(route('pengaturan.qris.update'), [
            'merchant_name' => 'WARUNG PKK KETINTANG',
            'nmid' => 'ID9999999999',
            'kota' => 'SURABAYA',
            'payload' => $newPayload,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $this->assertEquals('WARUNG PKK KETINTANG', Pengaturan::getValue('qris_merchant_name'));
        $this->assertEquals('ID9999999999', Pengaturan::getValue('qris_nmid'));
        $this->assertEquals($newPayload, Pengaturan::getValue('qris_payload'));
    }

    public function test_ketua_cannot_save_invalid_qris_payload(): void
    {
        $ketua = User::factory()->create([
            'role' => 'ketua',
        ]);

        $response = $this->actingAs($ketua)->post(route('pengaturan.qris.update'), [
            'merchant_name' => 'PKK KETINTANG',
            'nmid' => 'ID123',
            'kota' => 'SURABAYA',
            'payload' => 'INVALID_PAYLOAD_STRING',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_ketua_can_reset_qris_to_default(): void
    {
        $ketua = User::factory()->create([
            'role' => 'ketua',
        ]);

        Pengaturan::setValue('qris_merchant_name', 'TOKO SEMENTARA');

        $response = $this->actingAs($ketua)->post(route('pengaturan.qris.reset'));

        $response->assertSessionHas('success');
        $this->assertEquals(Pengaturan::DEFAULT_MERCHANT_NAME, Pengaturan::getValue('qris_merchant_name'));
    }

    public function test_kasir_uses_qris_configured_by_ketua(): void
    {
        Pengaturan::setValue('qris_merchant_name', 'PKK HEBAT KETINTANG');
        Pengaturan::setValue('qris_nmid', 'ID888888888');

        $bendahara = User::factory()->create([
            'role' => 'bendahara',
        ]);

        $response = $this->actingAs($bendahara)->get(route('kasir.index'));

        $response->assertStatus(200);
        $response->assertSee('PKK HEBAT KETINTANG');
        $response->assertSee('NMID: ID888888888');
    }
}
