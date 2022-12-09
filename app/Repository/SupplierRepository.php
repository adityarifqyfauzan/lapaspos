<?php

namespace App\Repository;

use App\Models\Supplier;

interface SupplierRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(Supplier $supplier): object;
    public function update(Supplier $supplier): object;
    public function delete(Supplier $supplier): object;
    public function count($criteria = []): int;
}
