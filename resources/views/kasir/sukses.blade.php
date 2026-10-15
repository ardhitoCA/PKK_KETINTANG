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
        <div class="py-3 border-b border-dashed border-slate-200 space-y-2 text-xs">
            <div class="flex justify-between items-center">
                <span class="text-slate-500">Metode Pembayaran:</span>
                @if($transaksi->metode_pembayaran === 'qris')
                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-red-50 text-red-700 border border-red-200">
                        <span>📱</span>
                        <span>QRIS Dinamis</span>
                    </span>
                @else
                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                        <span>💵</span>
                        <span>Tunai (Cash)</span>
                    </span>
                @endif
            </div>

            <div class="flex justify-between">
                <span class="text-slate-500">Total Tagihan:</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($transaksi->total_nominal, 0, ',', '.') }}</span>
            </div>

            @if($transaksi->metode_pembayaran === 'qris')
                <div class="flex justify-between">
                    <span class="text-slate-500">Dibayar (QRIS):</span>
                    <span class="font-semibold text-slate-700">Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center font-bold text-emerald-700 pt-1 text-xs">
                    <span>Status:</span>
                    <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md text-[10px] font-extrabold">LUNAS OTOMATIS</span>
                </div>
            @else
                <div class="flex justify-between">
                    <span class="text-slate-500">Uang Diterima:</span>
                    <span class="font-semibold text-slate-700">Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-emerald-700 pt-1 text-sm">
                    <span>Kembalian:</span>
                    <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <!-- Footer Struk -->
        <div class="text-center pt-4 text-[10px] text-slate-400 leading-relaxed">
            Terima kasih atas partisipasi Anda<br>
            mendukung kemandirian UMKM Ibu-Ibu PKK Ketintang!
        </div>
    </div>

    <!-- Actions -->
    <div class="space-y-2 print:hidden">
        <button type="button" onclick="window.print()" class="w-full py-3 bg-slate-800 hover:bg-slate-900 active:bg-black text-white font-bold rounded-2xl text-xs flex items-center justify-center space-x-2 shadow-md transition active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span>Cetak / Simpan Struk</span>
        </button>

        <a href="{{ route('kasir.index') }}" class="w-full py-3.5 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-bold rounded-2xl text-xs flex items-center justify-center space-x-2 shadow-lg shadow-purple-600/20 transition active:scale-95">
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
