<?php

namespace App\Services;

use App\Models\Payment;
use App\Repository\PaymentRepository;

class PaymentService extends Service implements PaymentRepository
{
    public function findOneBy($criteria = []) {

        $payment = Payment::with('payment_status:id,name', 'payment_method:id,name')->where($criteria)->first();

        return $payment;

    }

    public function create(Payment $payment) {
        if ($payment->save()) {
            return $this->serviceReturn(true, $payment);
        }
        return $this->serviceReturn(false);
    }

    public function update(Payment $payment) {
        if ($payment->update()) {
            return $this->serviceReturn(true, $payment);
        }
        return $this->serviceReturn(false);
    }

}
