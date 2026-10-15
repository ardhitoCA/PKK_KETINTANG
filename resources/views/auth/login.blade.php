@extends('layouts.app')

@section('title', 'Masuk - Sistem Kas & Stok PKK')
@section('header_title', 'Sistem Kas & Stok')

@section('content')
<div class="py-6 flex flex-col justify-center min-h-[580px]">
    <!-- Brand Logo & Header -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-tr from-emerald-600 to-emerald-400 rounded-3xl mx-auto flex items-center justify-center text-4xl shadow-xl shadow-emerald-600/20 mb-4 border border-emerald-300/40">
            🌿
        </div>
        <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Sistem Kas & Stok</h1>
        <p class="text-sm font-semibold text-emerald-700">UMKM PKK Ketintang</p>
        <p class="text-xs text-slate-500 mt-1">Silakan masuk dengan akun yang telah terdaftar</p>
    </div>

    <!-- Login Form (Gambar 3.8) -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Email Pengguna</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                        placeholder="nama@pkk.local">
                </div>
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Kata Sandi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                        placeholder="••••••••">
                </div>
            </div>

            <!-- Submit Button (Gambar 3.8) -->
            <button type="submit"
                class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-2xl shadow-lg shadow-emerald-600/30 transition duration-200 flex items-center justify-center space-x-2 text-sm mt-2">
                <span>Masuk ke Sistem</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </form>
    </div>

    <!-- Quick Demo Accounts (1-Tap Login) -->
    <div class="mt-8">
        <div class="relative flex py-2 items-center">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="flex-shrink mx-3 text-slate-400 text-xs font-semibold uppercase tracking-wider">Pilih Akun Demo (1-Klik)</span>
            <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-2">
            <!-- Bendahara -->
            <a href="{{ route('quick.login', 'bendahara') }}" class="p-3 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-2xl text-center transition group active:scale-95 shadow-sm">
                <span class="text-xl block mb-1">💼</span>
                <p class="text-xs font-bold text-amber-900 leading-tight">Bendahara</p>
                <span class="text-[10px] text-amber-700/80 block mt-0.5">Admin Kasir</span>
            </a>

            <!-- Ketua -->
            <a href="{{ route('quick.login', 'ketua') }}" class="p-3 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-2xl text-center transition group active:scale-95 shadow-sm">
                <span class="text-xl block mb-1">👑</span>
                <p class="text-xs font-bold text-emerald-900 leading-tight">Ketua PKK</p>
                <span class="text-[10px] text-emerald-700/80 block mt-0.5">ACC & Omset</span>
            </a>

            <!-- Anggota -->
            <a href="{{ route('quick.login', 'anggota') }}" class="p-3 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-2xl text-center transition group active:scale-95 shadow-sm">
                <span class="text-xl block mb-1">🧺</span>
                <p class="text-xs font-bold text-sky-900 leading-tight">Anggota</p>
                <span class="text-[10px] text-sky-700/80 block mt-0.5">Titip Barang</span>
            </a>
        </div>

        <!-- Info Password Card -->
        <div class="mt-3 bg-slate-100 rounded-2xl p-3 text-[11px] text-slate-600 border border-slate-200/80 text-center">
            <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-0.5">Kredensial Manual</span>
            <p>Password semua akun demo: <strong class="text-slate-800 font-mono font-bold bg-white px-1.5 py-0.5 rounded border border-slate-300">123456</strong></p>
            <p class="text-[10px] text-slate-500 mt-1">
                Email: <span class="font-mono text-slate-700">bendahara@pkk.local</span> / <span class="font-mono text-slate-700">ketua@pkk.local</span> / <span class="font-mono text-slate-700">anggota@pkk.local</span>
            </p>
        </div>
    </div>
</div>
@endsection
