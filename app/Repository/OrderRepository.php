<?php

namespace App\Repository;

use App\Models\Order;

interface OrderRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(Order $order): object;
    public function update(Order $order): object;
    public function delete(Order $order): object;
    public function count($criteria = []): int;
    public function sumQty($order_id): int;
    public function sumTotal($order_id): int;
}
