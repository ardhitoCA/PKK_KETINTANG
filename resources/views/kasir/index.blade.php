@extends('layouts.app')

@section('title', 'Kasir Penjualan - Bendahara')
@section('header_title', 'Kasir Penjualan')
@section('back_url', route('dashboard'))

@section('content')
<div class="space-y-4 pb-20">

    <!-- Search Input Bar (Gambar 3.15) -->
    <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
            🔍
        </span>
        <input type="text" id="search-input" onkeyup="filterBarang()"
            class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-sm"
            placeholder="Cari nama barang...">
    </div>

    <!-- Barang Items List (Gambar 3.15) -->
    <div id="barang-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse ($barangs as $item)
            <div class="barang-item bg-white rounded-2xl p-3.5 border border-slate-100 shadow-sm flex items-center justify-between gap-3 transition hover:border-purple-200"
                 data-id="{{ $item->id }}"
                 data-nama="{{ strtolower($item->nama_barang) }}"
                 data-harga="{{ $item->harga_jual }}"
                 data-stok="{{ $item->stok_saat_ini }}">

                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-200/60 flex items-center justify-center text-2xl shrink-0">
                        🛍️
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-slate-800 truncate leading-tight">{{ $item->nama_barang }}</h4>
                        <p class="text-[11px] text-purple-700 font-extrabold mt-0.5">
                            Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                            <span class="text-slate-400 font-normal ml-1">| Stok: <strong class="text-slate-600 font-bold item-stok-text">{{ $item->stok_saat_ini }}</strong></span>
                        </p>
                        <span class="text-[10px] text-slate-400 truncate block">Titipan: {{ $item->user->name ?? 'Anggota' }}</span>
                    </div>
                </div>

                <!-- Action / Counter Container -->
                <div class="item-action-container shrink-0">
                    <!-- Default state: Button Tambah -->
                    <button type="button" onclick="addToCart({{ $item->id }}, '{{ addslashes($item->nama_barang) }}', {{ $item->harga_jual }}, {{ $item->stok_saat_ini }})"
                        id="btn-tambah-{{ $item->id }}"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-bold text-xs rounded-xl shadow-md shadow-purple-600/20 transition active:scale-95">
                        Tambah
                    </button>

                    <!-- Active state: (-) Qty (+) counter -->
                    <div id="counter-{{ $item->id }}" class="hidden flex items-center space-x-2 bg-slate-100 rounded-xl p-1 border border-slate-200">
                        <button type="button" onclick="updateQty({{ $item->id }}, -1)" class="w-7 h-7 rounded-lg bg-white text-purple-700 font-extrabold flex items-center justify-center shadow-xs active:scale-95">
                            -
                        </button>
                        <span id="qty-text-{{ $item->id }}" class="text-xs font-bold text-slate-800 px-1">1</span>
                        <button type="button" onclick="updateQty({{ $item->id }}, 1)" class="w-7 h-7 rounded-lg bg-purple-600 text-white font-extrabold flex items-center justify-center shadow-xs active:scale-95">
                            +
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 border border-slate-100 text-center shadow-sm">
                <span class="text-3xl block mb-2">📦</span>
                <p class="text-xs font-bold text-slate-700">Tidak Ada Barang Siap Jual</p>
                <p class="text-[11px] text-slate-400 mt-1">Pastikan barang titipan sudah di-ACC oleh ketua dan stok fisik sudah diinput oleh bendahara.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- STICKY BOTTOM CHECKOUT BAR (Gambar 3.15) -->
<div class="fixed md:sticky bottom-14 md:bottom-2 left-0 right-0 max-w-full md:max-w-5xl mx-auto bg-white/95 backdrop-blur border-t md:border border-slate-200 p-4 z-40 flex items-center justify-between shadow-2xl md:rounded-2xl mt-4">
    <div>
        <span class="text-[10px] font-semibold text-slate-400 block uppercase tracking-wider">Total Pembayaran:</span>
        <span id="total-text" class="text-base font-black text-purple-700">Rp 0</span>
    </div>

    <button type="button" id="btn-bayar" onclick="openCheckoutModal()" disabled
        class="py-2.5 px-6 bg-purple-600 hover:bg-purple-700 disabled:bg-slate-300 disabled:shadow-none text-white font-bold rounded-xl shadow-lg shadow-purple-600/30 transition text-xs flex items-center space-x-1.5 active:scale-95">
        <span>Bayar</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
        </svg>
    </button>
</div>

