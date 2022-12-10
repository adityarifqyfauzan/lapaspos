<?php

namespace App\Repository;

use App\Models\LoginHistory;

interface LoginHistoryRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(LoginHistory $login_history): object;
}
