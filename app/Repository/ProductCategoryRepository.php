<?php

namespace App\Repository;

use App\Models\ProductCategory;

interface ProductCategoryRepository {
    public function findBy($criteria = []);
    public function findOneBy($criteria = []);
    public function create(ProductCategory $product_category): object;
    public function update(ProductCategory $product_category): object;
    public function delete(ProductCategory $product_category): object;
    public function count($criteria = []): int;
}
