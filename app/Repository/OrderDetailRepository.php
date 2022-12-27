<?php

namespace App\Repository;

use App\Models\OrderDetail;

interface OrderDetailRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(OrderDetail $order_detail): object;
    public function update(OrderDetail $order_detail): object;
    public function delete(OrderDetail $order_detail): object;
    public function count($criteria = []): int;
}
