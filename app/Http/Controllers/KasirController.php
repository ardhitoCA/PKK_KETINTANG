<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\MutasiStok;
use App\Models\TransaksiPenjualan;
use App\Models\TransaksiPenjualanDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KasirController extends Controller
{
    public function index(): View
    {
        $barangs = Barang::where('status', 'aktif')
            ->where('stok_saat_ini', '>', 0)
            ->with(['user', 'kategori'])
            ->orderBy('nama_barang', 'asc')
            ->get();

        return view('kasir.index', compact('barangs'));
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'string'], // JSON string of [{id, qty}]
            'bayar' => ['required', 'numeric', 'min:0'],
            'metode_pembayaran' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $cartItems = json_decode($validated['items'], true);

        if (! is_array($cartItems) || empty($cartItems)) {
            return back()->with('error', 'Keranjang belanja masih kosong.');
        }

        try {
            $transaksi = DB::transaction(function () use ($cartItems, $validated) {
                $totalNominal = 0;
                $detailsToInsert = [];
                $barangUpdates = [];

                foreach ($cartItems as $cartItem) {
                    $barang = Barang::lockForUpdate()->find($cartItem['id']);

                    if (! $barang) {
                        throw new \Exception('Barang dengan ID '.$cartItem['id'].' tidak ditemukan.');
                    }

                    $qty = (int) $cartItem['qty'];
                    if ($qty <= 0) {
                        continue;
                    }

                    if ($barang->stok_saat_ini < $qty) {
                        throw new \Exception('Stok untuk '.$barang->nama_barang.' tidak mencukupi (sisa: '.$barang->stok_saat_ini.').');
                    }

                    $subtotal = $barang->harga_jual * $qty;
                    $hakAnggota = $barang->harga_beli * $qty;
                    $labaUmkm = ($barang->harga_jual - $barang->harga_beli) * $qty;

                    $totalNominal += $subtotal;

                    $detailsToInsert[] = [
                        'barang' => $barang,
                        'qty' => $qty,
                        'harga_beli' => $barang->harga_beli,
                        'harga_jual' => $barang->harga_jual,
                        'subtotal' => $subtotal,
                        'laba_umkm' => $labaUmkm,
                        'hak_anggota' => $hakAnggota,
                    ];

                    $barangUpdates[] = [
                        'barang' => $barang,
                        'qty' => $qty,
                    ];
                }

                $uangBayar = (float) $validated['bayar'];
                if ($uangBayar < $totalNominal) {
                    throw new \Exception('Uang bayar (Rp '.number_format($uangBayar, 0, ',', '.').') kurang dari total belanja (Rp '.number_format($totalNominal, 0, ',', '.').').');
                }

                $kembalian = $uangBayar - $totalNominal;

                // Buat Kode Transaksi
                $kodeTrx = 'TRX-'.now()->format('Ymd').'-'.str_pad((string) (TransaksiPenjualan::count() + 1), 4, '0', STR_PAD_LEFT);

                $transaksiBaru = TransaksiPenjualan::create([
                    'kode_transaksi' => $kodeTrx,
                    'user_id' => Auth::id(),
                    'tanggal' => now(),
                    'total_nominal' => $totalNominal,
                    'bayar' => $uangBayar,
                    'kembalian' => $kembalian,
                    'metode_pembayaran' => $validated['metode_pembayaran'] ?? 'tunai',
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                // Simpan Detail & Update Stok & Mutasi
                foreach ($detailsToInsert as $detail) {
                    TransaksiPenjualanDetail::create([
                        'transaksi_penjualan_id' => $transaksiBaru->id,
                        'barang_id' => $detail['barang']->id,
                        'harga_beli' => $detail['harga_beli'],
                        'harga_jual' => $detail['harga_jual'],
                        'jumlah' => $detail['qty'],
                        'subtotal' => $detail['subtotal'],
                        'laba_umkm' => $detail['laba_umkm'],
                        'hak_anggota' => $detail['hak_anggota'],
                    ]);

                    // Potong stok otomatis
                    $detail['barang']->decrement('stok_saat_ini', $detail['qty']);

                    // Catat mutasi stok
                    MutasiStok::create([
                        'barang_id' => $detail['barang']->id,
                        'user_id' => Auth::id(),
                        'jenis_mutasi' => 'penjualan',
                        'jumlah' => $detail['qty'],
                        'tanggal' => now(),
                        'keterangan' => 'Penjualan kasir transaksi #'.$kodeTrx,
                    ]);
                }

                return $transaksiBaru;
            });

            return redirect()->route('kasir.sukses', $transaksi->id)->with('success', 'Transaksi berhasil disimpan! Stok barang otomatis terpotong.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function sukses(TransaksiPenjualan $transaksi): View
    {
        $transaksi->load(['details.barang.user', 'user']);

        return view('kasir.sukses', compact('transaksi'));
    }
}
