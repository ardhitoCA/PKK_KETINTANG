@extends('layouts.app')

@section('title', 'Retur Sisa Barang - Bendahara')
@section('header_title', 'Retur Sisa Barang')
@section('back_url', route('dashboard'))

@section('content')
<div class="space-y-4">
    <!-- Header description -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-800">Pengembalian Barang Titipan</h3>
            <p class="text-xs text-slate-500">Kembalikan produk yang tidak habis terjual</p>
        </div>
        <span class="text-xs font-bold px-3 py-1 bg-rose-100 text-rose-800 rounded-full border border-rose-200">
            {{ $barangs->count() }} Produk
        </span>
    </div>

    <!-- Barang Items List (Gambar 3.19) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @forelse ($barangs as $item)
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-2xl shrink-0">
                        🔄
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-slate-800 truncate leading-tight">{{ $item->nama_barang }}</h4>
                        <p class="text-xs font-extrabold text-slate-600 mt-0.5">
                            Sisa Stok: <span class="text-rose-600 font-black">{{ $item->stok_saat_ini }}</span> {{ $item->satuan }}
                        </p>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Pemilik: {{ $item->user->name ?? 'Anggota' }}</span>
                    </div>
                </div>

                <!-- Action Button Retur (Gambar 3.19) -->
                <button type="button" onclick="openReturModal({{ $item->id }}, '{{ addslashes($item->nama_barang) }}', {{ $item->stok_saat_ini }}, '{{ $item->satuan }}', '{{ addslashes($item->user->name ?? 'Anggota') }}')"
                    class="px-4 py-2 bg-slate-600 hover:bg-slate-700 active:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition shrink-0 active:scale-95">
                    Retur
                </button>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 border border-slate-100 text-center shadow-sm">
                <span class="text-3xl block mb-2">✨</span>
                <p class="text-xs font-bold text-slate-700">Tidak Ada Sisa Barang</p>
                <p class="text-[11px] text-slate-400 mt-1">Semua barang titipan di etalase stoknya 0 atau telah terjual habis.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Retur Sisa Barang -->
<div id="retur-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl animate-fade-in relative border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-extrabold text-slate-800">Retur Sisa Barang ke Pemilik</h3>
            <button type="button" onclick="closeReturModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">✕</button>
        </div>

        <form id="retur-form" method="POST" action="" class="space-y-4">
            @csrf

            <div class="bg-rose-50 p-3 rounded-2xl border border-rose-200/80 text-xs">
                <p class="text-rose-900">Nama Produk: <strong id="modal-nama-barang" class="font-bold"></strong></p>
                <p class="text-rose-900 mt-1">Pemilik Barang: <span id="modal-nama-pemilik" class="font-semibold"></span></p>
                <p class="text-rose-900 mt-1">Sisa Stok Fisik: <strong id="modal-sisa-stok" class="font-bold"></strong></p>
            </div>

            <!-- Input Jumlah Retur -->
            <div>
                <label for="jumlah_retur" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">
                    Jumlah Barang Dikembalikan (<span id="modal-satuan"></span>)
                </label>
                <input type="number" id="jumlah_retur" name="jumlah_retur" required min="1" step="1"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500"
                    placeholder="Contoh: 5">
            </div>

            <!-- Keterangan Tambahan -->
            <div>
                <label for="keterangan" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">
                    Keterangan (Opsional)
                </label>
                <input type="text" id="keterangan" name="keterangan"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-rose-500"
                    placeholder="Contoh: Barang diambil sore hari">
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <button type="button" onclick="closeReturModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold rounded-2xl text-xs shadow-md shadow-rose-600/20 transition">
                    Simpan Retur
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReturModal(id, nama, stok, satuan, pemilik) {
        document.getElementById('retur-form').action = `/retur-barang/${id}`;
        document.getElementById('modal-nama-barang').innerText = nama;
        document.getElementById('modal-nama-pemilik').innerText = pemilik;
        document.getElementById('modal-sisa-stok').innerText = stok + ' ' + satuan;
        document.getElementById('modal-satuan').innerText = satuan;
        document.getElementById('jumlah_retur').max = stok;
        document.getElementById('jumlah_retur').value = stok; // default retur all remaining
        document.getElementById('retur-modal').classList.remove('hidden');
    }

    function closeReturModal() {
        document.getElementById('retur-modal').classList.add('hidden');
    }
</script>
@endsection
