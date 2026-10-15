@extends('layouts.app')

@section('title', 'Rekap Bagi Hasil UMKM')
@section('header_title', 'Rekap Bagi Hasil UMKM')
@section('back_url', route('dashboard'))

@section('content')
<div class="space-y-4">

    <!-- TOP SUMMARY CARD (Gambar 3.18) -->
    <div class="bg-gradient-to-r from-teal-700 to-emerald-700 rounded-3xl p-5 text-white shadow-md shadow-teal-900/20 space-y-2.5">
        <div class="flex justify-between items-center text-xs pb-2 border-b border-teal-600/60">
            <span class="text-teal-200">Total Omset Kotor</span>
            <span class="font-extrabold text-white text-sm">Rp {{ number_format($totalOmsetKotor, 0, ',', '.') }}</span>
        </div>

        <div class="flex justify-between items-center text-xs pb-2 border-b border-teal-600/60">
            <span class="text-teal-200">Total Hak Anggota (Modal)</span>
            <span class="font-extrabold text-amber-300 text-sm">Rp {{ number_format($totalHakAnggota, 0, ',', '.') }}</span>
        </div>

        <div class="flex justify-between items-center text-xs pt-1">
            <span class="text-teal-200 font-bold">Bersih Kas PKK (Margin)</span>
            <span class="font-black text-emerald-300 text-base">Rp {{ number_format($totalLabaPkk, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- MEMBER CARDS LIST (Gambar 3.18) -->
    <div class="space-y-3">
        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bagi Hasil Per Anggota</h4>

        @forelse ($anggotaList as $item)
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-11 h-11 rounded-2xl bg-teal-50 border border-teal-200/60 text-teal-700 flex items-center justify-center text-xl shrink-0">
                        👩‍🍳
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-slate-800 truncate leading-tight">{{ $item['user']->name }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Barang Terjual: <strong class="text-slate-700">{{ $item['barang_terjual'] }} pcs</strong></p>
                        <p class="text-[11px] text-teal-700 font-medium">Masuk Kas: Rp {{ number_format($item['masuk_kas_pkk'], 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Right Action: Cairkan Uang (Gambar 3.18) -->
                <div class="text-right shrink-0">
                    <span class="text-[10px] text-slate-400 block mb-1">Hak Anggota:</span>
                    <button type="button" onclick="openCairkanModal({{ $item['user']->id }}, '{{ addslashes($item['user']->name) }}', {{ $item['sisa_belum_cair'] }})"
                        class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-extrabold text-[11px] rounded-xl shadow-sm transition active:scale-95">
                        Cairkan Uang<br>
                        <span class="text-xs font-black">Rp {{ number_format($item['sisa_belum_cair'], 0, ',', '.') }}</span>
                    </button>
                    @if ($item['sudah_cair'] > 0)
                        <span class="text-[9px] text-emerald-600 block mt-1 font-semibold">Tercairkan: Rp {{ number_format($item['sudah_cair'], 0, ',', '.') }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 border border-slate-100 text-center text-xs text-slate-400 shadow-sm">
                Belum ada data penjualan anggota yang tercatat.
            </div>
        @endforelse
    </div>

</div>

<!-- Modal Pencairan Uang Bagi Hasil -->
<div id="cairkan-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl animate-fade-in relative border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-extrabold text-slate-800">Pencairan Dana Anggota</h3>
            <button type="button" onclick="closeCairkanModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">✕</button>
        </div>

        <form id="cairkan-form" method="POST" action="{{ route('laporan.cairkan') }}" class="space-y-4">
            @csrf
            <input type="hidden" id="modal-user-id" name="user_id">

            <div class="bg-amber-50 p-3.5 rounded-2xl border border-amber-200/80 text-xs">
                <p class="text-amber-900">Nama Penerima: <strong id="modal-anggota-name" class="font-bold"></strong></p>
                <p class="text-amber-900 mt-1">Sisa Hak Belum Dicairkan: <strong id="modal-hak-nominal" class="font-bold text-amber-700"></strong></p>
            </div>

            <div>
                <label for="nominal" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">
                    Nominal Pencairan (Rp)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" id="nominal" name="nominal" required min="100" step="500"
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label for="keterangan" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">
                    Keterangan (Opsional)
                </label>
                <input type="text" id="keterangan" name="keterangan"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="Contoh: Diserahkan tunai di balai RT">
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <button type="button" onclick="closeCairkanModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold rounded-2xl text-xs shadow-md shadow-amber-500/20 transition">
                    Konfirmasi Pencairan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCairkanModal(userId, name, sisaHak) {
        document.getElementById('modal-user-id').value = userId;
        document.getElementById('modal-anggota-name').innerText = name;
        document.getElementById('modal-hak-nominal').innerText = 'Rp ' + sisaHak.toLocaleString('id-ID');
        document.getElementById('nominal').value = sisaHak;
        document.getElementById('cairkan-modal').classList.remove('hidden');
    }

    function closeCairkanModal() {
        document.getElementById('cairkan-modal').classList.add('hidden');
    }
</script>
@endsection
