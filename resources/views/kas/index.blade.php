@extends('layouts.app')

@section('title', 'Buku Kas Operasional - Bendahara')
@section('header_title', 'Buku Kas Operasional')
@section('back_url', route('dashboard'))

@section('content')
<div class="space-y-4">

    <!-- Saldo & Filter Summary -->
    <div class="bg-gradient-to-tr from-emerald-700 to-emerald-600 rounded-3xl p-5 text-white shadow-md shadow-emerald-700/20">
        <span class="text-xs font-semibold text-emerald-200 block mb-1">Total Saldo Kas Operasional</span>
        <h2 class="text-2xl font-black tracking-tight">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h2>

        <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-emerald-500/50 text-xs">
            <div>
                <span class="text-emerald-200 text-[10px] uppercase font-bold block">Total Masuk</span>
                <span class="font-bold text-white">+Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-emerald-200 text-[10px] uppercase font-bold block">Total Keluar</span>
                <span class="font-bold text-rose-200">-Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Tombol Input Baru -->
    <a href="{{ route('kas.create') }}" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-sm transition flex items-center justify-center space-x-2 text-xs active:scale-95">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        <span>Catat Transaksi Kas Baru</span>
    </a>

    <!-- Riwayat Transaksi -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm space-y-3">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Riwayat Kas Masuk & Keluar</h3>

        <div class="divide-y divide-slate-100">
            @forelse ($transaksiList as $item)
                @php $isMasuk = $item->kategori->jenis === 'masuk'; @endphp
                <div class="py-3 flex items-start justify-between gap-2">
                    <div class="flex items-start space-x-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm shrink-0 mt-0.5 {{ $isMasuk ? 'bg-emerald-50 text-emerald-600 border border-emerald-200/60' : 'bg-rose-50 text-rose-600 border border-rose-200/60' }}">
                            {{ $isMasuk ? '↓' : '↑' }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-800 leading-tight truncate">{{ $item->kategori->nama_kategori }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $item->keterangan }}</p>
                            <span class="text-[10px] text-slate-400 block mt-0.5 font-medium">{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('D MMM Y') }}</span>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-xs font-extrabold {{ $isMasuk ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $isMasuk ? '+' : '-' }}Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </span>
                        @if ($item->bukti_nota)
                            <a href="{{ asset('storage/' . $item->bukti_nota) }}" target="_blank" class="block text-[10px] text-sky-600 hover:underline mt-0.5 font-semibold">
                                📎 Lihat Nota
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    Belum ada catatan transaksi kas operasional.
                </div>
            @endforelse
        </div>

        @if ($transaksiList->hasPages())
            <div class="pt-2">
                {{ $transaksiList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
