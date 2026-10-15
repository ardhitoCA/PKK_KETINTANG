<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\MutasiStok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BarangController extends Controller
{
    // === 1. FITUR ANGGOTA: Pengajuan Barang Baru ===
    public function pengajuanForm(): View
    {
        $kategoriList = KategoriBarang::orderBy('nama_kategori')->get();

        return view('barang.pengajuan', compact('kategoriList'));
    }

    public function storePengajuan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_barang' => ['required', 'string', 'max:150'],
            'kategori_barang_id' => ['required', 'exists:kategori_barangs,id'],
            'harga_beli' => ['required', 'numeric', 'min:100'],
            'satuan' => ['nullable', 'string', 'max:20'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        // Auto-generate kode barang unik
        $lastId = Barang::max('id') ?? 0;
        $kodeBarang = 'BRG-'.str_pad((string) ($lastId + 1), 3, '0', STR_PAD_LEFT);

        Barang::create([
            'user_id' => Auth::id(),
            'kategori_barang_id' => $validated['kategori_barang_id'],
            'kode_barang' => $kodeBarang,
            'nama_barang' => $validated['nama_barang'],
            'satuan' => $validated['satuan'] ?? 'pcs',
            'harga_beli' => $validated['harga_beli'],
            'harga_jual' => 0, // Akan ditentukan oleh Ketua PKK saat ACC
            'stok_saat_ini' => 0,
            'status' => 'pending',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('dashboard')->with('success', 'Barang berhasil diajukan dan sedang menunggu persetujuan (ACC) Ketua PKK.');
    }

    // === 2. FITUR ANGGOTA: Riwayat Penjualan Saya ===
    public function riwayatSaya(): View
    {
        $user = Auth::user();

        $barangSaya = Barang::where('user_id', $user->id)
            ->with(['kategori', 'penjualanDetails'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                $totalTerjual = $item->penjualanDetails->sum('jumlah');
                $totalHakUang = $item->penjualanDetails->sum('hak_anggota');

                return [
                    'barang' => $item,
                    'terjual' => $totalTerjual,
                    'sisa_stok' => $item->stok_saat_ini,
                    'total_hak' => $totalHakUang,
                    'status_label' => $item->status === 'aktif' ? 'Di etalase' : ($item->status === 'pending' ? 'Pending ACC' : ucfirst($item->status)),
                ];
            });

        $totalSemuaTerjual = $barangSaya->sum('terjual');
        $totalSemuaHak = $barangSaya->sum('total_hak');

        return view('barang.riwayat_saya', compact('barangSaya', 'totalSemuaTerjual', 'totalSemuaHak'));
    }

    // === 3. FITUR KETUA PKK: Antrean ACC Barang ===
    public function antreanAcc(): View
    {
        $antrean = Barang::where('status', 'pending')
            ->with(['user', 'kategori'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('barang.acc_antrean', compact('antrean'));
    }

    public function prosesAcc(Request $request, Barang $barang): RedirectResponse
    {
        $validated = $request->validate([
            'harga_jual' => ['required', 'numeric', 'gte:'.$barang->harga_beli],
            'catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'harga_jual.gte' => 'Harga jual harus lebih besar atau sama dengan harga modal anggota (Rp '.number_format($barang->harga_beli, 0, ',', '.').').',
        ]);

        $barang->update([
            'harga_jual' => $validated['harga_jual'],
            'status' => 'aktif',
            'catatan' => $validated['catatan'] ?? $barang->catatan,
        ]);

        return redirect()->route('barang.acc_antrean')->with('success', 'Barang "'.$barang->nama_barang.'" berhasil di-ACC dengan harga jual Rp '.number_format($validated['harga_jual'], 0, ',', '.').' dan kini aktif di etalase!');
    }

    public function tolakAcc(Request $request, Barang $barang): RedirectResponse
    {
        $request->validate([
            'alasan_penolakan' => ['required', 'string', 'max:255'],
        ]);

        $barang->update([
            'status' => 'ditolak',
            'catatan' => 'Ditolak: '.$request->input('alasan_penolakan'),
        ]);

        return redirect()->route('barang.acc_antrean')->with('info', 'Barang "'.$barang->nama_barang.'" telah ditolak.');
    }

    // === 4. FITUR BENDAHARA: Kelola Stok Barang Titipan ===
    public function kelolaStok(): View
    {
        $barangs = Barang::where('status', 'aktif')
            ->with(['user', 'kategori'])
            ->orderBy('nama_barang', 'asc')
            ->get();

        return view('barang.kelola_stok', compact('barangs'));
    }

    public function tambahStok(Request $request, Barang $barang): RedirectResponse
    {
        $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $barang) {
            $jumlah = (int) $request->input('jumlah');

            $barang->increment('stok_saat_ini', $jumlah);

            MutasiStok::create([
                'barang_id' => $barang->id,
                'user_id' => Auth::id(),
                'jenis_mutasi' => 'masuk',
                'jumlah' => $jumlah,
                'tanggal' => now(),
                'keterangan' => $request->input('keterangan') ?: ('Penambahan stok fisik masuk dari '.($barang->user->name ?? 'anggota')),
            ]);
        });

        return redirect()->route('barang.kelola_stok')->with('success', 'Stok '.$barang->nama_barang.' berhasil ditambah '.$request->input('jumlah').' '.$barang->satuan.'.');
    }

    // === 5. FITUR BENDAHARA: Retur Sisa Barang ===
    public function returIndex(): View
    {
        $barangs = Barang::where('status', 'aktif')
            ->where('stok_saat_ini', '>', 0)
            ->with(['user', 'kategori'])
            ->orderBy('nama_barang', 'asc')
            ->get();

        return view('barang.retur', compact('barangs'));
    }

    public function prosesRetur(Request $request, Barang $barang): RedirectResponse
    {
        $request->validate([
            'jumlah_retur' => ['required', 'integer', 'min:1', 'max:'.$barang->stok_saat_ini],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $barang) {
            $jumlah = (int) $request->input('jumlah_retur');

            $barang->decrement('stok_saat_ini', $jumlah);

            MutasiStok::create([
                'barang_id' => $barang->id,
                'user_id' => Auth::id(),
                'jenis_mutasi' => 'retur',
                'jumlah' => $jumlah,
                'tanggal' => now(),
                'keterangan' => $request->input('keterangan') ?: ('Retur sisa barang tidak terjual dikembalikan ke '.($barang->user->name ?? 'anggota')),
            ]);
        });

        return redirect()->route('barang.retur')->with('success', 'Berhasil meretur '.$request->input('jumlah_retur').' '.$barang->satuan.' '.$barang->nama_barang.' kepada pemilik barang.');
    }
}
