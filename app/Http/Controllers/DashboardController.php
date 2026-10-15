<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\TransaksiKas;
use App\Models\TransaksiPenjualan;
use App\Models\TransaksiPenjualanDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        // 7 days omset calculation (Senin - Minggu or last 7 days)
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::today()->subDays($i));
        }

        $omset7Hari = $dates->map(function ($date) use ($user) {
            $formattedDate = $date->format('Y-m-d');
            $dayName = $date->locale('id')->isoFormat('ddd');

            if ($user->role === 'anggota') {
                $nominal = TransaksiPenjualanDetail::whereHas('barang', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                    ->whereHas('transaksi', function ($query) use ($formattedDate) {
                        $query->whereDate('tanggal', $formattedDate);
                    })
                    ->sum('hak_anggota');
            } else {
                $nominal = TransaksiPenjualan::whereDate('tanggal', $formattedDate)
                    ->sum('total_nominal');
            }

            return [
                'day' => $dayName,
                'date' => $formattedDate,
                'nominal' => (float) $nominal,
            ];
        });

        $maxOmset = $omset7Hari->max('nominal') ?: 10000;

        // Specific metrics according to roles
        $pendingCount = Barang::where('status', 'pending')->count();
        $totalBarangAktif = Barang::where('status', 'aktif')->count();
        $totalStok = Barang::where('status', 'aktif')->sum('stok_saat_ini');

        $saldoKas = TransaksiKas::whereHas('kategori', function ($q) {
            $q->where('jenis', 'masuk');
        })->sum('nominal') - TransaksiKas::whereHas('kategori', function ($q) {
            $q->where('jenis', 'keluar');
        })->sum('nominal');

        // For Anggota:
        $myBarangsCount = 0;
        $myTerjualCount = 0;
        $myHakUang = 0;
        if ($user->role === 'anggota') {
            $myBarangsCount = Barang::where('user_id', $user->id)->count();
            $myTerjualCount = TransaksiPenjualanDetail::whereHas('barang', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->sum('jumlah');
            $myHakUang = TransaksiPenjualanDetail::whereHas('barang', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->sum('hak_anggota');
        }

        return view('dashboard.index', compact(
            'user',
            'omset7Hari',
            'maxOmset',
            'pendingCount',
            'totalBarangAktif',
            'totalStok',
            'saldoKas',
            'myBarangsCount',
            'myTerjualCount',
            'myHakUang'
        ));
    }
}
