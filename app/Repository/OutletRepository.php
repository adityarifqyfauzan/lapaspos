<?php

namespace App\Repository;

use App\Models\Outlet;

interface OutletRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(Outlet $outlet): object;
    public function update(Outlet $outlet): object;
    public function delete(Outlet $outlet): object;
    public function count($criteria = []): int;
}
