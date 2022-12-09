<?php

namespace App\Helper;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Generator
{
    /**
    * Generate virtual product code
    * @return string
    */
    public static function virtualProductCode()
    {
        return config('constants.code.product') . strtoupper(Str::random(7));
    }

    /**
     * Generate transaction code
     * @return string
     */
    public static function tansactionCode($count)
    {

        $invoice = DB::table('payments')->select(DB::raw("SELECT MAX(MID(invoice, 4, 4)) as current_invoice"))->where('created_at', now())->get();
        // TRX000128112022
        return config('constants.code.transaction') . $count . date('dmY');
    }

}
