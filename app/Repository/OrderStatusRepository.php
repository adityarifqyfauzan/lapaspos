<?php

namespace App\Repository;

use App\Models\OrderStatus;

interface OrderStatusRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(OrderStatus $order_status): object;
    public function update(OrderStatus $order_status): object;
    public function delete(OrderStatus $order_status): object;
    public function count($criteria = []): int;
}
