<?php

namespace App\Http\Context\Payment;

use Illuminate\Http\Request;

interface PaymentContextInterface {
    public function store(Request $request);
}
