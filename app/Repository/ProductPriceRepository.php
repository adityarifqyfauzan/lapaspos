<?php

namespace App\Repository;

use App\Models\ProductPrice;

interface ProductPriceRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(ProductPrice $product_price): object;
    public function update(ProductPrice $product_price): object;
    public function delete(ProductPrice $product_price): object;
    public function count($criteria = []): int;
}
