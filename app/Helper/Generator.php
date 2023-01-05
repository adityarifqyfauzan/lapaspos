<?php

namespace App\Helper;

use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
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
    public static function tansactionCode(int $outlet_id): string
    {
        $order_count = 1;

        // get order count today
        $orders = Order::where('outlet_id', $outlet_id)->whereDate('created_at', Carbon::today())->count();
        if ($orders != 0) {
            $order_count = $orders + 1;
        }

        $count = str_pad($order_count, 4, '0', STR_PAD_LEFT);
        $outlet_code = str_pad($outlet_id, 3, '0', STR_PAD_LEFT);

        return config('constants.code.transaction') . $count . date('dmY') . $outlet_code;
    }

    /**
     * Generate invoice code
     * @return string
     */
    public static function invoiceCode(int $outlet_id)
    {
        $payment_count = 1;

        // get payment count today
        $payments = Payment::whereDate('created_at', Carbon::today())->count();
        if ($payments != 0) {
            $payment_count = $payments + 1;
        }

        $count = str_pad($payment_count, 4, '0', STR_PAD_LEFT);
        $outlet_code = str_pad($outlet_id, 3, '0', STR_PAD_LEFT);

        return config('constants.code.invoice') . $count . date('dmY') . $outlet_code;
    }

}
