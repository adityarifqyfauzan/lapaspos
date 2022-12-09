<?php

namespace App\Repository;

interface SettingRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
}
