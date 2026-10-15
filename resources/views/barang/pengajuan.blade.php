@extends('layouts.app')

@section('title', 'Pengajuan Barang Baru')
@section('header_title', 'Pengajuan Barang Baru')
@section('back_url', route('dashboard'))

@section('content')
<div class="space-y-4">
    <!-- Info Banner (Gambar 3.12) -->
    <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-4 text-xs text-amber-900 flex items-start space-x-3 shadow-sm">
        <div class="w-6 h-6 rounded-full bg-amber-200/80 text-amber-800 flex items-center justify-center font-bold text-sm shrink-0">
            ℹ
        </div>
        <p class="leading-relaxed font-medium">
            Barang yang diajukan belum bisa dijual sebelum disetujui (ACC) dan ditentukan harga jualnya oleh Ketua PKK.
        </p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
        <form action="{{ route('barang.pengajuan.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nama Barang -->
            <div>
                <label for="nama_barang" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Nama Barang
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        🏷️
                    </span>
                    <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                        placeholder="Contoh: Kripik Pisang Manis">
                </div>
            </div>

            <!-- Kategori Produk (Dropdown) -->
            <div>
                <label for="kategori_barang_id" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Kategori Produk
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        📁
                    </span>
                    <select id="kategori_barang_id" name="kategori_barang_id" required
                        class="w-full pl-11 pr-8 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition appearance-none">
                        <option value="">-- Pilih Kategori Produk --</option>
                        @foreach ($kategoriList as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_barang_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400 text-xs">
                        ▼
                    </span>
                </div>
            </div>

            <!-- Satuan & Harga Setor Modal -->
            <div class="grid grid-cols-3 gap-2">
                <div class="col-span-1">
                    <label for="satuan" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Satuan
                    </label>
                    <input type="text" id="satuan" name="satuan" value="{{ old('satuan', 'pcs') }}" required
                        class="w-full px-3 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-center font-medium"
                        placeholder="pcs">
                </div>

                <div class="col-span-2">
                    <label for="harga_beli" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Harga Setor / Modal (Rp)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400 font-semibold text-xs">
                            Rp
                        </span>
                        <input type="number" id="harga_beli" name="harga_beli" value="{{ old('harga_beli') }}" required min="100" step="500"
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition font-semibold"
                            placeholder="10.000">
                    </div>
                </div>
            </div>

            <!-- Catatan Tambahan (Opsional) -->
            <div>
                <label for="catatan" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Keterangan Produk (Opsional)
                </label>
                <textarea id="catatan" name="catatan" rows="2"
                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                    placeholder="Contoh: Tahan 1 bulan, kemasan plastik 150gr">{{ old('catatan') }}</textarea>
            </div>

            <!-- Submit Button (Gambar 3.12) -->
            <button type="submit"
                class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-2xl shadow-lg shadow-emerald-600/30 transition flex items-center justify-center space-x-2 text-sm mt-3">
                <span>Ajukan Barang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </button>
        </form>
    </div>
</div>
@endsection
