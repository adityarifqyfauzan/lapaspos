<?php

namespace App\Helper;
use Illuminate\Support\Str;

class Generator
{
    /**
    * Generate virtual product code
    * @return string
    */
    public static function virtualProductCode()
    {
        return config('constants.code.transaction') . strtoupper(Str::random(7));
    }

    /**
     * Generate transaction code
     * @return string
     */
    public static function tansactionCode()
    {
        return config('constants.code.transaction') . strtoupper(Str::random(16));
    }

}
