<?php

namespace App\Http\Controllers;

use App\Models\KategoriKas;
use App\Models\TransaksiKas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KasController extends Controller
{
    public function index(): View
    {
        $kategoriList = KategoriKas::orderBy('jenis')->orderBy('nama_kategori')->get();

        $transaksiList = TransaksiKas::with(['kategori', 'user'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $totalMasuk = TransaksiKas::whereHas('kategori', function ($q) {
            $q->where('jenis', 'masuk');
        })->sum('nominal');

        $totalKeluar = TransaksiKas::whereHas('kategori', function ($q) {
            $q->where('jenis', 'keluar');
        })->sum('nominal');

        $saldoKas = $totalMasuk - $totalKeluar;

        return view('kas.index', compact('kategoriList', 'transaksiList', 'totalMasuk', 'totalKeluar', 'saldoKas'));
    }

    public function create(): View
    {
        $kategoriList = KategoriKas::orderBy('jenis')->orderBy('nama_kategori')->get();

        return view('kas.create', compact('kategoriList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kategori_kas_id' => ['required', 'exists:kategori_kas,id'],
            'nominal' => ['required', 'numeric', 'min:500'],
            'keterangan' => ['required', 'string', 'max:500'],
            'tanggal' => ['nullable', 'date'],
            'bukti_nota' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ]);

        $buktiNotaPath = null;
        if ($request->hasFile('bukti_nota')) {
            $buktiNotaPath = $request->file('bukti_nota')->store('bukti_nota', 'public');
        }

        TransaksiKas::create([
            'user_id' => Auth::id(),
            'kategori_kas_id' => $validated['kategori_kas_id'],
            'tanggal' => $validated['tanggal'] ?? now(),
            'nominal' => $validated['nominal'],
            'keterangan' => $validated['keterangan'],
            'bukti_nota' => $buktiNotaPath,
        ]);

        return redirect()->route('kas.index')->with('success', 'Transaksi kas berhasil dicatat dan saldo kas diperbarui.');
    }
}
