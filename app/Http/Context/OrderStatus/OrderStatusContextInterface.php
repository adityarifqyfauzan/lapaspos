<?php

namespace App\Http\Context\OrderStatus;

use Illuminate\Http\Request;

interface OrderStatusContextInterface {
    public function getBy(Request $request);
}
