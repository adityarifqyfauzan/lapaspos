<?php

namespace App\Repository;

use App\Models\Activity;

interface ActivityRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(Activity $activity): object;
    public function count($criteria = []): int;
}
