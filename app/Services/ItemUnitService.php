<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\ItemUnit;
use App\Repository\ItemUnitRepository;

class ItemUnitService extends Service implements ItemUnitRepository
{
    public function findBy($criteria = [], $page, $size) {

        $offset = Pagination::getOffset($page, $size);
        $item_units = ItemUnit::where($criteria)->offset($offset)->take($size)->get();
        return $item_units;

    }

    public function findOneBy($criteria = []) {
        $item_unit = ItemUnit::where($criteria)->first();
        return $item_unit;
    }

    public function create(ItemUnit $item_unit): object {

        if ($item_unit->save()) {
            return $this->serviceReturn(true, $item_unit);
        }
        return $this->serviceReturn(false);

    }

    public function update(ItemUnit $item_unit): object {

        if ($item_unit->update()) {
            return $this->serviceReturn(true, $item_unit);
        }
        return $this->serviceReturn(false);

    }

    public function delete(ItemUnit $item_unit): object {

        if ($item_unit->delete()) {
            return $this->serviceReturn();
        }
        return $this->serviceReturn(false);

    }

    public function count($criteria = []): int {
        return ItemUnit::where($criteria)->count();
    }

}
