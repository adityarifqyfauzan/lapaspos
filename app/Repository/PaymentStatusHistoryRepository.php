<?php

namespace App\Repository;

use App\Models\OrderStatusHistory;
use App\Models\PaymentStatusHistory;

interface PaymentStatusHistoryRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(PaymentStatusHistory $payment_status_history): object;
    public function update(PaymentStatusHistory $payment_status_history): object;
    public function delete(PaymentStatusHistory $payment_status_history): object;
    public function count($criteria = []): int;
}
