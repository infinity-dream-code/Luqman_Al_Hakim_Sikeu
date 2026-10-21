<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class scctcust extends Model
{
    protected $connection = "DATA_MYSQL";

    protected $table = 'scctcust';

    protected $primaryKey = 'CUSTID';

    public $timestamps = false;

    public $incrementing = false;

    public static function showVAMTS($nis): string
    {
        return self::formatVA((string) config('app.nova', 751011), $nis);
    }

    public static function showVAMA($nis): string
    {
        return self::formatVA((string) config('app.nova', 751011), $nis);
    }

    /** VA khusus PSB STAI (751033). */
    public static function showVAPSB($nis): string
    {
        return self::formatVA((string) config('app.nova_psb', 751033), $nis);
    }

    public static function showVASpp($nis): string
    {
        return self::showVA($nis);
    }

    public static function showVASaku($nis): string
    {
        return self::showVA($nis);
    }

    public static function showVA($nis): string
    {
        return self::showVAMA($nis);
    }

    public static function formatVA(string $prefix, mixed $nis): string
    {
        $digits = preg_replace('/\D/', '', (string) $nis);
        if ($digits === '' || $digits === '-') {
            return '';
        }

        return $prefix . str_pad($digits, 10, '0', STR_PAD_LEFT);
    }

    public static function nextCustId(): int
    {
        $max = self::query()->max('CUSTID');

        return ((int) $max) + 1;
    }

    protected $guarded = [];
}
