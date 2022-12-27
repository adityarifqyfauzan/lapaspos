<?php

namespace App\Repository;

use App\Models\Payment;

interface PaymentRepository {
    public function findOneBy($criteria = []);
    public function create(Payment $payment);
    public function update(Payment $payment);
}
