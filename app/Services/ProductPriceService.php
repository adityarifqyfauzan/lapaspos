<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\ProductPrice;
use App\Repository\ProductPriceRepository;

class ProductPriceService extends Service implements ProductPriceRepository
{
    public function findBy($criteria = [], $page, $size) {
        $offset = Pagination::getOffset($page, $size);

        $product_prices = ProductPrice::where($criteria)->orderBy('id', 'desc')->limit($size)->offset($offset)->get();

        return $product_prices;

    }

    public function findOneBy($criteria = []) {

        $product_price = ProductPrice::where($criteria)->orderBy('id', 'desc')->first();

        return $product_price;
    }

    public function create(ProductPrice $product_price): object {
        if ($product_price->save()) {
            return $this->serviceReturn(true, $product_price);
        }
        return $this->serviceReturn(false);
    }

    public function update(ProductPrice $product_price): object {
        if ($product_price->update()) {
            return $this->serviceReturn(true, $product_price);
        }
        return $this->serviceReturn(false);
    }

    public function delete(ProductPrice $product_price): object {
        if ($product_price->delete()) {
            return $this->serviceReturn();
        }
        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int {
        return ProductPrice::where($criteria)->count();
    }

}
