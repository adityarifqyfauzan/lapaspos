<?php

namespace App\Repository;

use App\Models\OrderStatusHistory;

interface OrderStatusHistoryRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(OrderStatusHistory $order_status_history): object;
    public function update(OrderStatusHistory $order_status_history): object;
    public function delete(OrderStatusHistory $order_status_history): object;
    public function count($criteria = []): int;
}
