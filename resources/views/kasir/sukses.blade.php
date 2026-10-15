@extends('layouts.app')

@section('title', 'Transaksi Berhasil - Kasir')
@section('header_title', 'Struk Pembayaran')
@section('back_url', route('kasir.index'))

@section('content')
<div class="space-y-4">

    <!-- Struk Nota Paper Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-md relative overflow-hidden">
        <!-- Top Jagged Edge / Decor -->
        <div class="text-center pb-4 border-b border-dashed border-slate-200">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-2xl mx-auto mb-2">
                ✓
            </div>
            <h3 class="text-base font-extrabold text-slate-800">Transaksi Selesai</h3>
            <p class="text-xs text-slate-400">UMKM PKK Kelurahan Ketintang</p>
            <p class="text-[11px] font-mono text-slate-500 mt-1 font-bold">{{ $transaksi->kode_transaksi }}</p>
            <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y H:i') }}</span>
        </div>

        <!-- Detail Item Belanja -->
        <div class="py-4 border-b border-dashed border-slate-200 space-y-2 text-xs">
            @foreach ($transaksi->details as $item)
                <div class="flex justify-between items-start">
                    <div class="pr-2">
                        <p class="font-bold text-slate-800 leading-tight">{{ $item->barang->nama_barang }}</p>
                        <span class="text-[10px] text-slate-400">{{ $item->jumlah }} x Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</span>
                        <span class="text-[9px] text-slate-400 block italic">Titipan: {{ $item->barang->user->name ?? 'Anggota' }}</span>
                    </div>
                    <span class="font-bold text-slate-800 shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <!-- Ringkasan Pembayaran -->
        <div class="py-3 border-b border-dashed border-slate-200 space-y-1.5 text-xs">
            <div class="flex justify-between">
                <span class="text-slate-500">Total Tagihan:</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($transaksi->total_nominal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Uang Diterima:</span>
                <span class="font-semibold text-slate-700">Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-emerald-700 pt-1 text-sm">
                <span>Kembalian:</span>
                <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer Struk -->
        <div class="text-center pt-4 text-[10px] text-slate-400 leading-relaxed">
            Terima kasih atas partisipasi Anda<br>
            mendukung kemandirian UMKM Ibu-Ibu PKK Ketintang!
        </div>
    </div>

    <!-- Actions -->
    <div class="space-y-2">
        <a href="{{ route('kasir.index') }}" class="w-full py-3.5 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-bold rounded-2xl text-xs flex items-center justify-center space-x-2 shadow-lg shadow-purple-600/20 transition">
            <span>Transaksi Baru</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>

        <a href="{{ route('dashboard') }}" class="w-full py-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-2xl text-xs flex items-center justify-center transition">
            Kembali ke Dashboard
        </a>
    </div>

</div>
@endsection
