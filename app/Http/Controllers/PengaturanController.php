<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    /**
     * Tampilkan formulir pengaturan QRIS Toko PKK (khusus Ketua PKK).
     */
    public function qrisIndex(): View
    {
        $config = Pengaturan::getQrisConfig();

        return view('pengaturan.qris', compact('config'));
    }

    /**
     * Simpan pembaruan QRIS oleh Ketua PKK.
     */
    public function updateQris(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'merchant_name' => ['required', 'string', 'max:100'],
            'nmid' => ['required', 'string', 'max:60'],
            'kota' => ['required', 'string', 'max:50'],
            'payload' => ['required', 'string'],
        ], [
            'merchant_name.required' => 'Nama merchant wajib diisi.',
            'nmid.required' => 'NMID QRIS wajib diisi.',
            'kota.required' => 'Kota merchant wajib diisi.',
            'payload.required' => 'String base payload QRIS statis wajib diisi.',
        ]);

        $cleanPayload = trim(str_replace(["\r", "\n", "\t"], '', $validated['payload']));

        // Validasi dasar EMVCo (harus berawalan 000201)
        if (! str_starts_with($cleanPayload, '000201')) {
            return back()
                ->withInput()
                ->with('error', 'Payload QRIS tidak valid. Format QRIS standar Bank Indonesia harus diawali dengan "000201".');
        }

        Pengaturan::setValue('qris_merchant_name', strtoupper(trim($validated['merchant_name'])), 'Nama Merchant Resmi QRIS');
        Pengaturan::setValue('qris_nmid', trim($validated['nmid']), 'NMID QRIS Resmi');
        Pengaturan::setValue('qris_kota', strtoupper(trim($validated['kota'])), 'Kota Merchant');
        Pengaturan::setValue('qris_payload', $cleanPayload, 'String Base Payload QRIS Statis PKK');

        return back()->with('success', 'Pengaturan QRIS resmi PKK berhasil diperbarui oleh Ketua!');
    }

    /**
     * Kembalikan konfigurasi QRIS ke default bawaan PKK Ketintang.
     */
    public function resetQris(): RedirectResponse
    {
        Pengaturan::setValue('qris_merchant_name', Pengaturan::DEFAULT_MERCHANT_NAME, 'Nama Merchant Resmi QRIS');
        Pengaturan::setValue('qris_nmid', Pengaturan::DEFAULT_NMID, 'NMID QRIS Resmi');
        Pengaturan::setValue('qris_kota', Pengaturan::DEFAULT_KOTA, 'Kota Merchant');
        Pengaturan::setValue('qris_payload', Pengaturan::DEFAULT_QRIS_PAYLOAD, 'String Base Payload QRIS Statis PKK');

        return back()->with('success', 'Pengaturan QRIS berhasil dikembalikan ke standar default PKK Kelurahan Ketintang.');
    }
}
