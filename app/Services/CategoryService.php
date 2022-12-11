<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\Category;
use App\Repository\CategoryRepository;
use Illuminate\Support\Facades\DB;

class CategoryService extends Service implements CategoryRepository
{
    public function findBy($criteria = [], $page, $size) {

        $offset = Pagination::getOffset($page, $size);
        $categories = Category::where($criteria)->offset($offset)->take($size)->get();
        return $categories;

    }

    public function findOneBy($criteria = []) {

        $category = Category::where($criteria)->first();
        if ($category) {
            return $this->serviceReturn(true, $category);
        }
        return $this->serviceReturn(false);

    }

    public function create(Category $category): object {

        if ($category->save()) {
            return $this->serviceReturn(true, $category);
        }
        return $this->serviceReturn(false);

    }

    public function update(Category $category): object {
        if ($category->update()) {
            return $this->serviceReturn(true, $category);
        }
        return $this->serviceReturn(false);
    }

    public function delete(Category $category): object {
        if ($category->delete()) {
            return $this->serviceReturn();
        }
        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int {
        return Category::where($criteria)->count();
    }

}
