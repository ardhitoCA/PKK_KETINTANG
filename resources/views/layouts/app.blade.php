<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#16a34a">
    <title>@yield('title', 'Sistem Kas & Stok PKK') - PKK Ketintang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        /* Hide scrollbars but keep functionality */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

    <!-- DESKTOP TOP BAR (Tampil hanya di layar Laptop/PC) -->
    <header class="hidden md:block bg-emerald-800 text-white shadow-md border-b border-emerald-900 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-emerald-600 rounded-xl flex items-center justify-center font-bold text-lg shadow-inner">
                    🌿
                </div>
                <div>
                    <h1 class="text-sm font-bold tracking-tight leading-none text-emerald-100">Sistem Kas & Stok PKK</h1>
                    <p class="text-xs text-emerald-300 font-medium">UMKM Mandiri Kelurahan Ketintang Surabaya</p>
                </div>
            </div>

            <!-- Demo Fast Switcher Bar -->
            <div class="flex items-center space-x-2 text-xs bg-emerald-900/60 backdrop-blur px-3 py-1.5 rounded-full border border-emerald-700/60">
                <span class="text-emerald-300 font-semibold mr-1">Demo Cepat:</span>
                <a href="{{ route('quick.login', 'bendahara') }}" class="px-2.5 py-1 rounded-full font-medium transition {{ auth()->check() && auth()->user()->role === 'bendahara' ? 'bg-amber-400 text-slate-900 font-bold shadow' : 'bg-emerald-800 text-emerald-200 hover:bg-emerald-700' }}">
                    Bendahara (Admin)
                </a>
                <a href="{{ route('quick.login', 'ketua') }}" class="px-2.5 py-1 rounded-full font-medium transition {{ auth()->check() && auth()->user()->role === 'ketua' ? 'bg-amber-400 text-slate-900 font-bold shadow' : 'bg-emerald-800 text-emerald-200 hover:bg-emerald-700' }}">
                    Ketua PKK
                </a>
                <a href="{{ route('quick.login', 'anggota') }}" class="px-2.5 py-1 rounded-full font-medium transition {{ auth()->check() && auth()->user()->role === 'anggota' ? 'bg-amber-400 text-slate-900 font-bold shadow' : 'bg-emerald-800 text-emerald-200 hover:bg-emerald-700' }}">
                    Anggota PKK
                </a>
            </div>

            <!-- Desktop View Mode Toggle -->
            <div class="flex items-center space-x-2 text-xs">
                <button type="button" id="toggle-view-btn" onclick="toggleDesktopWidth()" class="flex items-center space-x-1.5 bg-emerald-700 hover:bg-emerald-600 px-3 py-1.5 rounded-lg text-emerald-100 font-medium transition shadow-sm">
                    <svg id="view-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span id="view-mode-text">Mode Layar HP</span>
                </button>
            </div>
        </div>
    </header>

    <!-- WRAPPER UTAMA RESPONSIVE -->
    <div class="min-h-screen md:py-6 flex items-start justify-center">
        <!-- Container App: Mobile (full width) | Desktop (default max-w-md ala smartphone, atau lebar via toggle) -->
        <main id="app-container" class="w-full max-w-md transition-all duration-300 md:rounded-3xl md:shadow-2xl md:ring-1 md:ring-slate-900/10 bg-slate-50 min-h-screen md:min-h-[820px] flex flex-col relative overflow-hidden">

            <!-- MOBILE APP HEADER -->
            <header class="bg-white/95 backdrop-blur border-b border-slate-100 px-4 py-3 sticky top-0 z-40 flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    @hasSection('back_url')
                        <a href="@yield('back_url')" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @else
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white text-lg shadow-sm shadow-emerald-500/30">
                            🌿
                        </div>
                    @endif
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 leading-tight">@yield('header_title', 'Dashboard PKK')</h2>
                        <span class="text-[11px] text-slate-400 font-medium block">PKK Kelurahan Ketintang</span>
                    </div>
                </div>

                <div class="flex items-center space-x-1.5">
                    @auth
                        <!-- Role Badge -->
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider
                            {{ auth()->user()->role === 'bendahara' ? 'bg-amber-100 text-amber-800 border border-amber-300/50' : '' }}
                            {{ auth()->user()->role === 'ketua' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300/50' : '' }}
                            {{ auth()->user()->role === 'anggota' ? 'bg-sky-100 text-sky-800 border border-sky-300/50' : '' }}">
                            {{ auth()->user()->role }}
                        </span>

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Keluar / Logout" class="w-8 h-8 rounded-full text-rose-500 hover:bg-rose-50 flex items-center justify-center transition active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            <!-- FLASH ALERTS -->
            <div class="px-4 pt-3">
                @if (session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-3 text-xs flex items-start space-x-2 shadow-sm animate-fade-in">
                        <span class="text-emerald-600 font-bold text-base leading-none">✓</span>
                        <div class="flex-1 font-medium">{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-3 text-xs flex items-start space-x-2 shadow-sm animate-fade-in">
                        <span class="text-rose-600 font-bold text-base leading-none">✕</span>
                        <div class="flex-1 font-medium">{{ session('error') }}</div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="bg-sky-50 border border-sky-200 text-sky-800 rounded-xl p-3 text-xs flex items-start space-x-2 shadow-sm animate-fade-in">
                        <span class="text-sky-600 font-bold text-base leading-none">ℹ</span>
                        <div class="flex-1 font-medium">{{ session('info') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-xl p-3 text-xs shadow-sm">
                        <p class="font-bold mb-1">Periksa kembali data Anda:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-amber-800">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- MAIN CONTENT AREA -->
            <div class="flex-1 px-4 py-3 pb-24">
                @yield('content')
            </div>

            <!-- BOTTOM MOBILE NAVIGATION BAR -->
            @auth
            <nav class="fixed md:absolute bottom-0 left-0 right-0 max-w-md mx-auto bg-white/95 backdrop-blur border-t border-slate-200/80 px-4 py-2 z-40 flex items-center justify-around shadow-lg">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-1 px-2 text-xs font-semibold {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Beranda</span>
                </a>

                @if (auth()->user()->role === 'bendahara')
                    <a href="{{ route('kasir.index') }}" class="flex flex-col items-center py-1 px-2 text-xs font-semibold {{ request()->routeIs('kasir.*') ? 'text-purple-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span>Kasir</span>
                    </a>
                    <a href="{{ route('kas.index') }}" class="flex flex-col items-center py-1 px-2 text-xs font-semibold {{ request()->routeIs('kas.*') ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Buku Kas</span>
                    </a>
                    <a href="{{ route('laporan.keuangan') }}" class="flex flex-col items-center py-1 px-2 text-xs font-semibold {{ request()->routeIs('laporan.*') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Laporan</span>
                    </a>
                @elseif (auth()->user()->role === 'ketua')
                    <a href="{{ route('barang.acc_antrean') }}" class="flex flex-col items-center py-1 px-2 text-xs font-semibold {{ request()->routeIs('barang.acc_antrean') ? 'text-rose-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>ACC Barang</span>
                    </a>
                    <a href="{{ route('laporan.keuangan') }}" class="flex flex-col items-center py-1 px-2 text-xs font-semibold {{ request()->routeIs('laporan.*') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Laporan</span>
                    </a>
                @else
                    <a href="{{ route('barang.pengajuan') }}" class="flex flex-col items-center py-1 px-2 text-xs font-semibold {{ request()->routeIs('barang.pengajuan') ? 'text-amber-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Ajukan Barang</span>
                    </a>
                    <a href="{{ route('barang.riwayat_saya') }}" class="flex flex-col items-center py-1 px-2 text-xs font-semibold {{ request()->routeIs('barang.riwayat_saya') ? 'text-sky-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Riwayat Saya</span>
                    </a>
                @endif
            </nav>
            @endauth

        </main>
    </div>

    <!-- SCRIPT VIEW TOGGLE FOR DESKTOP -->
    <script>
        let isExpanded = false;
        function toggleDesktopWidth() {
            const container = document.getElementById('app-container');
            const nav = document.querySelector('nav');
            const text = document.getElementById('view-mode-text');

            if (!isExpanded) {
                container.classList.remove('max-w-md');
                container.classList.add('max-w-4xl');
                if (nav) {
                    nav.classList.remove('max-w-md');
                    nav.classList.add('max-w-4xl');
                }
                text.innerText = "Mode Layar Lebar";
                isExpanded = true;
            } else {
                container.classList.remove('max-w-4xl');
                container.classList.add('max-w-md');
                if (nav) {
                    nav.classList.remove('max-w-4xl');
                    nav.classList.add('max-w-md');
                }
                text.innerText = "Mode Layar HP";
                isExpanded = false;
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
