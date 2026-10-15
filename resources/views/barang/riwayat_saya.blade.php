@extends('layouts.app')

@section('title', 'Riwayat Penjualan Saya - Anggota PKK')
@section('header_title', 'Riwayat Penjualan Saya')
@section('back_url', route('dashboard'))

@section('content')
<div class="space-y-4">

    <!-- Top Total Hak Pill -->
    <div class="bg-gradient-to-tr from-sky-600 to-sky-500 rounded-3xl p-5 text-white shadow-md shadow-sky-600/20">
        <span class="text-xs font-semibold text-sky-200 block mb-1">Total Hak Pendapatan Saya</span>
        <h2 class="text-2xl font-black tracking-tight">Rp {{ number_format($totalSemuaHak, 0, ',', '.') }}</h2>

        <div class="flex items-center space-x-2 mt-3 pt-3 border-t border-sky-400/40 text-xs">
            <span class="text-sky-100">Total Produk Laku Terjual:</span>
            <strong class="font-extrabold text-white">{{ $totalSemuaTerjual }} pcs</strong>
        </div>
    </div>

    <!-- Items List (Gambar 3.17) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @forelse ($barangSaya as $data)
            @php $item = $data['barang']; @endphp
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-2.5">
                <!-- Header Card: Nama & Badge (Gambar 3.17) -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h4 class="text-xs font-bold text-slate-800 truncate">{{ $item->nama_barang }}</h4>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $item->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                        {{ $data['status_label'] }}
                    </span>
                </div>

                <!-- Grid Data: Terjual & Sisa Stok (Gambar 3.17) -->
                <div class="grid grid-cols-2 gap-2 text-xs text-slate-600">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Terjual:</span>
                        <strong class="font-bold text-slate-800">{{ $data['terjual'] }} {{ $item->satuan }}</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Sisa Stok:</span>
                        <strong class="font-bold text-slate-800">{{ $data['sisa_stok'] }} {{ $item->satuan }}</strong>
                    </div>
                </div>

                <!-- Total Uang Hak (Gambar 3.17) -->
                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                    <span class="text-xs font-semibold text-slate-500">Total Uang Hak:</span>
                    <span class="text-xs font-extrabold text-emerald-600">
                        Rp {{ number_format($data['total_hak'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 border border-slate-100 text-center shadow-sm">
                <span class="text-3xl block mb-2">🧺</span>
                <p class="text-xs font-bold text-slate-700">Belum Ada Barang yang Dititipkan</p>
                <p class="text-[11px] text-slate-400 mt-1 mb-4">Mulai dengan mengajukan barang konsinyasi buatan Anda.</p>
                <a href="{{ route('barang.pengajuan') }}" class="inline-block px-4 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20">
                    + Ajukan Barang Baru
                </a>
            </div>
        @endforelse
    </div>

</div>
@endsection
