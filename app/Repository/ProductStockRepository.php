<?php

namespace App\Repository;

use App\Models\ProductStock;

interface ProductStockRepository
{
    public function findBy($criteria = []);
    public function findPagedBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(ProductStock $product_stock): object;
    public function update(ProductStock $product_stock): object;
    public function delete(ProductStock $product_stock): object;
    public function count($criteria = []): int;
}
