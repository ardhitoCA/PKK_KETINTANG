@extends('layouts.app')

@section('title', 'Input Transaksi Kas - Bendahara')
@section('header_title', 'Input Transaksi Kas')
@section('back_url', route('kas.index'))

@section('content')
<div class="space-y-4">
    <!-- Subtitle description -->
    <p class="text-xs text-slate-500">
        Catat arus kas masuk atau keluar di luar penjualan (seperti kas operasional, listrik, perlengkapan, dan iuran).
    </p>

    <!-- Form Container (Gambar 3.14) -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
        <form action="{{ route('kas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Kategori Transaksi -->
            <div>
                <label for="kategori_kas_id" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Kategori Transaksi
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        🏷️
                    </span>
                    <select id="kategori_kas_id" name="kategori_kas_id" required
                        class="w-full pl-11 pr-8 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition appearance-none">
                        <option value="">-- Pilih Kategori Kas --</option>
                        <optgroup label="Pemasukan Kas">
                            @foreach ($kategoriList->where('jenis', 'masuk') as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_kas_id') == $kat->id ? 'selected' : '' }}>
                                    (+) {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Pengeluaran Kas">
                            @foreach ($kategoriList->where('jenis', 'keluar') as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_kas_id') == $kat->id ? 'selected' : '' }}>
                                    (-) {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400 text-xs">
                        ▼
                    </span>
                </div>
            </div>

            <!-- Nominal (Rp) -->
            <div>
                <label for="nominal" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Nominal (Rp)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" id="nominal" name="nominal" value="{{ old('nominal') }}" required min="500" step="500"
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800"
                        placeholder="50.000">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Keterangan
                </label>
                <textarea id="keterangan" name="keterangan" rows="3" required
                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                    placeholder="Contoh: Pembayaran listrik stan bazar bulan Oktober">{{ old('keterangan') }}</textarea>
            </div>

            <!-- Upload Bukti Nota (Opsional) (Gambar 3.14) -->
            <div>
                <label for="bukti_nota" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Upload Bukti Nota (Opsional)
                </label>
                <div class="relative">
                    <input type="file" id="bukti_nota" name="bukti_nota" accept="image/*"
                        class="w-full p-2.5 bg-slate-50 border border-dashed border-slate-300 rounded-2xl text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Format foto struk: JPG, PNG, atau WEBP (Maks 3MB).</p>
            </div>

            <!-- Submit Button (Gambar 3.14) -->
            <button type="submit"
                class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-2xl shadow-lg shadow-emerald-600/30 transition flex items-center justify-center space-x-2 text-sm mt-3 active:scale-95">
                <span>Simpan Transaksi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </button>
        </form>
    </div>
</div>
@endsection
