<?php

namespace App\Repository;

use App\Models\Role;

interface RoleRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(Role $role): object;
    public function update(Role $role): object;
    public function delete(Role $role): object;
    public function count($criteria = []): int;
}
