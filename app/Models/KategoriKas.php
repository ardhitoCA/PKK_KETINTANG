<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriKas extends Model
{
    use HasFactory;

    protected $table = 'kategori_kas';

    protected $fillable = [
        'nama_kategori',
        'jenis',
    ];

    public function transaksiKas(): HasMany
    {
        return $this->hasMany(TransaksiKas::class, 'kategori_kas_id');
    }
}
