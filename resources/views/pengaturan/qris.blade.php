@extends('layouts.app')

@section('title', 'Pengaturan QRIS Resmi - Ketua PKK')
@section('header_title', 'Pengaturan QRIS')
@section('back_url', route('dashboard'))

@section('content')
<div class="space-y-4 pb-12">

    <!-- Header Badge Info -->
    <div class="bg-gradient-to-r from-red-600 to-rose-600 rounded-3xl p-5 text-white shadow-md shadow-red-600/20 relative overflow-hidden">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur border border-white/30 flex items-center justify-center text-2xl shrink-0">
                📱
            </div>
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider bg-white/20 px-2 py-0.5 rounded-full inline-block mb-1 border border-white/30">
                    Otoritas Ketua PKK
                </span>
                <h3 class="text-base font-extrabold leading-tight">Master QRIS Toko PKK</h3>
                <p class="text-xs text-rose-100 mt-0.5">Atur QRIS resmi organisasi yang digunakan kasir bendahara</p>
            </div>
        </div>
    </div>

    <!-- Alert Success / Error Feedback -->
    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center space-x-2 text-xs font-bold animate-fade-in">
            <span>✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl flex items-center space-x-2 text-xs font-bold animate-fade-in">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        
        <!-- KOLOM 1: FORMULIR PENGATURAN KETUA -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Identitas & Payload QRIS</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Pengaturan ini akan langsung tersinkron ke layar kasir bendahara.</p>
            </div>

            <form action="{{ route('pengaturan.qris.update') }}" method="POST" class="space-y-3.5">
                @csrf

                <!-- Nama Merchant -->
                <div>
                    <label for="merchant_name" class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Merchant / Toko <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="merchant_name" name="merchant_name" required
                        value="{{ old('merchant_name', $config['merchant_name']) }}"
                        oninput="updatePreview()"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Contoh: PKK KELURAHAN KETINTANG">
                </div>

                <!-- NMID -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="nmid" class="block text-xs font-bold text-slate-700 mb-1">
                            NMID QRIS <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nmid" name="nmid" required
                            value="{{ old('nmid', $config['nmid']) }}"
                            oninput="updatePreview()"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Contoh: ID102026888KETINTANG">
                    </div>

                    <div>
                        <label for="kota" class="block text-xs font-bold text-slate-700 mb-1">
                            Kota Merchant <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="kota" name="kota" required
                            value="{{ old('kota', $config['kota']) }}"
                            oninput="updatePreview()"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Contoh: SURABAYA">
                    </div>
                </div>

                <!-- Base String Payload QRIS Statis -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="payload" class="block text-xs font-bold text-slate-700">
                            Base Payload QRIS Statis <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[10px] text-slate-400 font-mono">Standar EMVCo MPM</span>
                    </div>
                    <textarea id="payload" name="payload" rows="4" required
                        oninput="updatePreview()"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-[11px] font-mono text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500 leading-relaxed"
                        placeholder="Tempel string QRIS berawalan 000201...">{{ old('payload', $config['payload']) }}</textarea>
                    
                    <div class="bg-amber-50 border border-amber-200/80 rounded-xl p-2.5 mt-1.5 text-[11px] text-amber-800 leading-tight">
                        <strong>💡 Tips Cara Mendapatkan String QRIS:</strong><br>
                        Buka foto/stiker QRIS toko Anda (dari Bank/E-Wallet), scan menggunakan <strong>Google Lens</strong> atau kamera HP, lalu pilih <em>Salin Teks</em> dan tempelkan di kotak di atas.
                    </div>
                </div>

                <!-- Action Submit -->
                <div class="pt-2 flex items-center space-x-2">
                    <button type="submit" class="flex-1 py-3 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-2xl text-xs shadow-md shadow-red-600/20 transition active:scale-95">
                        💾 Simpan Pengaturan QRIS
                    </button>
                </div>
            </form>

            <!-- Reset to Default PKK Ketintang -->
            <form action="{{ route('pengaturan.qris.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan QRIS ke default bawaan PKK Ketintang?')">
                @csrf
                <button type="submit" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl text-xs transition">
                    🔄 Reset ke Default Bawaan PKK Ketintang
                </button>
            </form>
        </div>

        <!-- KOLOM 2: LIVE PREVIEW & SIMULATOR TEST KETUA -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Live Preview & Simulator</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Uji coba scan langsung QRIS Dinamis Anda</p>
                </div>
                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-extrabold text-[10px] rounded-full">
                    Siap Scan
                </span>
            </div>

            <!-- Kartu QRIS Preview -->
            <div class="bg-gradient-to-b from-white to-slate-50 border-2 border-red-500/80 rounded-2xl p-4 text-center shadow-md relative overflow-hidden max-w-xs mx-auto">
                <!-- Header Kartu QRIS -->
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-2 mb-2">
                    <div class="flex items-center space-x-1">
                        <span class="px-2 py-0.5 bg-red-600 text-white font-black text-xs tracking-wider rounded">QRIS</span>
                        <span class="text-[9px] font-bold text-slate-400">PEMBAYARAN DIGITAL</span>
                    </div>
                    <span class="text-[9px] font-black text-slate-600 tracking-wider bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">GPN</span>
                </div>

                <!-- Info Merchant -->
                <div class="mb-2">
                    <h4 id="preview-merchant-name" class="text-xs font-black text-slate-800 uppercase tracking-tight truncate">
                        {{ $config['merchant_name'] }}
                    </h4>
                    <p id="preview-nmid" class="text-[9px] text-slate-400 font-mono mt-0.5">
                        NMID: {{ $config['nmid'] }}
                    </p>
                </div>

                <!-- Canvas QRIS Dinamis -->
                <div class="py-1 flex justify-center">
                    <div class="p-2.5 bg-white rounded-2xl shadow-inner border border-slate-200 inline-block">
                        <canvas id="preview-qris-canvas" class="w-44 h-44 mx-auto block rounded-lg"></canvas>
                    </div>
                </div>

                <!-- Nominal Tagihan Diset Otomatis -->
                <div class="mt-2 bg-red-50 border border-red-200/80 rounded-xl py-2 px-3">
                    <p class="text-[9px] text-red-600 font-extrabold uppercase tracking-wider">Simulasi Nominal Dinamis:</p>
                    <p id="preview-nominal-display" class="text-base font-black text-red-700">Rp 25.000</p>
                </div>

                <p class="text-[10px] text-slate-400 mt-2">
                    <span id="preview-kota">{{ $config['kota'] }}</span> • Standar Bank Indonesia
                </p>
            </div>

            <!-- Input Simulator Nominal -->
            <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200/70 space-y-2">
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                    Uji Coba Nominal Dinamis (Rp)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" id="sim-nominal-input" value="25000" step="1000" min="500"
                        oninput="updatePreview()"
                        class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-800 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Quick Button Nominal Tes -->
                <div class="grid grid-cols-3 gap-1.5">
                    <button type="button" onclick="setSimNominal(1000)" class="py-1.5 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-lg text-[10px] border border-slate-200">
                        1.000
                    </button>
                    <button type="button" onclick="setSimNominal(25000)" class="py-1.5 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-lg text-[10px] border border-slate-200">
                        25.000
                    </button>
                    <button type="button" onclick="setSimNominal(50000)" class="py-1.5 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-lg text-[10px] border border-slate-200">
                        50.000
                    </button>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    function crc16(str) {
        let crc = 0xFFFF;
        for (let c = 0; c < str.length; c++) {
            crc ^= str.charCodeAt(c) << 8;
            for (let i = 0; i < 8; i++) {
                if ((crc & 0x8000) !== 0) {
                    crc = ((crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    crc = (crc << 1) & 0xFFFF;
                }
            }
        }
        return (crc & 0xFFFF).toString(16).toUpperCase().padStart(4, '0');
    }

    function makeDynamicQRIS(staticQris, amount) {
        let qris = (staticQris || '').trim();

        // 1. Ubah point of initiation method: 11 (statis) -> 12 (dinamis)
        qris = qris.replace('010211', '010212');

        // 2. Potong checksum lama (tag 6304XXXX) di bagian akhir jika ada
        const idx63 = qris.indexOf('6304');
        if (idx63 !== -1) {
            qris = qris.substring(0, idx63);
        }

        // 3. Format tag 54 (Transaction Amount): '54' + len(2 digit) + amount
        const amountStr = Math.round(amount).toString();
        const tag54 = '54' + amountStr.length.toString().padStart(2, '0') + amountStr;

        // 4. Sisipkan tag 54 sebelum tag 58 (5802ID)
        const idx58 = qris.indexOf('5802');
        const idx53 = qris.indexOf('5303360');

        if (idx58 !== -1 && idx53 !== -1 && idx58 > idx53) {
            const before58 = qris.substring(0, idx53 + 7);
            const from58 = qris.substring(idx58);
            qris = before58 + tag54 + from58;
        } else if (idx58 !== -1) {
            qris = qris.substring(0, idx58) + tag54 + qris.substring(idx58);
        } else {
            qris = qris + tag54;
        }

        // 5. Tambah tag 6304 dan hitung CRC16 baru
        const payloadForCrc = qris + '6304';
        const crc = crc16(payloadForCrc);
        return payloadForCrc + crc;
    }

    function updatePreview() {
        const merchantName = document.getElementById('merchant_name').value || 'PKK KELURAHAN KETINTANG';
        const nmid = document.getElementById('nmid').value || 'ID102026888KETINTANG';
        const kota = document.getElementById('kota').value || 'SURABAYA';
        const payload = document.getElementById('payload').value.trim();
        const simNominal = parseFloat(document.getElementById('sim-nominal-input').value) || 25000;

        document.getElementById('preview-merchant-name').innerText = merchantName;
        document.getElementById('preview-nmid').innerText = 'NMID: ' + nmid;
        document.getElementById('preview-kota').innerText = kota;
        document.getElementById('preview-nominal-display').innerText = 'Rp ' + simNominal.toLocaleString('id-ID');

        if (payload) {
            const dynamicPayload = makeDynamicQRIS(payload, simNominal);
            const canvas = document.getElementById('preview-qris-canvas');
            if (window.QRCode && window.QRCode.toCanvas) {
                window.QRCode.toCanvas(canvas, dynamicPayload, {
                    width: 200,
                    margin: 1,
                    color: {
                        dark: '#0f172a',
                        light: '#ffffff'
                    }
                }, function (error) {
                    if (error) console.error('Gagal render QRIS preview:', error);
                });
            }
        }
    }

    function setSimNominal(val) {
        document.getElementById('sim-nominal-input').value = val;
        updatePreview();
    }

    // Jalankan render awal saat halaman terbuka
    document.addEventListener('DOMContentLoaded', function () {
        updatePreview();
    });
</script>
@endsection
