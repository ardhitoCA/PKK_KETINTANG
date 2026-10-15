<?php

namespace App\Http\Controllers;

use App\Models\PencairanAnggota;
use App\Models\TransaksiKas;
use App\Models\TransaksiPenjualan;
use App\Models\TransaksiPenjualanDetail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanController extends Controller
{
    // === 1. Rekap Bagi Hasil UMKM (Gambar 3.18) ===
    public function rekapBagiHasil(): View
    {
        $totalOmsetKotor = TransaksiPenjualanDetail::sum('subtotal');
        $totalHakAnggota = TransaksiPenjualanDetail::sum('hak_anggota');
        $totalLabaPkk = TransaksiPenjualanDetail::sum('laba_umkm');

        $totalSudahDicairkan = PencairanAnggota::where('status', 'selesai')->sum('total_pencairan');

        // Rekap per anggota pemilik barang
        $anggotaList = User::where('role', 'anggota')
            ->with(['barangs.penjualanDetails'])
            ->get()
            ->map(function ($anggota) {
                $details = TransaksiPenjualanDetail::whereHas('barang', function ($q) use ($anggota) {
                    $q->where('user_id', $anggota->id);
                })->get();

                $barangTerjual = $details->sum('jumlah');
                $masukKasPkk = $details->sum('laba_umkm');
                $totalHak = $details->sum('hak_anggota');

                $sudahCair = PencairanAnggota::where('user_id', $anggota->id)
                    ->where('status', 'selesai')
                    ->sum('total_pencairan');

                $sisaBelumCair = max(0, $totalHak - $sudahCair);

                return [
                    'user' => $anggota,
                    'barang_terjual' => $barangTerjual,
                    'masuk_kas_pkk' => $masukKasPkk,
                    'total_hak' => $totalHak,
                    'sudah_cair' => $sudahCair,
                    'sisa_belum_cair' => $sisaBelumCair,
                ];
            });

        return view('laporan.rekap_bagi_hasil', compact(
            'totalOmsetKotor',
            'totalHakAnggota',
            'totalLabaPkk',
            'totalSudahDicairkan',
            'anggotaList'
        ));
    }

    public function prosesCairkanUang(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'nominal' => ['required', 'numeric', 'min:100'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        PencairanAnggota::create([
            'user_id' => $validated['user_id'],
            'bendahara_id' => Auth::id(),
            'tanggal' => now(),
            'total_pencairan' => $validated['nominal'],
            'status' => 'selesai',
            'keterangan' => $validated['keterangan'] ?? 'Pencairan hasil penjualan konsinyasi UMKM PKK',
        ]);

        return redirect()->route('laporan.bagi_hasil')->with('success', 'Dana hak anggota berhasil dicairkan sebesar Rp '.number_format($validated['nominal'], 0, ',', '.').'!');
    }

    // === 2. Laporan Keuangan Lengkap & Cetak ===
    public function laporanKeuangan(): View
    {
        $totalPenjualan = TransaksiPenjualan::sum('total_nominal');
        $totalModalAnggota = TransaksiPenjualanDetail::sum('hak_anggota');
        $labaUsahaPkk = TransaksiPenjualanDetail::sum('laba_umkm');

        $kasMasukLain = TransaksiKas::whereHas('kategori', function ($q) {
            $q->where('jenis', 'masuk');
        })->sum('nominal');

        $kasKeluar = TransaksiKas::whereHas('kategori', function ($q) {
            $q->where('jenis', 'keluar');
        })->sum('nominal');

        $saldoAkhir = ($labaUsahaPkk + $kasMasukLain) - $kasKeluar;

        $riwayatTransaksiKas = TransaksiKas::with('kategori')->orderBy('tanggal', 'desc')->take(20)->get();
        $riwayatPenjualan = TransaksiPenjualan::with('details.barang')->orderBy('tanggal', 'desc')->take(20)->get();

        return view('laporan.keuangan', compact(
            'totalPenjualan',
            'totalModalAnggota',
            'labaUsahaPkk',
            'kasMasukLain',
            'kasKeluar',
            'saldoAkhir',
            'riwayatTransaksiKas',
            'riwayatPenjualan'
        ));
    }

    public function cetakLaporan(): View
    {
        $totalPenjualan = TransaksiPenjualan::sum('total_nominal');
        $totalModalAnggota = TransaksiPenjualanDetail::sum('hak_anggota');
        $labaUsahaPkk = TransaksiPenjualanDetail::sum('laba_umkm');

        $kasMasukLain = TransaksiKas::whereHas('kategori', function ($q) {
            $q->where('jenis', 'masuk');
        })->sum('nominal');

        $kasKeluar = TransaksiKas::whereHas('kategori', function ($q) {
            $q->where('jenis', 'keluar');
        })->sum('nominal');

        $saldoAkhir = ($labaUsahaPkk + $kasMasukLain) - $kasKeluar;

        $daftarKas = TransaksiKas::with('kategori')->orderBy('tanggal', 'asc')->get();
        $daftarPenjualan = TransaksiPenjualan::with('details.barang')->orderBy('tanggal', 'asc')->get();

        return view('laporan.cetak', compact(
            'totalPenjualan',
            'totalModalAnggota',
            'labaUsahaPkk',
            'kasMasukLain',
            'kasKeluar',
            'saldoAkhir',
            'daftarKas',
            'daftarPenjualan'
        ));
    }
}
