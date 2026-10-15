<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    public const DEFAULT_QRIS_PAYLOAD = '00020101021126680014ID.LINKAJA.WWW01189360091100202688880215ID10202688800010303UMI51450015ID.OR.GPN.WWW0215ID10202688800010303UMI5204541153033605802ID5923PKK KELURAHAN KETINTANG6008SURABAYA61056023162070703A016304';

    public const DEFAULT_MERCHANT_NAME = 'PKK KELURAHAN KETINTANG';

    public const DEFAULT_NMID = 'ID102026888KETINTANG';

    public const DEFAULT_KOTA = 'SURABAYA';

    protected $fillable = [
        'kunci',
        'nilai',
        'keterangan',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $setting = static::where('kunci', $key)->first();

        return $setting ? $setting->nilai : $default;
    }

    public static function setValue(string $key, ?string $value, ?string $keterangan = null): self
    {
        return static::updateOrCreate(
            ['kunci' => $key],
            [
                'nilai' => $value,
                'keterangan' => $keterangan,
            ]
        );
    }

    /**
     * @return array{payload: string, merchant_name: string, nmid: string, kota: string}
     */
    public static function getQrisConfig(): array
    {
        return [
            'payload' => static::getValue('qris_payload', self::DEFAULT_QRIS_PAYLOAD) ?: self::DEFAULT_QRIS_PAYLOAD,
            'merchant_name' => static::getValue('qris_merchant_name', self::DEFAULT_MERCHANT_NAME) ?: self::DEFAULT_MERCHANT_NAME,
            'nmid' => static::getValue('qris_nmid', self::DEFAULT_NMID) ?: self::DEFAULT_NMID,
            'kota' => static::getValue('qris_kota', self::DEFAULT_KOTA) ?: self::DEFAULT_KOTA,
        ];
    }
}
