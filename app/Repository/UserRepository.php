<?php

namespace App\Repository;

use App\Models\User;

interface UserRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(User $user): object;
    public function update(User $user): object;
    public function delete(User $user): object;
    public function count($criteria = []): int;
}
