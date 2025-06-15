<?php

namespace App\Helpers;

class FormatHelper
{
    public static function rupiah($nominal)
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }
}