<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPenjualanDetail extends Model
{
    use HasFactory;

    protected $table = 'transaksi_penjualan_details';

    protected $fillable = [
        'transaksi_penjualan_id',
        'barang_id',
        'harga_beli',
        'harga_jual',
        'jumlah',
        'subtotal',
        'laba_umkm',
        'hak_anggota',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'jumlah' => 'integer',
            'subtotal' => 'decimal:2',
            'laba_umkm' => 'decimal:2',
            'hak_anggota' => 'decimal:2',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiPenjualan::class, 'transaksi_penjualan_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
