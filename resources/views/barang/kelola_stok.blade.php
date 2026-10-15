@extends('layouts.app')

@section('title', 'Kelola Stok Barang - Bendahara')
@section('header_title', 'Kelola Stok Barang')
@section('back_url', route('dashboard'))

@section('content')
<div class="space-y-4">
    <!-- Header description -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-800">Stok Barang di Etalase</h3>
            <p class="text-xs text-slate-500">Pembaruan saat fisik barang titipan masuk</p>
        </div>
        <span class="text-xs font-bold px-3 py-1 bg-amber-100 text-amber-800 rounded-full border border-amber-200">
            {{ $barangs->count() }} Produk Aktif
        </span>
    </div>

    <!-- Barang Items List (Gambar 3.16) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @forelse ($barangs as $item)
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-800 truncate leading-tight">{{ $item->nama_barang }}</h4>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">Kategori: {{ $item->kategori->nama_kategori ?? 'Lainnya' }}</p>
                    <p class="text-xs font-extrabold text-slate-700 mt-0.5">
                        Stok Saat Ini: <span class="text-emerald-700 font-black">{{ $item->stok_saat_ini }}</span> {{ $item->satuan }}
                    </p>
                    <span class="text-[10px] text-slate-400 block mt-0.5">Pemilik: {{ $item->user->name ?? 'Anggota' }}</span>
                </div>

                <!-- Button + Stok (Gambar 3.16) -->
                <button type="button" onclick="openTambahStokModal({{ $item->id }}, '{{ addslashes($item->nama_barang) }}', {{ $item->stok_saat_ini }}, '{{ $item->satuan }}')"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition shrink-0 active:scale-95 border border-slate-200">
                    + Stok
                </button>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 border border-slate-100 text-center shadow-sm">
                <span class="text-3xl block mb-2">📦</span>
                <p class="text-xs font-bold text-slate-700">Belum Ada Barang di Etalase</p>
                <p class="text-[11px] text-slate-400 mt-1">Barang titipan baru harus disetujui (ACC) oleh ketua terlebih dahulu.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Stok Fisik -->
<div id="stok-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl animate-fade-in relative border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-extrabold text-slate-800">Tambah Stok Fisik Masuk</h3>
            <button type="button" onclick="closeTambahStokModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">✕</button>
        </div>

        <form id="stok-form" method="POST" action="" class="space-y-4">
            @csrf

            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/80 text-xs">
                <p class="text-slate-500">Nama Produk: <strong id="modal-nama-barang" class="text-slate-800 font-bold"></strong></p>
                <p class="text-slate-500 mt-1">Stok Saat Ini: <strong id="modal-stok-sekarang" class="text-emerald-700 font-bold"></strong></p>
            </div>

            <!-- Input Jumlah Fisik Masuk -->
            <div>
                <label for="jumlah" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">
                    Jumlah Masuk (<span id="modal-satuan"></span>)
                </label>
                <input type="number" id="jumlah" name="jumlah" required min="1" step="1"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Contoh: 10">
            </div>

            <!-- Keterangan Tambahan -->
            <div>
                <label for="keterangan" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">
                    Keterangan (Opsional)
                </label>
                <input type="text" id="keterangan" name="keterangan"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Contoh: Kiriman titipan pagi">
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <button type="button" onclick="closeTambahStokModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-2xl text-xs shadow-md shadow-emerald-600/20 transition">
                    Simpan Stok
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTambahStokModal(id, nama, stok, satuan) {
        document.getElementById('stok-form').action = `/kelola-stok/${id}/tambah`;
        document.getElementById('modal-nama-barang').innerText = nama;
        document.getElementById('modal-stok-sekarang').innerText = stok + ' ' + satuan;
        document.getElementById('modal-satuan').innerText = satuan;
        document.getElementById('jumlah').value = '';
        document.getElementById('stok-modal').classList.remove('hidden');
    }

    function closeTambahStokModal() {
        document.getElementById('stok-modal').classList.add('hidden');
    }
</script>
@endsection
