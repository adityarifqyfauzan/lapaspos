<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\Outlet;
use App\Repository\OutletRepository;
use Illuminate\Support\Arr;

class OutletService extends Service implements OutletRepository
{
    public function findBy($criteria = [], $page, $size) {
        $offset = Pagination::getOffset($page, $size);
        $outlets = Outlet::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $outlets = $outlets->where("name", "LIKE", "%" . $criteria["name"] . "%");
        }

        $outlets = $outlets->take($size)->offset($offset)->get();

        return $outlets;
    }

    public function findOneBy($criteria = []) {
        $outlet = Outlet::where($criteria)->first();
        return $outlet;
    }

    public function create(Outlet $outlet): object {
        if ($outlet->save()) {
            return $this->serviceReturn(true, $outlet);
        }
        return $this->serviceReturn(false);
    }

    public function update(Outlet $outlet): object {
        if ($outlet->update()) {
            return $this->serviceReturn(true, $outlet);
        }
        return $this->serviceReturn(false);
    }

    public function delete(Outlet $outlet): object {
        if ($outlet->delete()) {
            return $this->serviceReturn(true, $outlet);
        }
        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int {
        $outlets = Outlet::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $outlets = $outlets->where("name", "LIKE", "%" . $criteria["name"] . "%");
        }

        $outlets = $outlets->count();
        return $outlets;
    }

}
