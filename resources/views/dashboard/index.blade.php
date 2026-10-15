@extends('layouts.app')

@section('title', 'Dashboard - Sistem Kas & Stok PKK')
@section('header_title', 'Dashboard PKK')

@section('content')
<div class="space-y-4">

    <!-- 1. GREETING PROFILE CARD (Gambar 3.9, 3.10, 3.11) -->
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 rounded-3xl p-5 text-white shadow-md shadow-emerald-600/20 relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur border border-white/30 flex items-center justify-center text-3xl shadow-inner">
                @if ($user->role === 'ketua')
                    👑
                @elseif ($user->role === 'bendahara')
                    💼
                @else
                    👩‍🍳
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <h3 class="text-lg font-extrabold tracking-tight truncate leading-tight">
                    @if ($user->role === 'ketua')
                        Hai, Ketua!
                    @elseif ($user->role === 'bendahara')
                        Hai, Admin!
                    @else
                        Hai, {{ explode(' ', $user->name)[1] ?? $user->name }}!
                    @endif
                </h3>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/25 text-white border border-white/30">
                        {{ $user->role }}
                    </span>
                    <span class="text-xs text-emerald-100 truncate font-medium">{{ $user->name }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SUMMARY METRIC PILLS -->
    @if ($user->role === 'bendahara')
        <div class="grid grid-cols-2 gap-2.5">
            <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
                <span class="text-[11px] font-semibold text-slate-400 block mb-0.5">Saldo Kas Operasional</span>
                <p class="text-base font-extrabold text-emerald-600">Rp {{ number_format($saldoKas, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
                <span class="text-[11px] font-semibold text-slate-400 block mb-0.5">Total Stok di Etalase</span>
                <p class="text-base font-extrabold text-slate-800">{{ $totalStok }} <span class="text-xs font-normal text-slate-500">pcs ({{ $totalBarangAktif }} produk)</span></p>
            </div>
        </div>
    @elseif ($user->role === 'ketua')
        <div class="grid grid-cols-2 gap-2.5">
            <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
                <span class="text-[11px] font-semibold text-slate-400 block mb-0.5">Antrean ACC Barang</span>
                <p class="text-base font-extrabold text-rose-600">{{ $pendingCount }} <span class="text-xs font-normal text-slate-500">menunggu</span></p>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
                <span class="text-[11px] font-semibold text-slate-400 block mb-0.5">Produk Aktif Terjual</span>
                <p class="text-base font-extrabold text-slate-800">{{ $totalBarangAktif }} <span class="text-xs font-normal text-slate-500">produk</span></p>
            </div>
        </div>
    @else
        <!-- Anggota -->
        <div class="grid grid-cols-2 gap-2.5">
            <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
                <span class="text-[11px] font-semibold text-slate-400 block mb-0.5">Total Terjual</span>
                <p class="text-base font-extrabold text-sky-600">{{ $myTerjualCount }} <span class="text-xs font-normal text-slate-500">pcs</span></p>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
                <span class="text-[11px] font-semibold text-slate-400 block mb-0.5">Hak Uang Saya</span>
                <p class="text-base font-extrabold text-emerald-600">Rp {{ number_format($myHakUang, 0, ',', '.') }}</p>
            </div>
        </div>
    @endif

    <!-- 3. RINGKASAN HARI INI: GRAFIK OMSET 7 HARI TERAKHIR (Gambar 3.9, 3.10, 3.11) -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ringkasan Hari Ini</h4>
                <p class="text-sm font-bold text-slate-800 mt-0.5">
                    {{ $user->role === 'anggota' ? 'Penjualan Produk Saya' : 'Omset 7 Hari Terakhir' }}
                </p>
            </div>
            <span class="text-[11px] bg-slate-100 text-slate-600 font-semibold px-2.5 py-1 rounded-full">
                7 Hari
            </span>
        </div>

        <!-- Bar Chart Container -->
        <div class="h-36 flex items-end justify-between gap-2 pt-6 px-1 border-b border-slate-100 pb-2">
            @foreach ($omset7Hari as $item)
                @php
                    $heightPercent = $maxOmset > 0 ? max(8, round(($item['nominal'] / $maxOmset) * 100)) : 8;
                    $isPositive = $item['nominal'] > 0;
                @endphp
                <div class="flex-1 flex flex-col items-center group relative h-full justify-end">
                    <!-- Tooltip Hover -->
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-8 bg-slate-800 text-white text-[10px] font-bold px-2 py-1 rounded-lg pointer-events-none whitespace-nowrap z-20 shadow-md">
                        Rp {{ number_format($item['nominal'], 0, ',', '.') }}
                    </div>

                    <!-- Bar -->
                    <div class="w-full max-w-[28px] rounded-t-lg transition-all duration-500 {{ $isPositive ? 'bg-emerald-500 group-hover:bg-emerald-600 shadow-sm shadow-emerald-500/20' : 'bg-slate-200' }}"
                         style="height: {{ $heightPercent }}%;">
                    </div>

                    <!-- Day Label -->
                    <span class="text-[11px] font-semibold {{ $isPositive ? 'text-slate-700' : 'text-slate-400' }} mt-2">
                        {{ $item['day'] }}
                    </span>
                </div>
            @endforeach
        </div>
        <p class="text-[11px] text-slate-400 text-center mt-2 font-medium">Arahkan atau ketuk grafik untuk melihat rincian omset</p>
    </div>

    <!-- 4. MENU UTAMA SESUAI PERAN (Gambar 3.9, 3.10, 3.11) -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Menu Utama</h4>

        @if ($user->role === 'bendahara')
            <!-- MENU BENDAHARA (Gambar 3.10) -->
            <div class="grid grid-cols-4 sm:grid-cols-5 gap-3">
                <!-- Input Kas (Biru) -->
                <a href="{{ route('kas.create') }}" class="flex flex-col items-center group text-center active:scale-95 transition">
                    <div class="w-13 h-13 rounded-2xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-2xl shadow-sm group-hover:bg-blue-100 transition mb-1.5">
                        💵
                    </div>
                    <span class="text-xs font-semibold text-slate-700 leading-tight">Input Kas</span>
                </a>

                <!-- Data Stok (Kuning) -->
                <a href="{{ route('barang.kelola_stok') }}" class="flex flex-col items-center group text-center active:scale-95 transition">
                    <div class="w-13 h-13 rounded-2xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-2xl shadow-sm group-hover:bg-amber-100 transition mb-1.5">
                        📦
                    </div>
                    <span class="text-xs font-semibold text-slate-700 leading-tight">Data Stok</span>
                </a>

                <!-- Retur (Coklat/Merah) -->
                <a href="{{ route('barang.retur') }}" class="flex flex-col items-center group text-center active:scale-95 transition">
                    <div class="w-13 h-13 rounded-2xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-2xl shadow-sm group-hover:bg-rose-100 transition mb-1.5">
                        🔄
                    </div>
                    <span class="text-xs font-semibold text-slate-700 leading-tight">Retur</span>
                </a>

                <!-- Penjualan Kasir (Ungu) -->
                <a href="{{ route('kasir.index') }}" class="flex flex-col items-center group text-center active:scale-95 transition">
                    <div class="w-13 h-13 rounded-2xl bg-purple-50 border border-purple-200/60 flex items-center justify-center text-2xl shadow-sm group-hover:bg-purple-100 transition mb-1.5">
                        🛍️
                    </div>
                    <span class="text-xs font-semibold text-slate-700 leading-tight">Penjualan</span>
                </a>

                <!-- Laporan (Hijau) -->
                <a href="{{ route('laporan.keuangan') }}" class="flex flex-col items-center group text-center active:scale-95 transition">
                    <div class="w-13 h-13 rounded-2xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-2xl shadow-sm group-hover:bg-emerald-100 transition mb-1.5">
                        📊
                    </div>
                    <span class="text-xs font-semibold text-slate-700 leading-tight">Laporan</span>
                </a>

                <!-- Rekap Bagi Hasil (Baru) -->
                <a href="{{ route('laporan.bagi_hasil') }}" class="flex flex-col items-center group text-center active:scale-95 transition">
                    <div class="w-13 h-13 rounded-2xl bg-teal-50 border border-teal-200/60 flex items-center justify-center text-2xl shadow-sm group-hover:bg-teal-100 transition mb-1.5">
                        🤝
                    </div>
                    <span class="text-xs font-semibold text-slate-700 leading-tight">Bagi Hasil</span>
                </a>
            </div>

        @elseif ($user->role === 'ketua')
            <!-- MENU KETUA PKK (Gambar 3.9) -->
            <div class="grid grid-cols-4 gap-2.5">
                <!-- Laporan Keuangan -->
                <a href="{{ route('laporan.keuangan') }}" class="flex flex-col items-center group text-center active:scale-95 transition">
                    <div class="w-13 h-13 rounded-2xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-2xl shadow-sm group-hover:bg-emerald-100 transition mb-1.5">
                        📑
                    </div>
                    <span class="text-xs font-semibold text-slate-700 leading-tight">Laporan</span>
                </a>

                <!-- ACC Barang -->
                <a href="{{ route('barang.acc_antrean') }}" class="flex flex-col items-center group text-center active:scale-95 transition relative">
                    <div class="w-13 h-13 rounded-2xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-2xl shadow-sm group-hover:bg-rose-100 transition mb-1.5 relative">
                        ✅
                        @if ($pendingCount > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white rounded-full text-[10px] font-black flex items-center justify-center border-2 border-white shadow">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </div>
                    <span class="text-xs font-semibold text-slate-700 leading-tight">ACC Barang</span>
                </a>

                <!-- Pengaturan QRIS Toko (Khusus Ketua) -->
                <a href="{{ route('pengaturan.qris') }}" class="flex flex-col items-center group text-center active:scale-95 transition">
                    <div class="w-13 h-13 rounded-2xl bg-red-50 border border-red-200/60 flex items-center justify-center text-2xl shadow-sm group-hover:bg-red-100 transition mb-1.5">
                        📱
                    </div>
                    <span class="text-xs font-semibold text-slate-700 leading-tight">Master QRIS</span>
                </a>

                <!-- Cetak Laporan -->
                <a href="{{ route('laporan.cetak') }}" target="_blank" class="flex flex-col items-center group text-center active:scale-95 transition">
                    <div class="w-13 h-13 rounded-2xl bg-sky-50 border border-sky-200/60 flex items-center justify-center text-2xl shadow-sm group-hover:bg-sky-100 transition mb-1.5">
                        🖨️
                    </div>
                    <span class="text-xs font-semibold text-slate-700 leading-tight">Cetak</span>
                </a>
            </div>

        @else
            <!-- MENU ANGGOTA PKK (Gambar 3.11) -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Pengajuan Baru (Oranye) -->
                <a href="{{ route('barang.pengajuan') }}" class="flex flex-col items-center p-4 rounded-2xl bg-amber-50 hover:bg-amber-100/70 border border-amber-200/70 transition group active:scale-95 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shadow-md shadow-amber-500/20 mb-2">
                        ➕
                    </div>
                    <span class="text-sm font-bold text-slate-800">Pengajuan Barang</span>
                    <span class="text-[11px] text-slate-500 mt-0.5">Titip produk konsinyasi baru</span>
                </a>

                <!-- Riwayat Penjualan (Biru) -->
                <a href="{{ route('barang.riwayat_saya') }}" class="flex flex-col items-center p-4 rounded-2xl bg-sky-50 hover:bg-sky-100/70 border border-sky-200/70 transition group active:scale-95 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-sky-500 text-white flex items-center justify-center text-2xl shadow-md shadow-sky-500/20 mb-2">
                        🕒
                    </div>
                    <span class="text-sm font-bold text-slate-800">Riwayat Penjualan</span>
                    <span class="text-[11px] text-slate-500 mt-0.5">Cek sisa stok & hak uang</span>
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
