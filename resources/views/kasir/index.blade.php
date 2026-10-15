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
    <div id="barang-list" class="space-y-2.5">
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
<div class="fixed md:absolute bottom-14 left-0 right-0 max-w-md mx-auto bg-white/95 backdrop-blur border-t border-slate-200 p-3.5 z-40 flex items-center justify-between shadow-2xl">
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
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl animate-fade-in relative border border-slate-100">
        <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-extrabold text-slate-800">Pembayaran Kasir</h3>
                <p class="text-[11px] text-slate-400">Pastikan uang yang diterima sesuai</p>
            </div>
            <button type="button" onclick="closeCheckoutModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">✕</button>
        </div>

        <form id="checkout-form" method="POST" action="{{ route('kasir.checkout') }}" class="space-y-3.5">
            @csrf
            <input type="hidden" id="input-items-json" name="items" value="[]">

            <!-- Ringkasan Pesanan Singkat -->
            <div id="modal-cart-summary" class="bg-slate-50 p-3 rounded-2xl max-h-36 overflow-y-auto divide-y divide-slate-200/60 text-xs">
            </div>

            <div class="flex justify-between items-center py-1">
                <span class="text-xs font-bold text-slate-500">Total Tagihan:</span>
                <span id="modal-total-tagihan" class="text-sm font-black text-purple-700">Rp 0</span>
            </div>

            <!-- Input Uang Diterima -->
            <div>
                <label for="bayar" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">
                    Uang Diterima (Rp)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" id="bayar" name="bayar" required min="0" step="500" onkeyup="hitungKembalian()"
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-black text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="Contoh: 20000">
                </div>
            </div>

            <!-- Quick Money Buttons -->
            <div class="grid grid-cols-3 gap-1.5 text-xs">
                <button type="button" onclick="setUangPas()" class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-[10px]">Uang Pas</button>
                <button type="button" onclick="setUangNominal(20000)" class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-[10px]">20.000</button>
                <button type="button" onclick="setUangNominal(50000)" class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-[10px]">50.000</button>
            </div>

            <!-- Kembalian Info -->
            <div class="bg-purple-50 p-3 rounded-2xl border border-purple-200/60 flex items-center justify-between text-xs">
                <span class="font-bold text-purple-900">Kembalian:</span>
                <span id="modal-kembalian-text" class="text-sm font-black text-purple-700">Rp 0</span>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <button type="button" onclick="closeCheckoutModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition">
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

        // Reset all item buttons
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

        // Reset bayar input
        document.getElementById('bayar').value = '';
        document.getElementById('modal-kembalian-text').innerText = 'Rp 0';
        document.getElementById('checkout-modal').classList.remove('hidden');
    }

    function closeCheckoutModal() {
        document.getElementById('checkout-modal').classList.add('hidden');
    }

    function hitungKembalian() {
        let total = 0;
        Object.values(cart).forEach(i => total += (i.harga * i.qty));
        const bayar = parseFloat(document.getElementById('bayar').value) || 0;
        const kembalian = bayar - total;

        const kembalianElem = document.getElementById('modal-kembalian-text');
        if (kembalian >= 0) {
            kembalianElem.innerText = 'Rp ' + kembalian.toLocaleString('id-ID');
            kembalianElem.className = 'text-sm font-black text-purple-700';
            document.getElementById('btn-submit-checkout').disabled = false;
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
</script>
@endsection
