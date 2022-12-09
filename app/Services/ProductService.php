<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\Product;
use App\Repository\ProductRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductService extends Service implements ProductRepository
{
    public function findBy($criteria = [], $page, $size) {

        $offset = Pagination::getOffset($page, $size);
        $products = Product::with('categories:id,name', 'product_prices')->where(Arr::except($criteria, ["name", "category_id"]));

        if (Arr::exists($criteria, "name")) {
            $products = $products->where("name", "like", "%". $criteria["name"] . "%");
        }

        if (Arr::exists($criteria, "category_id")) {
            $products = $products->whereHas('categories', function ($q) use ($criteria)
            {
                $q->whereIn('category_id', (array) $criteria["category_id"]);
            });
        }

        $products = $products->offset($offset)->take($size)->orderBy('name')->get();

        return $products;

    }

    public function findOneBy($criteria = []) {

        $product = Product::with('categories:id,name')->where(Arr::except($criteria, ["name", "category_id"]));

        if (Arr::exists($criteria, "name")) {
            $product = $product->where("name", "like", "%". $criteria["name"] . "%");
        }

        if (Arr::exists($criteria, "category_id")) {
            $product = $product->whereHas('categories', function ($q) use ($criteria)
            {
                $q->whereIn('category_id', (array) $criteria["category_id"]);
            });
        }

        $product = $product->first();

        return $product;

    }

    public function create(Product $product): object {
        if($product->save()){
            return $this->serviceReturn(true, $product);
        }

        return $this->serviceReturn(false);
    }

    public function update(Product $product): object {
        if ($product->update()) {
            return $this->serviceReturn(true, $product);
        }
        return $this->serviceReturn(false);
    }

    public function delete(Product $product): object {

        if ($product->delete()) {
            return $this->serviceReturn();
        }
        return $this->serviceReturn(false);

    }

    public function count($criteria = []): int {
        return Product::where($criteria)->count();
    }
}
