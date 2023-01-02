<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\ItemUnit;
use App\Repository\ItemUnitRepository;
use Illuminate\Support\Arr;

class ItemUnitService extends Service implements ItemUnitRepository
{
    public function findBy($criteria = [], $page, $size) {

        $offset = Pagination::getOffset($page, $size);
        $item_units = ItemUnit::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $item_units = $item_units->where("name", "like", "%". $criteria["name"] . "%");
        }

        $item_units = $item_units->offset($offset)->take($size)->get();
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
        $item_units = ItemUnit::where(Arr::except($criteria, ["name"]));
        if (Arr::exists($criteria, "name")) {
            $item_units = $item_units->where("name", "like", "%". $criteria["name"] . "%");
        }
        $item_units = $item_units->count();
        return $item_units;
    }

}
