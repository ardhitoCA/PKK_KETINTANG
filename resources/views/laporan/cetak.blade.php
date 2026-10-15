<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan & Inventaris PKK Kelurahan Ketintang</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1e293b;
            background: #f8fafc;
            padding: 20px;
            font-size: 12px;
            line-height: 1.5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 13px;
            font-weight: 600;
            margin: 4px 0;
            color: #15803d;
        }
        .header p {
            font-size: 10px;
            color: #64748b;
            margin: 0;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }
        .summary-card {
            border: 1px solid #cbd5e1;
            padding: 12px;
            border-radius: 6px;
            background: #f8fafc;
        }
        .summary-card span {
            font-size: 11px;
            color: #64748b;
            display: block;
        }
        .summary-card strong {
            font-size: 15px;
            color: #0f172a;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        table th, table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        table th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #334155;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sign-box {
            text-align: center;
            width: 200px;
        }
        .sign-space {
            height: 60px;
        }
        .no-print {
            text-align: center;
            margin-bottom: 16px;
        }
        .btn-print {
            background: #16a34a;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan sebagai PDF</button>
    </div>

    <div class="container">
        <!-- Letterhead Kop Surat -->
        <div class="header">
            <h1>Pemberdayaan Kesejahteraan Keluarga (PKK)</h1>
            <h2>Kelurahan Ketintang - Kecamatan Gayungan - Kota Surabaya</h2>
            <p>Laporan Pertanggungjawaban Keuangan & Inventaris Usaha Mandiri</p>
            <p style="font-weight: bold; margin-top: 4px; color: #0f172a;">Periode Cetak: {{ now()->locale('id')->isoFormat('D MMMM Y') }}</p>
        </div>

        <!-- Ringkasan Finansial -->
        <h3 style="font-size: 12px; margin-bottom: 8px; text-transform: uppercase;">I. Ringkasan Finansial Usaha</h3>
        <div class="summary-grid">
            <div class="summary-card">
                <span>Total Penjualan Kasir UMKM</span>
                <strong>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</strong>
            </div>
            <div class="summary-card">
                <span>Laba Bersih Margin PKK</span>
                <strong style="color: #15803d;">Rp {{ number_format($labaUsahaPkk, 0, ',', '.') }}</strong>
            </div>
            <div class="summary-card">
                <span>Pemasukan Kas Non-Penjualan</span>
                <strong>+Rp {{ number_format($kasMasukLain, 0, ',', '.') }}</strong>
            </div>
            <div class="summary-card">
                <span>Pengeluaran Kas Operasional</span>
                <strong style="color: #b91c1c;">-Rp {{ number_format($kasKeluar, 0, ',', '.') }}</strong>
            </div>
            <div class="summary-card" style="grid-column: span 2; background: #ecfdf5; border-color: #a7f3d0;">
                <span style="color: #065f46; font-weight: bold;">SALDO AKHIR KAS USAHA PKK</span>
                <strong style="color: #047857; font-size: 18px;">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</strong>
            </div>
        </div>

        <!-- Tabel Riwayat Penjualan -->
        <h3 style="font-size: 12px; margin-bottom: 8px; text-transform: uppercase;">II. Riwayat Penjualan Kasir Konsinyasi</h3>
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Produk Terjual</th>
                    <th class="text-right">Total (Rp)</th>
                    <th class="text-right">Laba PKK (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPenjualan as $idx => $trx)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td style="font-family: monospace; font-weight: bold;">{{ $trx->kode_transaksi }}</td>
                        <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}</td>
                        <td>
                            @foreach ($trx->details as $d)
                                {{ $d->barang->nama_barang }} ({{ $d->jumlah }} {{ $d->barang->satuan }}){{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </td>
                        <td class="text-right">{{ number_format($trx->total_nominal, 0, ',', '.') }}</td>
                        <td class="text-right" style="font-weight: bold; color: #15803d;">{{ number_format($trx->details->sum('laba_umkm'), 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="color: #94a3b8;">Belum ada riwayat penjualan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tabel Mutasi Kas -->
        <h3 style="font-size: 12px; margin-bottom: 8px; text-transform: uppercase;">III. Arus Kas Masuk & Keluar</h3>
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>Tanggal</th>
                    <th>Kategori Kas</th>
                    <th>Keterangan</th>
                    <th class="text-right">Masuk (Rp)</th>
                    <th class="text-right">Keluar (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarKas as $idx => $kas)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($kas->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $kas->kategori->nama_kategori }}</td>
                        <td>{{ $kas->keterangan }}</td>
                        <td class="text-right" style="color: #15803d; font-weight: bold;">
                            {{ $kas->kategori->jenis === 'masuk' ? number_format($kas->nominal, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-right" style="color: #b91c1c; font-weight: bold;">
                            {{ $kas->kategori->jenis === 'keluar' ? number_format($kas->nominal, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="color: #94a3b8;">Belum ada riwayat kas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan Pengurus -->
        <div class="signatures">
            <div class="sign-box">
                <p>Mengetahui,<br><strong>Ketua PKK Kelurahan Ketintang</strong></p>
                <div class="sign-space"></div>
                <p><u>( Ibu Hj. Aminah )</u></p>
            </div>

            <div class="sign-box">
                <p>Surabaya, {{ now()->locale('id')->isoFormat('D MMMM Y') }}<br><strong>Bendahara Usaha PKK</strong></p>
                <div class="sign-space"></div>
                <p><u>( Ibu Rahmawati )</u></p>
            </div>
        </div>
    </div>

</body>
</html>
