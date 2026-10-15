<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PencairanAnggota extends Model
{
    use HasFactory;

    protected $table = 'pencairan_anggotas';

    protected $fillable = [
        'user_id',
        'bendahara_id',
        'tanggal',
        'total_pencairan',
        'status',
        'keterangan',
        'bukti_transfer',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total_pencairan' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bendahara(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bendahara_id');
    }
}
