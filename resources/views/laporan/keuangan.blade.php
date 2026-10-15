@extends('layouts.app')

@section('title', 'Laporan Keuangan & Laba Rugi')
@section('header_title', 'Laporan Keuangan')
@section('back_url', route('dashboard'))

@section('content')
<div class="space-y-4">

    <!-- Top Action: Cetak Laporan -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-800">Rekapitulasi Usaha PKK</h3>
            <p class="text-xs text-slate-500">Ringkasan laba rugi dan arus kas</p>
        </div>
        <a href="{{ route('laporan.cetak') }}" target="_blank"
            class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center space-x-1.5 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span>Cetak Laporan</span>
        </a>
    </div>

    <!-- LABA RUGI SUMMARY CARDS -->
    <div class="bg-gradient-to-tr from-emerald-800 to-emerald-600 rounded-3xl p-5 text-white shadow-lg space-y-3">
        <div>
            <span class="text-emerald-200 text-xs font-semibold block">Estimasi Saldo Kas Akhir</span>
            <h2 class="text-2xl font-black tracking-tight mt-0.5">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h2>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-emerald-500/50 text-xs">
            <div>
                <span class="text-emerald-200 text-[10px] uppercase font-bold block">Total Penjualan Kasir</span>
                <span class="font-extrabold text-white text-sm">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-emerald-200 text-[10px] uppercase font-bold block">Laba Bersih Usaha PKK</span>
                <span class="font-black text-amber-300 text-sm">Rp {{ number_format($labaUsahaPkk, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-emerald-200 text-[10px] uppercase font-bold block">Pemasukan Kas Lain</span>
                <span class="font-extrabold text-emerald-100">+Rp {{ number_format($kasMasukLain, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-emerald-200 text-[10px] uppercase font-bold block">Pengeluaran Kas</span>
                <span class="font-extrabold text-rose-200">-Rp {{ number_format($kasKeluar, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- RIWAYAT PENJUALAN TERAKHIR -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm space-y-2.5">
        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Penjualan Kasir Terbaru</h4>
        <div class="divide-y divide-slate-100 text-xs">
            @forelse ($riwayatPenjualan as $trx)
                <div class="py-2.5 flex justify-between items-center">
                    <div>
                        <p class="font-bold text-slate-800">{{ $trx->kode_transaksi }}</p>
                        <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($trx->tanggal)->locale('id')->isoFormat('D MMM Y, HH:mm') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-black text-purple-700 block">Rp {{ number_format($trx->total_nominal, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-emerald-600 font-semibold">+Laba: Rp {{ number_format($trx->details->sum('laba_umkm'), 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <p class="text-center text-slate-400 py-4 text-xs">Belum ada data transaksi kasir.</p>
            @endforelse
        </div>
    </div>

    <!-- RIWAYAT KAS OPERASIONAL -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm space-y-2.5">
        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Kas Masuk & Keluar Terbaru</h4>
        <div class="divide-y divide-slate-100 text-xs">
            @forelse ($riwayatTransaksiKas as $kas)
                @php $isMasuk = $kas->kategori->jenis === 'masuk'; @endphp
                <div class="py-2.5 flex justify-between items-center">
                    <div>
                        <p class="font-bold text-slate-800 truncate max-w-[200px]">{{ $kas->kategori->nama_kategori }}</p>
                        <span class="text-[10px] text-slate-400">{{ $kas->keterangan }}</span>
                    </div>
                    <span class="font-bold {{ $isMasuk ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $isMasuk ? '+' : '-' }}Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                    </span>
                </div>
            @empty
                <p class="text-center text-slate-400 py-4 text-xs">Belum ada mutasi kas.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