<!-- MODAL PEMBAYARAN & HITUNG KEMBALIAN KASIR -->
<div id="checkout-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full max-h-[92vh] overflow-y-auto p-5 md:p-6 shadow-2xl animate-fade-in relative border border-slate-100">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-extrabold text-slate-800">Pembayaran Kasir</h3>
                <p class="text-[11px] text-slate-400">Pilih metode pembayaran (Tunai / QRIS)</p>
            </div>
            <button type="button" onclick="closeCheckoutModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold transition">
                ✕
            </button>
        </div>

        <form id="checkout-form" method="POST" action="{{ route('kasir.checkout') }}" class="space-y-3.5">
            @csrf
            <input type="hidden" id="input-items-json" name="items" value="[]">
            <input type="hidden" id="metode_pembayaran" name="metode_pembayaran" value="tunai">

            <!-- Ringkasan Pesanan Singkat -->
            <div id="modal-cart-summary" class="bg-slate-50 p-3 rounded-2xl max-h-28 overflow-y-auto divide-y divide-slate-200/60 text-xs">
            </div>

            <!-- Total Tagihan -->
            <div class="flex justify-between items-center py-2 px-3 bg-purple-50/70 border border-purple-100 rounded-2xl">
                <span class="text-xs font-bold text-slate-600">Total Tagihan:</span>
                <span id="modal-total-tagihan" class="text-base font-black text-purple-700">Rp 0</span>
            </div>

            <!-- TAB PILIHAN METODE PEMBAYARAN -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                    Metode Pembayaran
                </label>
                <div class="flex p-1 bg-slate-100 rounded-2xl border border-slate-200/80 gap-1">
                    <button type="button" id="tab-tunai" onclick="selectPaymentMethod('tunai')"
                        class="flex-1 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 bg-white text-purple-700 shadow-xs border border-purple-200">
                        <span>💵</span>
                        <span>Tunai (Cash)</span>
                    </button>
                    <button type="button" id="tab-qris" onclick="selectPaymentMethod('qris')"
                        class="flex-1 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 text-slate-500 hover:text-slate-800">
                        <span>📱</span>
                        <span>QRIS Dinamis</span>
                        <span class="text-[9px] bg-red-500 text-white font-extrabold px-1.5 py-0.5 rounded-full">Auto</span>
                    </button>
                </div>
            </div>

            <!-- ===================== SECTION 1: TUNAI ===================== -->
            <div id="section-tunai" class="space-y-3">
                <!-- Input Uang Diterima -->
                <div>
                    <label for="bayar" class="block text-xs font-bold text-slate-700 mb-1">
                        Uang Tunai Diterima (Rp)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-xs">Rp</span>
                        <input type="number" id="bayar" name="bayar" required min="0" step="500" onkeyup="hitungKembalian()"
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-black text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500"
                            placeholder="Contoh: 30000">
                    </div>
                </div>

                <!-- Quick Money Buttons -->
                <div class="grid grid-cols-4 gap-1.5 text-xs">
                    <button type="button" onclick="setUangPas()" class="py-2 px-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-[10px] text-center transition">Uang Pas</button>
                    <button type="button" onclick="setUangNominal(20000)" class="py-2 px-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-[10px] text-center transition">20.000</button>
                    <button type="button" onclick="setUangNominal(50000)" class="py-2 px-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-[10px] text-center transition">50.000</button>
                    <button type="button" onclick="setUangNominal(100000)" class="py-2 px-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-[10px] text-center transition">100.000</button>
                </div>

                <!-- Kembalian Info -->
                <div class="bg-purple-50 p-3 rounded-2xl border border-purple-200/60 flex items-center justify-between text-xs">
                    <span class="font-bold text-purple-900">Kembalian:</span>
                    <span id="modal-kembalian-text" class="text-sm font-black text-purple-700">Rp 0</span>
                </div>
            </div>

            <!-- ===================== SECTION 2: QRIS DINAMIS ===================== -->
            <div id="section-qris" class="hidden space-y-3">
                
                <!-- KARTU QRIS RESMI (Standar BI / ASPI) -->
                <div class="bg-gradient-to-b from-white to-slate-50 border-2 border-red-500/80 rounded-2xl p-4 text-center shadow-md relative overflow-hidden">
                    
                    <!-- Header Kartu QRIS -->
                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-2 mb-3">
                        <div class="flex items-center space-x-1">
                            <span class="px-2 py-0.5 bg-red-600 text-white font-black text-xs tracking-wider rounded">QRIS</span>
                            <span class="text-[9px] font-bold text-slate-400">PEMBAYARAN DIGITAL</span>
                        </div>
                        <span class="text-[9px] font-black text-slate-600 tracking-wider bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">GPN</span>
                    </div>

                    <!-- Merchant Info -->
                    <div class="mb-2">
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-tight">{{ $qrisConfig['merchant_name'] }}</h4>
                        <p class="text-[9px] text-slate-400 font-mono mt-0.5">NMID: {{ $qrisConfig['nmid'] }}</p>
                    </div>

                    <!-- Canvas QRIS Generated Dinamis -->
                    <div class="py-2 flex justify-center">
                        <div class="p-2.5 bg-white rounded-2xl shadow-inner border border-slate-200 inline-block">
                            <canvas id="qris-canvas" class="w-48 h-48 mx-auto block rounded-lg"></canvas>
                        </div>
                    </div>

                    <!-- Nominal Terkunci Otomatis -->
                    <div class="mt-2 bg-red-50 border border-red-200/80 rounded-xl py-2 px-3">
                        <p class="text-[9px] text-red-600 font-extrabold uppercase tracking-wider">Nominal Otomatis Diset:</p>
                        <p id="qris-nominal-display" class="text-base font-black text-red-700">Rp 0</p>
                    </div>

                    <!-- Instruksi Pembeli -->
                    <div class="mt-2 text-[10px] text-slate-500 leading-tight">
                        Arahkan kamera / scan dari <strong>m-Banking</strong> (BCA, Mandiri, BRI, BNI) atau <strong>E-Wallet</strong> (GoPay, OVO, ShopeePay, DANA).
                        <span class="text-emerald-700 font-bold block mt-0.5">✓ Nominal tagihan langsung terkunci di HP pembeli</span>
                    </div>
                </div>

                <!-- Info Otoritas QRIS Resmi -->
                <div class="pt-1 text-center">
                    <span class="inline-flex items-center space-x-1.5 text-[10px] text-slate-400 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                        <span>🔒</span>
                        <span>QRIS Resmi disahkan & diatur oleh <strong>Ketua PKK</strong></span>
                    </span>
                </div>

            </div>

            <!-- Tombol Aksi Bawah -->
            <div class="flex items-center space-x-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeCheckoutModal()" class="w-1/3 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition">
                    Batal
                </button>
                <button type="submit" id="btn-submit-checkout" class="flex-1 py-3 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-bold rounded-2xl text-xs shadow-md shadow-purple-600/20 transition active:scale-95">
                    Selesaikan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let cart = {}; // { [id]: { nama, harga, stok, qty } }
    let currentMetode = 'tunai';
    
    // Base Payload QRIS Statis Resmi Organisasi (Diatur & disahkan oleh Ketua PKK)
    const OFFICIAL_STATIC_QRIS = @json($qrisConfig['payload']);

    function filterBarang() {
        const query = document.getElementById('search-input').value.toLowerCase();
        const items = document.querySelectorAll('.barang-item');
        items.forEach(el => {
            const nama = el.getAttribute('data-nama');
            el.style.display = nama.includes(query) ? 'flex' : 'none';
        });
    }

    function addToCart(id, nama, harga, stok) {
        if (!cart[id]) {
            cart[id] = { id, nama, harga, stok, qty: 1 };
        } else {
            if (cart[id].qty < stok) cart[id].qty++;
        }
        renderCartUI();
    }

    function updateQty(id, delta) {
        if (!cart[id]) return;
        const newQty = cart[id].qty + delta;
        if (newQty <= 0) {
            delete cart[id];
        } else if (newQty > cart[id].stok) {
            alert('Stok tidak mencukupi (Maks: ' + cart[id].stok + ')');
            return;
        } else {
            cart[id].qty = newQty;
        }
        renderCartUI();
    }

    function renderCartUI() {
        let grandTotal = 0;
        let totalItems = 0;

        document.querySelectorAll('.barang-item').forEach(el => {
            const id = el.getAttribute('data-id');
            const btnTambah = document.getElementById('btn-tambah-' + id);
            const counter = document.getElementById('counter-' + id);
            const qtyText = document.getElementById('qty-text-' + id);

            if (cart[id] && cart[id].qty > 0) {
                btnTambah.classList.add('hidden');
                counter.classList.remove('hidden');
                qtyText.innerText = cart[id].qty;
                grandTotal += cart[id].harga * cart[id].qty;
                totalItems += cart[id].qty;
            } else {
                btnTambah.classList.remove('hidden');
                counter.classList.add('hidden');
            }
        });

        document.getElementById('total-text').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        document.getElementById('btn-bayar').disabled = totalItems === 0;
    }

    function openCheckoutModal() {
        const itemsArray = Object.values(cart);
        if (itemsArray.length === 0) return;

        let grandTotal = 0;
        let summaryHtml = '';

        itemsArray.forEach(item => {
            const subtotal = item.harga * item.qty;
            grandTotal += subtotal;
            summaryHtml += `
                <div class="py-1.5 flex justify-between items-center">
                    <div>
                        <p class="font-bold text-slate-800">${item.nama}</p>
                        <span class="text-[10px] text-slate-400">${item.qty} x Rp ${item.harga.toLocaleString('id-ID')}</span>
                    </div>
                    <span class="font-bold text-slate-700">Rp ${subtotal.toLocaleString('id-ID')}</span>
                </div>
            `;
        });

        document.getElementById('modal-cart-summary').innerHTML = summaryHtml;
        document.getElementById('modal-total-tagihan').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        document.getElementById('input-items-json').value = JSON.stringify(itemsArray);

        // Reset to tunai by default or keep state
        selectPaymentMethod('tunai');
        document.getElementById('bayar').value = '';
        document.getElementById('modal-kembalian-text').innerText = 'Rp 0';
        document.getElementById('checkout-modal').classList.remove('hidden');
    }

    function closeCheckoutModal() {
        document.getElementById('checkout-modal').classList.add('hidden');
    }

    function selectPaymentMethod(metode) {
        currentMetode = metode;
        document.getElementById('metode_pembayaran').value = metode;

        const tabTunai = document.getElementById('tab-tunai');
        const tabQris = document.getElementById('tab-qris');
        const secTunai = document.getElementById('section-tunai');
        const secQris = document.getElementById('section-qris');
        const btnSubmit = document.getElementById('btn-submit-checkout');
        const inputBayar = document.getElementById('bayar');

        let grandTotal = 0;
        Object.values(cart).forEach(i => grandTotal += (i.harga * i.qty));

        if (metode === 'tunai') {
            tabTunai.className = 'flex-1 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 bg-white text-purple-700 shadow-xs border border-purple-200';
            tabQris.className = 'flex-1 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 text-slate-500 hover:text-slate-800';
            secTunai.classList.remove('hidden');
            secQris.classList.add('hidden');
            btnSubmit.innerText = 'Selesaikan Transaksi (Tunai)';
            btnSubmit.className = 'flex-1 py-3 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-bold rounded-2xl text-xs shadow-md shadow-purple-600/20 transition active:scale-95';
            
            inputBayar.value = '';
            hitungKembalian();
        } else {
            tabQris.className = 'flex-1 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 bg-red-600 text-white shadow-xs font-bold';
            tabTunai.className = 'flex-1 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 text-slate-500 hover:text-slate-800';
            secTunai.classList.add('hidden');
            secQris.classList.remove('hidden');
            btnSubmit.innerText = '✓ Konfirmasi Pembayaran QRIS (Lunas)';
            btnSubmit.className = 'flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-2xl text-xs shadow-md shadow-emerald-600/20 transition active:scale-95';

            // Pada QRIS, bayar otomatis diisi pas dengan total tagihan
            inputBayar.value = grandTotal;
            btnSubmit.disabled = false;

            // Generate & Render QRIS Dinamis
            renderDynamicQRIS(grandTotal);
        }
    }

    function hitungKembalian() {
        if (currentMetode !== 'tunai') return;

        let total = 0;
        Object.values(cart).forEach(i => total += (i.harga * i.qty));
        const bayar = parseFloat(document.getElementById('bayar').value) || 0;
        const kembalian = bayar - total;

        const kembalianElem = document.getElementById('modal-kembalian-text');
        if (kembalian >= 0) {
            kembalianElem.innerText = 'Rp ' + kembalian.toLocaleString('id-ID');
            kembalianElem.className = 'text-sm font-black text-purple-700';
            document.getElementById('btn-submit-checkout').disabled = (bayar < total);
        } else {
            kembalianElem.innerText = 'Kurang Rp ' + Math.abs(kembalian).toLocaleString('id-ID');
            kembalianElem.className = 'text-sm font-black text-rose-600';
            document.getElementById('btn-submit-checkout').disabled = true;
        }
    }

    function setUangPas() {
        let total = 0;
        Object.values(cart).forEach(i => total += (i.harga * i.qty));
        document.getElementById('bayar').value = total;
        hitungKembalian();
    }

    function setUangNominal(val) {
        document.getElementById('bayar').value = val;
        hitungKembalian();
    }

    // ==========================================
    // QRIS DINAMIS ENGINE (EMVCo MPM & CRC16)
    // ==========================================
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
        let qris = (staticQris || DEFAULT_STATIC_QRIS).trim();

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

        // 4. Cari posisi tag 58 (5802ID) dan tag 53 (5303360)
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

    function renderDynamicQRIS(amount) {
        const dynamicPayload = makeDynamicQRIS(OFFICIAL_STATIC_QRIS, amount);

        document.getElementById('qris-nominal-display').innerText = 'Rp ' + amount.toLocaleString('id-ID');

        const canvas = document.getElementById('qris-canvas');
        if (window.QRCode && window.QRCode.toCanvas) {
            window.QRCode.toCanvas(canvas, dynamicPayload, {
                width: 220,
                margin: 1,
                color: {
                    dark: '#0f172a',
                    light: '#ffffff'
                }
            }, function (error) {
                if (error) console.error('Gagal render QRIS:', error);
            });
        }
    }
</script>
@endsection
