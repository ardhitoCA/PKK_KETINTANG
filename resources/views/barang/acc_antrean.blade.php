@extends('layouts.app')

@section('title', 'Antrean ACC Barang - Ketua PKK')
@section('header_title', 'Antrean ACC Barang')
@section('back_url', route('dashboard'))

@section('content')
<div class="space-y-4">
    <!-- Header Summary -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-800">Menunggu Persetujuan</h3>
            <p class="text-xs text-slate-500">Tinjau dan tentukan harga jual etalase</p>
        </div>
        <span class="text-xs font-bold px-3 py-1 bg-rose-100 text-rose-700 rounded-full border border-rose-200">
            {{ $antrean->count() }} Permintaan
        </span>
    </div>

    <!-- Antrean List (Gambar 3.13) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @forelse ($antrean as $item)
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-2xl shrink-0">
                        📦
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-sm font-bold text-slate-800 truncate leading-tight">{{ $item->nama_barang }}</h4>
                        <p class="text-xs text-slate-500 truncate mt-0.5">Dari: <span class="font-semibold text-slate-700">{{ $item->user->name ?? 'Anggota' }}</span></p>
                        <p class="text-xs text-emerald-700 font-bold mt-0.5">
                            Modal: Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Action Button ACC (Gambar 3.13) -->
                <button type="button" onclick="openAccModal({{ $item->id }}, '{{ addslashes($item->nama_barang) }}', {{ $item->harga_beli }}, '{{ addslashes($item->user->name ?? 'Anggota') }}')"
                    class="px-4 py-2 bg-sky-500 hover:bg-sky-600 active:bg-sky-700 text-white font-bold text-xs rounded-xl shadow-md shadow-sky-500/20 transition shrink-0 active:scale-95">
                    ACC
                </button>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 border border-slate-100 text-center shadow-sm">
                <span class="text-4xl block mb-2">🎉</span>
                <p class="text-sm font-bold text-slate-700">Semua Pengajuan Telah Disetujui</p>
                <p class="text-xs text-slate-400 mt-1">Saat ini belum ada pengajuan barang baru yang pending dari anggota.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Form ACC (Tentukan Harga Jual Etalase) -->
<div id="acc-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl animate-fade-in relative border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-extrabold text-slate-800">Persetujuan (ACC) Barang</h3>
            <button type="button" onclick="closeAccModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">✕</button>
        </div>

        <form id="acc-form" method="POST" action="" class="space-y-4">
            @csrf

            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/80 text-xs">
                <p class="text-slate-500">Nama Produk: <strong id="modal-nama-barang" class="text-slate-800 font-bold"></strong></p>
                <p class="text-slate-500 mt-1">Diajukan oleh: <span id="modal-nama-anggota" class="font-semibold text-slate-700"></span></p>
                <p class="text-slate-500 mt-1">Harga Modal: <strong id="modal-harga-modal" class="text-emerald-700 font-bold"></strong></p>
            </div>

            <!-- Input Harga Jual -->
            <div>
                <label for="harga_jual" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">
                    Tentukan Harga Jual Etalase (Rp)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" id="harga_jual" name="harga_jual" required min="100" step="500"
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-extrabold text-emerald-700">
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Selisih antara harga jual dan modal menjadi laba kas usaha PKK.</p>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <button type="button" onclick="closeAccModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 bg-sky-500 hover:bg-sky-600 active:bg-sky-700 text-white font-bold rounded-2xl text-xs shadow-md shadow-sky-500/20 transition">
                    Setujui & Aktifkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAccModal(id, nama, modal, anggota) {
        document.getElementById('acc-form').action = `/acc-barang/${id}`;
        document.getElementById('modal-nama-barang').innerText = nama;
        document.getElementById('modal-nama-anggota').innerText = anggota;
        document.getElementById('modal-harga-modal').innerText = 'Rp ' + modal.toLocaleString('id-ID');
        document.getElementById('harga_jual').value = modal; // default set to modal or slightly higher
        document.getElementById('acc-modal').classList.remove('hidden');
    }

    function closeAccModal() {
        document.getElementById('acc-modal').classList.add('hidden');
    }
</script>
@endsection
