<?php

namespace App\Repository;

use App\Models\Category;

interface CategoryRepository {
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
    public function create(Category $category): object;
    public function update(Category $category): object;
    public function delete(Category $category): object;
    public function count($criteria = []): int;
}
