<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';

    protected $fillable = [
        'user_id',
        'kategori_barang_id',
        'kode_barang',
        'nama_barang',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok_saat_ini',
        'status',
        'catatan',
        'foto_barang',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'stok_saat_ini' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id');
    }

    public function mutasiStoks(): HasMany
    {
        return $this->hasMany(MutasiStok::class, 'barang_id');
    }

    public function penjualanDetails(): HasMany
    {
        return $this->hasMany(TransaksiPenjualanDetail::class, 'barang_id');
    }
}
