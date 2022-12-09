<?php

namespace App\Repository;

use App\Models\PaymentMethod;

interface PaymentMethodRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(PaymentMethod $payment_method): object;
    public function update(PaymentMethod $payment_method): object;
    public function delete(PaymentMethod $payment_method): object;
    public function count($criteria = []): int;
}
