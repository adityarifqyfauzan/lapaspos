<?php

namespace App\Repository;

use App\Models\ItemUnit;

interface ItemUnitRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(ItemUnit $item_unit): object;
    public function update(ItemUnit $item_unit): object;
    public function delete(ItemUnit $item_unit): object;
    public function count($criteria = []): int;
}
